<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function __construct()
    {
        // View Permission
        $this->middleware('permission:customer.view')
            ->only(['index']);

        // Create Permission
        $this->middleware('permission:customer.create')
            ->only(['create', 'store']);

        // Edit Permission
        $this->middleware('permission:customer.edit')
            ->only(['edit', 'update']);

        // Delete Permission
        $this->middleware('permission:customer.delete')
            ->only(['destroy']);
    }

    /**
     * Display all customers.
     */
    public function index()
    {
        $customers = Customer::latest()->get();

        return view(
            'Admin.Customer.index',
            compact('customers')
        );
    }

    /**
     * Show create customer form.
     */
    public function create()
    {
        return view('Admin.Customer.create');
    }

    /**
     * Store new customer.
     */
    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();

        $data['customer_code'] = 'CUS-' . time();
        $data['country'] = $data['country'] ?? 'India';
        $data['credit_limit'] = $data['credit_limit'] ?? 0;
        $data['opening_balance'] = $data['opening_balance'] ?? 0;
        $data['customer_type'] = $data['customer_type'] ?? 'business';
        $data['status'] = $data['status'] ?? 1;
        $data['created_by'] = auth()->id();

        Customer::create($data);

        return redirect()
            ->route('Customer')
            ->with(
                'success',
                'Customer created successfully'
            );
    }

    /**
     * Show edit customer form.
     */
    public function edit(Customer $customer)
    {
        return view(
            'Admin.Customer.edit',
            compact('customer')
        );
    }

    /**
     * Update customer.
     */
    public function update(
        UpdateCustomerRequest $request,
        Customer $customer
    ) {
        $data = $request->validated();

        $data['country'] = $data['country'] ?? 'India';
        $data['credit_limit'] = $data['credit_limit'] ?? 0;
        $data['opening_balance'] = $data['opening_balance'] ?? 0;
        $data['updated_by'] = auth()->id();

        $customer->update($data);

        return redirect()
            ->route('Customer')
            ->with(
                'success',
                'Customer updated successfully'
            );
    }

    /**
     * Toggle customer status.
     */
    public function toggleStatus(Customer $customer)
    {
        $customer->update([
            'status' => ! $customer->status,
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Customer status updated successfully'
            );
    }

    /**
     * Delete customer.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()
            ->route('Customer')
            ->with(
                'success',
                'Customer deleted successfully'
            );
    }
}