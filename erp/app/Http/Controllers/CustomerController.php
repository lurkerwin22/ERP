<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Step 8, 13 & 14: Display customer list with Search & Pagination
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $customers = Customer::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate(10)
        ->withQueryString(); // Preserves search query across pagination links

        return view('customers.index', compact('customers', 'search'));
    }

    /**
     * Step 9: Show form to create a new customer
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Step 7 & 9: Store a newly created customer with Validation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['required', 'string', 'max:50'],
            'address'   => ['nullable', 'string', 'max:255'],
            'city'     => ['nullable', 'string', 'max:100'],
            'notes'     => ['nullable', 'string', 'max:1000'],
        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Step 11: Display customer details
     */
    public function show(Customer $customer)
    {
        $customer->load(['sales' => function ($query) {
            $query->where('status', '!=', 'cancelled')->with('payments');
        }]);

        return view('customers.show', compact('customer'));
    }

    /**
     * Step 10: Show form to edit a customer
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Step 7 & 10: Update customer details with unique email exception
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($customer->id)],
            'phone' => ['required', 'string', 'max:50'],
            'address'   => ['nullable', 'string', 'max:255'],
            'city'     => ['nullable', 'string', 'max:100'],
            'notes'     => ['nullable', 'string', 'max:1000'],
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Step 12: Delete customer
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully. Historical sales records have been preserved.');
    }
}