<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::with(['customer', 'sale'])->latest()->paginate(10);
        return view('quotes.index', compact('quotes'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        return view('quotes.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Generate unique quote number (DEV-000001)
            $nextId = (Quote::max('id') ?? 0) + 1;
            $quoteNumber = 'DEV-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

            $total = 0;
            foreach ($validated['items'] as $item) {
                $total += $item['quantity'] * $item['unit_price'];
            }

            $quote = Quote::create([
                'customer_id' => $validated['customer_id'],
                'user_id' => auth()->id(),
                'quote_number' => $quoteNumber,
                'date' => $validated['date'],
                'status' => 'draft',
                'total' => $total,
            ]);

            foreach ($validated['items'] as $item) {
                $quote->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);
            }
        });

        return redirect()->route('quotes.index')->with('success', 'Quote created successfully!');
    }

    public function show(Quote $quote)
    {
        $quote->load(['customer', 'items.product', 'sale']);
        return view('quotes.show', compact('quote'));
    }

    public function updateStatus(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,sent,accepted,rejected',
        ]);

        $quote->update(['status' => $validated['status']]);

        return back()->with('success', 'Quote status updated to ' . ucfirst($quote->status));
    }

    public function convert(Quote $quote)
    {
        // 1. Prevent duplicate conversions
        if ($quote->isConverted() || $quote->status === 'accepted') {
            return back()->with('error', 'This quote has already been accepted and converted into a sale.');
        }

        $quote->load(['items.product', 'customer']);

        // 2. Stock Check
        foreach ($quote->items as $item) {
            $product = $item->product;
            if ($product && $product->stock < $item->quantity) {
                return back()->with('error', "Insufficient stock for '{$product->name}'. Available: {$product->stock}, Required: {$item->quantity}.");
            }
        }

        try {
            DB::transaction(function () use ($quote) {
                // 3. Create Sale record
                $sale = Sale::create([
                    'customer_id'      => $quote->customer_id,
                    'customer_name'    => optional($quote->customer)->name ?? 'Walk-in Customer',
                    'customer_phone'   => optional($quote->customer)->phone,
                    'customer_address' => optional($quote->customer)->address,
                    'sale_date'        => now(),
                    'total'            => $quote->total ?? $quote->items->sum(fn($i) => $i->quantity * $i->unit_price),
                    'status'           => 'completed',
                    'notes'            => 'Converted from Quote #' . ($quote->quote_number ?? $quote->id),
                ]);

                // 4. Create Sale Items & Deduct Product Stock
                foreach ($quote->items as $item) {
                    $sale->saleItems()->create([
                        'product_id'   => $item->product_id,
                        'product_name' => optional($item->product)->name ?? $item->product_name ?? 'Product',
                        'quantity'     => $item->quantity,
                        'unit_price'   => $item->unit_price,
                        'subtotal'     => $item->quantity * $item->unit_price,
                    ]);

                    if ($item->product) {
                        $item->product->decrement('stock', $item->quantity);
                    }
                }

                // 5. Update Quote status to 'accepted' and attach sale_id
                $quote->update([
                    'status'  => 'accepted',
                    'sale_id' => $sale->id,
                ]);
            });

            return redirect()->route('quotes.show', $quote->id)
                ->with('success', 'Quote successfully converted to Sale! Stock levels updated.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert quote: ' . $e->getMessage());
        }
    }
}