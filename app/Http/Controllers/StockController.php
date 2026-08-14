<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Step 8: Display general stock overview & alerts
     */
    public function index()
    {
        $products = Products::latest()->Paginate(10);
        return view('stock.index', compact('products'));
    }

    /**
     * Step 8: Display movement history for a product
     */
    public function movements(Products $product)
    {
        $movements = $product->stockMovements()->latest()->get();
        return view('stock.movements', compact('product', 'movements'));
    }

    /**
     * Show page to adjust stock
     */
    public function adjust(Products $product)
    {
        return view('stock.adjust', compact('product'));
    }

    /**
     * Step 6: Add Stock (+)
     */
    public function addStock(Request $request, Products $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'reason'   => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $product) {
            $product->increment('stock', $request->quantity);

            StockMovement::create([
                'product_id' => $product->id, // or 'produit_id' depending on your migration column
                'type'       => 'in',
                'quantity'   => $request->quantity,
                'reason'     => $request->reason ?? 'Purchase/Restock',
            ]);
        });

        return redirect()->back()->with('success', 'Stock added successfully.');
    }

    /**
     * Step 7: Remove Stock (-)
     */
    public function removeStock(Request $request, Products $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'reason'   => 'nullable|string|max:255',
        ]);

        if ($request->quantity > $product->stock) {
            return redirect()->back()->withErrors([
                'quantity' => 'Cannot remove more stock than available (' . $product->stock . ' available).'
            ]);
        }

        DB::transaction(function () use ($request, $product) {
            $product->decrement('stock', $request->quantity);

            StockMovement::create([
                'product_id' => $product->id, // or 'produit_id' depending on your migration column
                'type'       => 'out',
                'quantity'   => $request->quantity,
                'reason'     => $request->reason ?? 'Sale/Reduction',
            ]);
        });

        return redirect()->back()->with('success', 'Stock removed successfully.');
    }
}