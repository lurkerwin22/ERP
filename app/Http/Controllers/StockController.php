<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
        $query = Product::query();

        // 1. Search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // 2. Stock Status Tag Filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            
            if ($status === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            } elseif ($status === 'low_stock') {
                $query->where('stock', '>', 0)
                      ->whereColumn('stock', '<=', 'alert_threshold');
            } elseif ($status === 'normal') {
                $query->whereColumn('stock', '>', 'alert_threshold');
            }
        }

        // 3. Sorting
        $sort = $request->input('sort', 'name');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        switch ($sort) {
            case 'stock':
                $query->orderBy('stock', $direction);
                break;

            case 'status':
                $query->orderByRaw("
                    CASE 
                        WHEN stock <= 0 THEN 0 
                        WHEN stock <= alert_threshold THEN 1 
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
    public function movements(Product $product)
    {
        $movements = $product->stockMovements()->latest()->get();
        return view('stock.movements', compact('product', 'movements'));
    }

    /**
     * Show page to adjust stock
     */
    public function adjust(Product $product)
    {
        return view('stock.adjust', compact('product'));
    }

    /**
     * Step 6: Add Stock (+)
     */
    public function addStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'reason'   => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $product) {
            $product = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $product->increment('stock', $request->quantity);

            StockMovement::create([
                'product_id' => $product->id, // or 'product_id' depending on your migration column
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
    public function removeStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'reason'   => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($request, $product) {
                $product = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();

                if ($request->quantity > $product->stock) {
                    throw new \RuntimeException('Cannot remove more stock than available (' . $product->stock . ' available).');
                }

                $product->decrement('stock', $request->quantity);

                StockMovement::create([
                    'product_id' => $product->id, // or 'product_id' depending on your migration column
                    'type'       => 'out',
                    'quantity'   => $request->quantity,
                    'reason'     => $request->reason ?? 'Sale/Reduction',
                ]);
            });
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['quantity' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Stock removed successfully.');
    }
}