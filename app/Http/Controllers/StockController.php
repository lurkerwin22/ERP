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
    public function index(Request $request)
    {
        $query = Products::query();

        // Handle Search if present
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Handle Sorting
        $sort = $request->input('sort', 'name'); // Default sort field
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        switch ($sort) {
            case 'stock':
                $query->orderBy('stock', $direction);
                break;

            case 'status':
                // Custom SQL ordering for status logic: Out of stock (0) -> Low stock (1) -> Normal (2)
                $query->orderByRaw("
                    CASE 
                        WHEN stock <= 0 THEN 0 
                        WHEN stock <= seuil_alerte THEN 1 
                        ELSE 2 
                    END {$direction}
                ");
                break;

            case 'name':
            default:
                $query->orderBy('name', $direction);
                break;
        }

        $products = $query->paginate(10)->withQueryString();

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