<?php 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;  
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function __construct()
        {
            $this->middleware('permission:vendor.view')->only(['index']); 
            $this->middleware('permission:vendor.create')
                ->only(['create', 'store']); 
            $this->middleware('permission:vendor.edit')
                ->only(['edit', 'update']); 
            $this->middleware('permission:vendor.delete')
                ->only(['destroy']);
        }


        // Vendor index
    public function index()
        {
            $vendors = Vendor::latest()->get();
            return view('admin.vendor.index', compact('vendors'));
        }
    
        // Vendor Create
    public function create()
        {
            return view('admin.vendor.create');
        }
        // Vendor Store
    public function store(Request $request)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'contact' => 'nullable|string|max:20',
                'gst_number' => 'nullable|string|max:50',
                'company_name' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
            ]); 
            Vendor::create($request->only([
                'name',
                'email',
                'contact',
                'gst_number',
                'company_name',
                'address',
                'city',
                'state'
            ])); 
            return redirect()->route('Vendors')
                ->with('success', 'Vendor created successfully');
        }

        // Vendor Edit
    public function edit($id)
        {
            $vendor = Vendor::findOrFail($id);
            return view('admin.vendor.edit', compact('vendor'));
        }

        // Update Created Vendor
    public function update(Request $request, $id)
        {
            $vendor = Vendor::findOrFail($id);
            $vendor->update($request->all()); 
            return redirect()->route('Vendors')
                ->with('success', 'Vendor updated successfully');
        }

        // Delete Vendor
    public function destroy($id)
        {
            Vendor::destroy($id); 
            return redirect()->route('vendors.index')
                ->with('success', 'Vendor deleted successfully');
        }
}