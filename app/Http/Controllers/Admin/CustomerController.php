<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
 
    public function __construct()
        {
            $this->middleware('permission:customer.view')
                ->only(['index']);

            $this->middleware('permission:customer.create')
                ->only(['create', 'store']);

            $this->middleware('permission:customer.edit')
                ->only(['edit', 'update']);

            $this->middleware('permission:customer.delete')
                ->only(['destroy']);
        }
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
        'name'              => 'required|string|max:255',
        'mobile'            => 'nullable|string|max:20',
        'alternate_mobile'  => 'nullable|string|max:20',
        'email'             => 'nullable|email|max:255',
        'gst_number'        => 'nullable|string|max:50',
        'pan_number'        => 'nullable|string|max:20',
        'credit_limit'      => 'nullable|numeric|min:0',
        'opening_balance'   => 'nullable|numeric',
    ]);

    Customer::create([
        'customer_code'     => 'CUS-' . time(),

        'name'              => $request->name,
        'company_name'      => $request->company_name,

        'mobile'            => $request->mobile,
        'alternate_mobile'  => $request->alternate_mobile,
        'email'             => $request->email,

        'billing_address'   => $request->billing_address,
        'shipping_address'  => $request->shipping_address,

        'city'              => $request->city,
        'state'             => $request->state,
        'pincode'           => $request->pincode,
        'country'           => $request->country ?? 'India',

        'gst_number'        => $request->gst_number,
        'pan_number'        => $request->pan_number,

        'credit_limit'      => $request->credit_limit ?? 0,
        'opening_balance'   => $request->opening_balance ?? 0,

        'customer_type'     => $request->customer_type ?? 'business',
        'status'            => $request->status ?? 1,

        'notes'             => $request->notes,

        'created_by'        => auth()->id(),
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
    $request->validate([
        'name'              => 'required|string|max:255',
        'mobile'            => 'nullable|string|max:20',
        'alternate_mobile'  => 'nullable|string|max:20',
        'email'             => 'nullable|email|max:255',
        'gst_number'        => 'nullable|string|max:50',
        'pan_number'        => 'nullable|string|max:20',
        'credit_limit'      => 'nullable|numeric|min:0',
        'opening_balance'   => 'nullable|numeric',
    ]);

    $customer = Customer::findOrFail($id);

    $customer->update([
        'name'              => $request->name,
        'company_name'      => $request->company_name,

        'mobile'            => $request->mobile,
        'alternate_mobile'  => $request->alternate_mobile,
        'email'             => $request->email,

        'billing_address'   => $request->billing_address,
        'shipping_address'  => $request->shipping_address,

        'city'              => $request->city,
        'state'             => $request->state,
        'pincode'           => $request->pincode,
        'country'           => $request->country ?? 'India',

        'gst_number'        => $request->gst_number,
        'pan_number'        => $request->pan_number,

        'credit_limit'      => $request->credit_limit ?? 0,
        'opening_balance'   => $request->opening_balance ?? 0,

        'customer_type'     => $request->customer_type,
        'status'            => $request->status,

        'notes'             => $request->notes,

        'updated_by'        => auth()->id(),
    ]);

    return redirect()->route('Customer')
        ->with('success', 'Customer updated successfully');
}
    /**
     * Delete customer
     */
 
public function toggleStatus($id)
{
    $customer = Customer::findOrFail($id);

    $customer->update([
        'status' => !$customer->status
    ]);

    return redirect()->back()
        ->with('success', 'Customer status updated successfully');
}
}