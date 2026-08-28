<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            $quoteNumber = 'TMP-' . Str::uuid();

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

            $quote->update(['quote_number' => 'DEV-' . str_pad($quote->id, 6, '0', STR_PAD_LEFT)]);

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quote->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
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
        if ($quote->isConverted() || $quote->status === 'accepted') {
            return back()->with('error', 'A converted quote cannot have its status changed.');
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,sent,rejected',
        ]);

        $quote->update(['status' => $validated['status']]);

        return back()->with('success', 'Quote status updated to ' . ucfirst($quote->status));
    }

    public function convert(Quote $quote)
    {
        if ($quote->isConverted() || $quote->status === 'accepted') {
            return back()->with('error', 'This quote has already been accepted and converted into a sale.');
        }

        $quote->load(['items', 'customer']);

        try {
            DB::transaction(function () use ($quote) {
                $lockedQuote = Quote::whereKey($quote->id)->lockForUpdate()->firstOrFail();
                if ($lockedQuote->sale_id || $lockedQuote->status === 'accepted') {
                    throw new \RuntimeException('This quote has already been converted.');
                }

                $items = $lockedQuote->items()->get();
                $products = [];
                foreach ($items as $item) {
                    $product = Product::whereKey($item->product_id)->lockForUpdate()->first();
                    if (!$product) {
                        throw new \RuntimeException("Product for quote item #{$item->id} no longer exists.");
                    }
                    if ($product->stock < $item->quantity) {
                        throw new \RuntimeException("Insufficient stock for '{$product->name}'. Available: {$product->stock}, Required: {$item->quantity}.");
                    }
                    $products[$item->id] = $product;
                }

                $customer = Customer::find($lockedQuote->customer_id);
                $sale = Sale::create([
                    'user_id' => auth()->id(),
                    'customer_id' => $lockedQuote->customer_id,
                    'customer_name' => optional($customer)->name ?? 'Walk-in Customer',
                    'customer_phone' => optional($customer)->phone,
                    'customer_address' => optional($customer)->address,
                    'sale_date' => now(),
                    'total' => $lockedQuote->total ?? $items->sum(fn ($i) => $i->quantity * $i->unit_price),
                    'status' => 'completed',
                    'notes' => 'Converted from Quote #' . ($lockedQuote->quote_number ?? $lockedQuote->id),
                ]);

                foreach ($items as $item) {
                    $product = $products[$item->id];
                    $product->decrement('stock', $item->quantity);

                    $sale->saleItems()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal' => $item->quantity * $item->unit_price,
                    ]);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'out',
                        'quantity' => $item->quantity,
                        'reason' => "Sale #{$sale->id} (Quote #{$lockedQuote->quote_number})",
                    ]);
                }

                $lockedQuote->update(['status' => 'accepted', 'sale_id' => $sale->id]);
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to convert quote: ' . $e->getMessage());
        }

        return redirect()->route('quotes.show', $quote->id)->with('success', 'Quote successfully converted to Sale! Stock levels updated.');
    }

}
