<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $sales = Sale::with(['customer', 'saleItems'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('customer', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales', 'search'));
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
        // Safe loading (doesn't throw if relationships are null)
        $sale->load(['customer', 'saleItems.product']);

        return view('sales.show', compact('sale'));
    }

    public function invoice(Sale $sale)
    {
        $sale->load(['customer', 'saleItems.product']);

        return view('sales.invoice', compact('sale'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'notes'     => 'nullable|string',
            'items'     => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        // 1. Fetch Customer info snapshot if customer selected
        $customer = $validated['customer_id'] ? Customer::find($validated['customer_id']) : null;

        // 2. Fetch Product info snapshots
        $itemsToCreate = [];
        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);

            $itemsToCreate[] = [
                'product_id'    => $product->id,
                'product_name'   => $product->name,
                'unit_price' => $product->price,
                'quantity'      => $item['quantity'],
                'subtotal'    => $product->price * $item['quantity'],
            ];
        }

        // 3. Store inside Database Transaction
        DB::transaction(function () use ($validated, $customer, $itemsToCreate) {
        $sale = Sale::create([
            'customer_id'        => $customer?->id,
            // Save actual customer's name snapshot if present, otherwise default to 'Walk-in Customer'
            'customer_name'       => $customer ? $customer->name : 'Walk-in Customer',
            'customer_phone' => $customer?->phone,
            'customer_address'   => $customer?->address,
            'sale_date'       => now(),
            'total'            => array_sum(array_column($itemsToCreate, 'subtotal')),
            'status'           => 'completed',
            'notes'            => $validated['notes'] ?? null,
        ]);

        foreach ($itemsToCreate as $item) {
            Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
            $sale->saleItems()->create($item);
        }
    });

        return redirect()->route('sales.index')
            ->with('success', 'Sale saved successfully.');
    }
    public function receipt(Sale $sale)
    {
        $sale->load(['customer', 'saleItems.product']);
        return view('sales.receipt', compact('sale'));
    }

    /**
     * Cancel a completed sale and safely restore stock if products still exist.
     */
    public function cancel(Sale $sale)
    {
        // Prevent cancelling twice
        if ($sale->status === 'cancelled') {
            return back()->with('error', 'This sale is already cancelled.');
        }

        foreach ($sale->saleItems as $item) {
            if ($item->product_id) {
                $product = Product::find($item->product_id);

                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }

        $sale->update([
            'status' => 'cancelled',
        ]);

        return redirect()
            ->route('sales.show', $sale)
            ->with('success', 'Sale cancelled and stock restored successfully.');
    }
}