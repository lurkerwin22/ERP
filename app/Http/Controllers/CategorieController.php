<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Categorie::latest()->paginate(9);

        return view('categories.index', [
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:254'],
        ]);

        Categorie::create($validated);

        return redirect('/categories')
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Display the specified category.
     */
    public function show(Categorie $categorie)
    {
        return view('categories.show', [
            'categorie' => $categorie
        ]);
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Categorie $categorie)
    {
        return view('categories.edit', [
            'categorie' => $categorie
        ]);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Categorie $categorie)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:254'],
        ]);

        $categorie->update($validated);

        return redirect('/categories')
            ->with('success', 'Catégorie modifiée avec succès.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Categorie $categorie)
    {
        $categorie->delete();

        return redirect('/categories')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}

