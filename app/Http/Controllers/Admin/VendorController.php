<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:vendor.view')
            ->only(['index']);

        $this->middleware('permission:vendor.create')
            ->only(['create', 'store']);

        $this->middleware('permission:vendor.edit')
            ->only(['edit', 'update']);

        $this->middleware('permission:vendor.delete')
            ->only(['destroy']);
    }

    /**
     * Display all vendors.
     */
    public function index()
    {
        $vendors = Vendor::withTrashed()
            ->latest()
            ->get();

        return view(
            'Admin.Vendor.index',
            compact('vendors')
        );
    }

    /**
     * Show create vendor form.
     */
    public function create()
    {
        return view('Admin.Vendor.create');
    }

    /**
     * Store new vendor.
     */
    public function store(StoreVendorRequest $request)
    {
        DB::beginTransaction();

        try {

            Vendor::create([
                'name' => $request->name,
                'email' => $request->email,
                'contact' => $request->contact,
                'gst_number' => $request->gst_number,
                'pan_number' => $request->pan_number,
                'company_name' => $request->company_name,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,

                'credit_limit' => $request->credit_limit ?? 0,
                'opening_balance' => $request->opening_balance ?? 0,
                'opening_balance_type' => $request->opening_balance_type,
                'payment_days' => $request->payment_days ?? 0,

                'bank_name' => $request->bank_name,
                'bank_account_no' => $request->bank_account_no,
                'ifsc_code' => $request->ifsc_code,

                'status' => $request->status ?? 'active',
                'remarks' => $request->remarks,

                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('Vendors')
                ->with('success', 'Vendor created successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show edit vendor form.
     */
    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);

        return view(
            'Admin.Vendor.edit',
            compact('vendor')
        );
    }

    /**
     * Update vendor.
     */
    public function update(
        UpdateVendorRequest $request,
        $id
    ) {
        DB::beginTransaction();

        try {

            $vendor = Vendor::findOrFail($id);

            $vendor->update([
                'name' => $request->name,
                'email' => $request->email,
                'contact' => $request->contact,
                'gst_number' => $request->gst_number,
                'pan_number' => $request->pan_number,
                'company_name' => $request->company_name,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,

                'credit_limit' => $request->credit_limit ?? 0,
                'opening_balance' => $request->opening_balance ?? 0,
                'opening_balance_type' => $request->opening_balance_type,
                'payment_days' => $request->payment_days ?? 0,

                'bank_name' => $request->bank_name,
                'bank_account_no' => $request->bank_account_no,
                'ifsc_code' => $request->ifsc_code,

                'status' => $request->status,
                'remarks' => $request->remarks,

                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('Vendors')
                ->with('success', 'Vendor updated successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete vendor.
     */
    public function destroy($id)
    {
        Vendor::findOrFail($id)->delete();

        return redirect()
            ->route('Vendors')
            ->with('success', 'Vendor deleted successfully');
    }

    /**
     * Restore deleted vendor.
     */
    public function restore($id)
    {
        Vendor::withTrashed()
            ->findOrFail($id)
            ->restore();

        return redirect()
            ->route('Vendors')
            ->with('success', 'Vendor restored successfully');
    }
}