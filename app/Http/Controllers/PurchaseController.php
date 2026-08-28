<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'items.product'])->latest('purchase_date');

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('purchase_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('purchase_date', '<=', $request->to_date);
        }

        $purchases = $query->paginate(10)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get();

        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::select('id', 'name', 'purchase_price', 'stock', 'supplier_id')->orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $grandTotal = 0;

            // 1. Create Purchase
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'purchase_date' => $validated['purchase_date'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'completed',
                'total' => 0,
            ]);

            // 2. Loop Items, calculate total, create records, update stock & purchase_price
            foreach ($validated['items'] as $itemData) {
                $lineTotal = $itemData['quantity'] * $itemData['unit_price'];
                $grandTotal += $lineTotal;

                // Lock the product so stock and purchase data stay consistent.
                $product = Product::whereKey($itemData['product_id'])->lockForUpdate()->firstOrFail();

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total' => $lineTotal,
                ]);


                // Increment stock directly
                $product->increment('stock', $itemData['quantity']);

                // Option A: Update product default purchase price to latest price
                $product->update([
                    'purchase_price' => $itemData['unit_price']
                ]);

                // Record the inventory change in the stock ledger.
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $itemData['quantity'],
                    'reason' => "Purchase #{$purchase->id}",
                ]);
            }

            // Update Header Total
            $purchase->update(['total' => $grandTotal]);
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase recorded successfully and stock updated.');
    }

    public function cancel(Purchase $purchase)
    {
        if ($purchase->status === 'cancelled') {
            return back()->with('error', 'This purchase is already cancelled.');
        }

        try {
            DB::transaction(function () use ($purchase) {
                $purchase = Purchase::whereKey($purchase->id)->lockForUpdate()->firstOrFail();
                $purchase->load('items');

                foreach ($purchase->items as $item) {
                    $product = Product::whereKey($item->product_id)->lockForUpdate()->first();
                    if (!$product) {
                        continue;
                    }

                    if ($product->stock < $item->quantity) {
                        throw new \RuntimeException("Cannot cancel purchase #{$purchase->id}: '{$product->name}' only has {$product->stock} in stock, but {$item->quantity} would need to be removed.");
                    }

                    $product->decrement('stock', $item->quantity);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'out',
                        'quantity' => $item->quantity,
                        'reason' => "Cancellation of Purchase #{$purchase->id}",
                    ]);
                }

                $purchase->update(['status' => 'cancelled']);
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Unable to cancel the purchase: ' . $e->getMessage());
        }

        return redirect()->route('purchases.show', $purchase)->with('success', 'Purchase cancelled and stock reversed successfully.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }
}
