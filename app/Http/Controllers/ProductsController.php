<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Products::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(10)->withQueryString();

        return view('products.index', [
            'products'  => $products,
            'categorie' => null,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $categorie = null;
        $categories = Categorie::all();

        if ($request->has('categorie')) {
            $categorie = Categorie::findOrFail($request->categorie);
        }

        return view('products.create', compact('categorie', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'image'        => ['nullable'], // Accepts either uploaded File OR string URL
            'prix'         => ['required', 'numeric', 'min:0'],
            'stock'        => ['required', 'numeric', 'min:0'],
            'seuil_alerte' => ['required', 'numeric', 'min:0'],
            'categorie_id' => ['nullable', 'exists:categories,id'],
        ]);

        // Handle File Upload or URL String input for `image`
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        } elseif (is_string($request->input('image'))) {
            $validated['image'] = $request->input('image');
        }

        Products::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Products $product)
    {
        $categories = Categorie::all();

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Products $product)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'image'        => ['required'], // Accepts File OR string URL
            'prix'         => ['required', 'numeric', 'min:0'],
            'stock'        => ['required', 'numeric', 'min:0'],
            'seuil_alerte' => ['required', 'numeric', 'min:0'],
            'categorie_id' => ['nullable', 'exists:categories,id'],
        ]);

        // Handle File Upload or URL String update for `image`
        if ($request->hasFile('image')) {
            // Delete old uploaded local file if exists
            if ($product->image && !str_starts_with($product->image, 'http')) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->filled('image')) {
            $validated['image'] = $request->input('image');
        } else {
            // Keep existing image if no new file or URL string provided
            unset($validated['image']);
        }

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Products $product)
    {
        // Delete stored image file if local
        if ($product->image && !str_starts_with($product->image, 'http')) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return redirect()->route('products.index');
    }

    /**
     * Display products by category.
     */
    public function categorie(Categorie $categorie)
    {
        $products = $categorie->products()->latest()->paginate(10);

        return view('products.index', compact('products', 'categorie'));
    }
}