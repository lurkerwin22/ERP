<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Client;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VenteController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $ventes = Vente::with(['client', 'ligneVentes'])
            ->when($search, function ($query, $search) {
                return $query->whereHas('client', function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('ventes.index', compact('ventes', 'search'));
    }

    /**
     * Show the form for creating a new sale (We will build this UI next).
     */
    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        // Only load products that have available stock
        $produits = Products::where('stock', '>', 0)->orderBy('nom')->get();

        return view('ventes.create', compact('clients', 'produits'));
    }

    /**
     * Display the specified sale details.
     */
    public function show(Vente $vente)
    {
        $vente->load(['client', 'ligneVentes.produit']);

        return view('ventes.show', compact('vente'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'          => ['nullable', 'exists:clients,id'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'], // <-- Updated from produit_id
            'items.*.quantite'   => ['required', 'integer', 'min:1'],
            'notes'              => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated) {
            $totalVente = 0;
            $itemsToCreate = [];

            foreach ($validated['items'] as $item) {
                $produit = Products::lockForUpdate()->findOrFail($item['product_id']); // <-- Updated

                if ($produit->stock < $item['quantite']) {
                    throw new \Exception("Stock insuffisant pour: {$produit->name}. Disponible: {$produit->stock}");
                }

                $unitPrice = $produit->prix;
                $subtotal = $unitPrice * $item['quantite'];
                $totalVente += $subtotal;

                $produit->decrement('stock', $item['quantite']);

                $itemsToCreate[] = [
                    'product_id'    => $produit->id, // <-- Updated from produit_id
                    'quantite'      => $item['quantite'],
                    'prix_unitaire' => $unitPrice,
                    'sous_total'    => $subtotal,
                ];
            }

            $vente = Vente::create([
                'client_id'  => $validated['client_id'] ?? null,
                'date_vente' => now(),
                'total'      => $totalVente,
                'statut'     => 'completee',
                'notes'      => $validated['notes'] ?? null,
            ]);

            foreach ($itemsToCreate as $item) {
                $vente->ligneVentes()->create($item);
            }
        });

        return redirect()->route('ventes.index')
            ->with('success', 'Sale completed successfully and stock updated.');
    }
    /**
     * Cancel a completed sale and restore product stock.
     */
    public function cancel(Vente $vente)
    {
        if ($vente->statut === 'annulee') {
            return redirect()->back()->withErrors(['cancel' => 'This sale is already cancelled.']);
        }

        DB::transaction(function () use ($vente) {
            // Restore stock for all line items
            foreach ($vente->ligneVentes as $item) {
                // Updated column name from 'quantite_stock' to 'stock'
                $item->produit->increment('stock', $item->quantite);
            }

            // Mark sale as cancelled
            $vente->update(['statut' => 'annulee']);
        });

        return redirect()->route('ventes.index')
            ->with('success', "Sale #{$vente->id} cancelled and stock restored successfully.");
    }
}