<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\Supplier;
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

                $purchase->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total' => $lineTotal,
                ]);

                // Update Product
                $product = Product::findOrFail($itemData['product_id']);

                // Increment stock directly
                $product->increment('stock', $itemData['quantity']);

                // Option A: Update product default purchase price to latest price
                $product->update([
                    'purchase_price' => $itemData['unit_price']
                ]);

                // Log Stock Movement if relation/table exists
                if (method_exists($product, 'stockMovements')) {
                    $product->stockMovements()->create([
                        'type' => 'in',
                        'quantity' => $itemData['quantity'],
                        'notes' => "Purchase #{$purchase->id}",
                    ]);
                }
            }

            // Update Header Total
            $purchase->update(['total' => $grandTotal]);
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase recorded successfully and stock updated.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }
}
