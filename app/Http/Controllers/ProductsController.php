<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Categorie;

use Illuminate\Http\Request;

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

        if ($request->has('categorie')) {
            $categorie = Categorie::findOrFail($request->categorie);
        }

        return view('products.create', compact('categorie'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'url'          => ['nullable', 'url'],
            'prix'         => ['required', 'numeric', 'min:0'],
            'stock'        => ['required', 'numeric', 'min:0'],
            'seuil_alerte' => ['required', 'numeric', 'min:0'],
            'categorie_id' => ['required', 'exists:categories,id'],
        ]);

        // Handle Image Upload or URL fallback
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['url'] = $path;
        }

        unset($validated['image']);

        Products::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    /* public function show(products $products)
    {
        $product = Products::find($products);
        return view('products.show',['products' => $product]);
    } */

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Products $product)
    {
        return view('products.edit',['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Products $product)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:254'],
            'image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'url'          => ['nullable', 'string'],
            'prix'         => ['required', 'numeric'],
            'stock'        => ['required', 'numeric'],
            'seuil_alerte' => ['required', 'numeric'],
        ]);

        // Handle New Image Upload
        if ($request->hasFile('image')) {
            // Optionally delete old image if it was uploaded locally
            if ($product->url && !str_starts_with($product->url, 'http')) {
                Storage::disk('public')->delete($product->url);
            }

            $path = $request->file('image')->store('products', 'public');
            $validated['url'] = $path;
        }

        unset($validated['image']);

        $product->update($validated);

        return redirect('/products')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Products $product)
    {
        $product->delete();
        return redirect('/products');
    }
    public function categorie(Categorie $categorie)
    {
        $products = $categorie->products()->latest()->paginate(10);

        return view('products.index', compact('products', 'categorie'));
    }

}
