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
public function show(Vente $vente)
    {
        // Safe loading (doesn't throw if relationships are null)
        $vente->load(['client', 'ligneVentes.product']);

        return view('ventes.show', compact('vente'));
    }

    public function invoice(Vente $vente)
    {
        $vente->load(['client', 'ligneVentes.product']);

        return view('ventes.invoice', compact('vente'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'notes'     => 'nullable|string',
            'items'     => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantite'   => 'required|integer|min:1',
        ]);

        // 1. Fetch Client info snapshot if client selected
        $client = $validated['client_id'] ? Client::find($validated['client_id']) : null;

        // 2. Fetch Product info snapshots
        $itemsToCreate = [];
        foreach ($validated['items'] as $item) {
            $product = Products::findOrFail($item['product_id']);

            $itemsToCreate[] = [
                'product_id'    => $product->id,
                'nom_produit'   => $product->name,
                'prix_unitaire' => $product->prix,
                'quantite'      => $item['quantite'],
                'sous_total'    => $product->prix * $item['quantite'],
            ];
        }

        // 3. Store inside Database Transaction
        DB::transaction(function () use ($validated, $client, $itemsToCreate) {
        $vente = Vente::create([
            'client_id'        => $client?->id,
            // Save actual client's name snapshot if present, otherwise default to 'Walk-in Customer'
            'client_nom'       => $client ? $client->nom : 'Walk-in Customer',
            'client_telephone' => $client?->telephone,
            'client_adresse'   => $client?->adresse,
            'date_vente'       => now(),
            'total'            => array_sum(array_column($itemsToCreate, 'sous_total')),
            'statut'           => 'completee',
            'notes'            => $validated['notes'] ?? null,
        ]);

        foreach ($itemsToCreate as $item) {
            Products::where('id', $item['product_id'])->decrement('stock', $item['quantite']);
            $vente->ligneVentes()->create($item);
        }
    });

        return redirect()->route('ventes.index')
            ->with('success', 'Sale saved successfully.');
    }

    /**
     * Cancel a completed sale and safely restore stock if products still exist.
     */
    public function cancel(Vente $vente)
    {
        if ($vente->statut === 'annulee') {
            return back()->with('error', 'Sale is already cancelled.');
        }

        DB::transaction(function () use ($vente) {
            foreach ($vente->ligneVentes as $item) {
                // Only restore stock if product still exists in catalog
                if ($item->product_id && $item->product) {
                    $item->product->increment('stock', $item->quantite);
                }
            }

            $vente->update(['statut' => 'annulee']);
        });

        return redirect()->route('ventes.show', $vente)
            ->with('success', 'Sale cancelled successfully.');
    }
}