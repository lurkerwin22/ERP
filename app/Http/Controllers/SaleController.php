<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StockMovement;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index(Request $request)
    {
        $query = Sale::query()->with(['customer', 'payments']);

        // 1. Text Search (Sale Ref ID or Customer Name)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                ->orWhere('customer_name', 'like', "%{$search}%")
                ->orWhereHas('customer', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%");
                });
            });
        }

        // 2. Sale Status Filter
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // 3. Payment Status Filter
        if ($paymentStatus = $request->input('payment_status')) {
            if ($paymentStatus === 'paid') {
                $query->whereRaw('(total - (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.sale_id = sales.id)) <= 0')
                    ->where('status', '!=', 'cancelled');
            } elseif ($paymentStatus === 'partial') {
                $query->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.sale_id = sales.id) > 0')
                    ->whereRaw('(total - (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.sale_id = sales.id)) > 0')
                    ->where('status', '!=', 'cancelled');
            } elseif ($paymentStatus === 'unpaid') {
                $query->whereRaw('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.sale_id = sales.id) = 0')
                    ->where('status', '!=', 'cancelled');
            }
        }

        // 4. Customer Filter
        if ($customerId = $request->input('customer_id')) {
            if ($customerId !== 'all') {
                $query->where('customer_id', $customerId);
            }
        }

        // 5. Date Range Filter
        if ($fromDate = $request->input('from_date')) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate = $request->input('to_date')) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        // 6. Total Range Filter
        if ($request->filled('min_total')) {
            $query->where('total', '>=', $request->input('min_total'));
        }

        if ($request->filled('max_total')) {
            $query->where('total', '<=', $request->input('max_total'));
        }

        // 7. Sorting
        switch ($request->input('sort', 'newest')) {
            case 'oldest':
                $query->oldest();
                break;
            case 'total_low_high':
                $query->orderBy('total', 'asc');
                break;
            case 'total_high_low':
                $query->orderBy('total', 'desc');
                break;
            case 'id_asc':
                $query->orderBy('id', 'asc');
                break;
            case 'id_desc':
                $query->orderBy('id', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        // Compute stats for KPI cards
        $allSales = Sale::where('status', '!=', 'cancelled')->with('payments')->get();

        $stats = [
            'total_sales' => $allSales->count(),
            'paid_sales' => $allSales->filter(fn ($s) => $s->remaining_balance <= 0)->count(),
            'unpaid_sales' => $allSales->filter(fn ($s) => $s->remaining_balance > 0)->count(),
            'outstanding_amount' => round($allSales->sum(fn ($s) => $s->remaining_balance), 2),
        ];

        $sales = $query->paginate(15)->appends($request->query());
        $customers = Customer::orderBy('name')->get();

        // Pass $stats in compact
        return view('sales.index', compact('sales', 'customers', 'stats'));
    }

    /**
     * Show the form for creating a new sale ().
     */
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        // Only load products that have available stock
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();

        return view('sales.create', compact('customers', 'products'));
    }
    public function show(Sale $sale)
    {
        // Eager load customer, items, products, and payment history
        $sale->load(['customer', 'saleItems.product', 'payments']);

        return view('sales.show', compact('sale'));
    }

    public function invoice(Sale $sale)
    {
        $sale->load(['customer', 'saleItems.product', 'payments']);

        return view('sales.invoice', compact('sale'));
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['customer', 'saleItems.product', 'payments']);

        return view('sales.receipt', compact('sale'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $customer = !empty($validated['customer_id'])
                    ? Customer::findOrFail($validated['customer_id'])
                    : null;

                // Aggregate duplicate product lines before checking stock.
                $requestedQuantities = [];
                foreach ($validated['items'] as $item) {
                    $requestedQuantities[$item['product_id']] = ($requestedQuantities[$item['product_id']] ?? 0) + $item['quantity'];
                }

                foreach ($requestedQuantities as $productId => $quantity) {
                    $product = Product::whereKey($productId)->lockForUpdate()->firstOrFail();
                    if ($product->stock < $quantity) {
                        throw new \RuntimeException("Insufficient stock for '{$product->name}'. Available: {$product->stock}, Required: {$quantity}.");
                    }
                }

                $itemsToCreate = [];
                foreach ($validated['items'] as $item) {
                    $product = Product::whereKey($item['product_id'])->lockForUpdate()->firstOrFail();
                    $itemsToCreate[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'unit_price' => $product->price,
                        'quantity' => $item['quantity'],
                        'subtotal' => $product->price * $item['quantity'],
                    ];
                }

                $sale = Sale::create([
                    'user_id' => auth()->id(),
                    'customer_id' => $customer?->id,
                    'customer_name' => $customer ? $customer->name : 'Walk-in Customer',
                    'customer_phone' => $customer?->phone,
                    'customer_address' => $customer?->address,
                    'sale_date' => now(),
                    'total' => array_sum(array_column($itemsToCreate, 'subtotal')),
                    'status' => 'completed',
                    'notes' => $validated['notes'] ?? null,
                ]);

                foreach ($itemsToCreate as $item) {
                    $product = Product::whereKey($item['product_id'])->lockForUpdate()->firstOrFail();
                    $product->decrement('stock', $item['quantity']);
                    $sale->saleItems()->create($item);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'out',
                        'quantity' => $item['quantity'],
                        'reason' => "Sale #{$sale->id}",
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('sales.index')->with('success', 'Sale saved successfully.');
    }

    /**
     * Cancel a completed sale and safely restore stock if products still exist.
     */
    public function cancel(Sale $sale)
    {
        if ($sale->status === 'cancelled') {
            return back()->with('error', 'This sale is already cancelled.');
        }

        try {
            DB::transaction(function () use ($sale) {
                $sale->load('saleItems');

                foreach ($sale->saleItems as $item) {
                    if (!$item->product_id) {
                        continue;
                    }

                    $product = Product::whereKey($item->product_id)->lockForUpdate()->first();
                    if (!$product) {
                        continue;
                    }

                    $product->increment('stock', $item->quantity);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'in',
                        'quantity' => $item->quantity,
                        'reason' => "Cancellation of Sale #{$sale->id}",
                    ]);
                }

                $sale->update(['status' => 'cancelled']);
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Unable to cancel the sale: ' . $e->getMessage());
        }

        return redirect()->route('sales.show', $sale)->with('success', 'Sale cancelled and stock restored successfully.');
    }

}
