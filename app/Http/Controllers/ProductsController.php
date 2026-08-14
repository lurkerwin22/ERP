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
    public function index()
    {
        $products = Products::latest()->paginate(10);

        return view('products.index', [
            'products' => $products,
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
            'url'          => ['nullable', 'url'],
            'prix'         => ['required', 'numeric', 'min:0'],
            'stock'        => ['required', 'numeric' , 'min:0'],
            'seuil_alerte' => ['required', 'numeric', 'min:0'],
            'categorie_id' => ['required', 'exists:categories,id'],
        ]);

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
            'url'          => ['required', 'url'],
            'prix'         => ['required', 'numeric'],
            'stock'        => ['required', 'numeric'],
            'seuil_alerte' => ['required', 'numeric'],
        ]);

        $product->update($validated);

        return redirect('/products');
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
