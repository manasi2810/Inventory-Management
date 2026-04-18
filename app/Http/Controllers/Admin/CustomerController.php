<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Show all customers
     */
    public function index()
    {
        $customers = Customer::latest()->get();
        return view('admin.customer.index', compact('customers'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.customer.create');
    }

    /**
     * Store new customer
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        Customer::create([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'mobile' => $request->mobile,
            'alternate_mobile' => $request->alternate_mobile,
            'email' => $request->email,

            'billing_address' => $request->billing_address,
            'shipping_address' => $request->shipping_address,

            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'country' => $request->country ?? 'India',

            'gst_number' => $request->gst_number,
            'pan_number' => $request->pan_number,

            'customer_type' => $request->customer_type ?? 'business',
            'status' => $request->status ?? 1,

            'notes' => $request->notes,
        ]);

        return redirect()->route('Customer')
                         ->with('success', 'Customer created successfully');
    }

    /**
     * Edit customer
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customer.edit', compact('customer'));
    }

    /**
     * Update customer
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $customer->update([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'mobile' => $request->mobile,
            'alternate_mobile' => $request->alternate_mobile,
            'email' => $request->email,

            'billing_address' => $request->billing_address,
            'shipping_address' => $request->shipping_address,

            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'country' => $request->country ?? 'India',

            'gst_number' => $request->gst_number,
            'pan_number' => $request->pan_number,

            'customer_type' => $request->customer_type,
            'status' => $request->status,

            'notes' => $request->notes,
        ]);

        return redirect()->route('Customer')
                         ->with('success', 'Customer updated successfully');
    }

    /**
     * Delete customer
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('Customer')
                         ->with('success', 'Customer deleted successfully');
    }
}