<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Step 8, 13 & 14: Display customer list with Search & Pagination
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $clients = Client::when($search, function ($query, $search) {
            return $query->where('nom', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('telephone', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(10)
        ->withQueryString(); // Preserves search query across pagination links

        return view('clients.index', compact('clients', 'search'));
    }

    /**
     * Step 9: Show form to create a new customer
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Step 7 & 9: Store a newly created customer with Validation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'       => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:clients,email'],
            'telephone' => ['required', 'string', 'max:50'],
            'adresse'   => ['nullable', 'string', 'max:255'],
            'ville'     => ['nullable', 'string', 'max:100'],
            'notes'     => ['nullable', 'string', 'max:1000'],
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Step 11: Display customer details
     */
    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    /**
     * Step 10: Show form to edit a customer
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Step 7 & 10: Update customer details with unique email exception
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nom'       => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', Rule::unique('clients', 'email')->ignore($client->id)],
            'telephone' => ['required', 'string', 'max:50'],
            'adresse'   => ['nullable', 'string', 'max:255'],
            'ville'     => ['nullable', 'string', 'max:100'],
            'notes'     => ['nullable', 'string', 'max:1000'],
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Step 12: Delete customer
     */
    public function destroy(Client $client)
    {
        // Check if the Vente model exists before running the relationship check
        if (class_exists(\App\Models\Vente::class) && $client->ventes()->exists()) {
            return redirect()->back()
                ->withErrors(['delete' => 'Cannot delete customer with associated sales records.']);
        }

        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Customer deleted successfully.');
    }
}