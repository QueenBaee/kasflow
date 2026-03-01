<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Store;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function store(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'speed_package' => 'required|string|max:100',
            'monthly_fee' => 'required|numeric|min:0',
        ]);

        $store->customers()->create($validated);

        return redirect()->back()->with('success', 'Customer added successfully');
    }

    public function update(Request $request, Store $store, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'speed_package' => 'required|string|max:100',
            'monthly_fee' => 'required|numeric|min:0',
        ]);

        $customer->update($validated);

        return redirect()->back()->with('success', 'Customer updated successfully');
    }

    public function destroy(Store $store, Customer $customer)
    {
        $customer->delete();

        return redirect()->back()->with('success', 'Customer deleted successfully');
    }
}
