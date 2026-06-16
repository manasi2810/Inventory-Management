<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:vendor.view')->only(['index']);
        $this->middleware('permission:vendor.create')->only(['create', 'store']);
        $this->middleware('permission:vendor.edit')->only(['edit', 'update']);
        $this->middleware('permission:vendor.delete')->only(['destroy']);
    }

    public function index()
    {
        $vendors = Vendor::withTrashed()
            ->latest()
            ->get();

        return view('admin.vendor.index', compact('vendors'));
    }

    public function create()
    {
        return view('admin.vendor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',

            'credit_limit' => 'nullable|numeric|min:0',
            'opening_balance' => 'nullable|numeric|min:0',
            'opening_balance_type' => 'required|in:CR,DR',
            'payment_days' => 'nullable|integer|min:0',

            'bank_name' => 'nullable|string|max:255',
            'bank_account_no' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',

            'status' => 'required|in:active,inactive,blocked',
            'remarks' => 'nullable|string',
        ]);

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

            return redirect()->route('Vendors')
                ->with('success', 'Vendor created successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('admin.vendor.edit', compact('vendor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',

            'credit_limit' => 'nullable|numeric|min:0',
            'opening_balance' => 'nullable|numeric|min:0',
            'opening_balance_type' => 'required|in:CR,DR',
            'payment_days' => 'nullable|integer|min:0',

            'bank_name' => 'nullable|string|max:255',
            'bank_account_no' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',

            'status' => 'required|in:active,inactive,blocked',
            'remarks' => 'nullable|string',
        ]);

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

            return redirect()->route('Vendors')
                ->with('success', 'Vendor updated successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        Vendor::findOrFail($id)->delete();

        return redirect()->route('Vendors')
            ->with('success', 'Vendor deleted successfully');
    }

    public function restore($id)
    {
        Vendor::withTrashed()->findOrFail($id)->restore();

        return redirect()->route('Vendors')
            ->with('success', 'Vendor restored successfully');
    }
}