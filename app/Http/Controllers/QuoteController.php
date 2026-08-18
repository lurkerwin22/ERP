<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
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

    public function convertToSale(Quote $quote)
    {
        // Guard 1: Must be accepted
        if ($quote->status !== 'accepted') {
            return back()->with('error', 'Only accepted quotes can be converted into a sale.');
        }

        // Guard 2: Prevent duplicate conversion
        if ($quote->isConverted()) {
            return back()->with('error', 'This quote has already been converted into Sale #' . $quote->sale_id);
        }

        // Guard 3: Stock Check
        foreach ($quote->items as $item) {
            if ($item->product->stock < $item->quantity) {
                return back()->with('error', "Insufficient stock for product: {$item->product->name}. Current stock: {$item->product->stock}");
            }
        }

        $sale = DB::transaction(function () use ($quote) {
            // 1. Create Sale
            $sale = Sale::create([
                'customer_id' => $quote->customer_id,
                'total' => $quote->total,
                'paid' => 0, // Initial balance pending
                'remaining' => $quote->total,
            ]);

            // 2. Create Sale Items & Deduct Stock
            foreach ($quote->items as $item) {
                $sale->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                ]);

                // Stock deduction happens ONLY here
                $item->product->decrement('stock', $item->quantity);
            }

            // 3. Mark Quote as Converted
            $quote->update([
                'sale_id' => $sale->id,
            ]);

            return $sale;
        });

        return redirect()->route('sales.show', $sale)->with('success', 'Quote successfully converted to Sale #' . $sale->id);
    }
}