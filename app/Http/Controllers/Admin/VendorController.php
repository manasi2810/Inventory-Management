<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::latest()->get();
        return view('Admin.Vendor.index', compact('vendors'));
    }

    public function create()
    {
        return view('Admin.Vendor.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'status' => 'required'
        ]);

        Vendor::create($request->all());

        return redirect('/Vendor')
            ->with('success', 'Vendor created successfully');
    }

    public function edit($id)
{
    $vendor = Vendor::findOrFail($id);

    return view('admin.vendor.edit', compact('vendor'));
}

    public function update(Request $request, Vendor $Vendor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'status' => 'required'
        ]);

        $Vendor->update($request->all());  
        return redirect('/Vendor') 
            ->with('success', 'Vendor updated successfully');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect()->back()
            ->with('success', 'Vendor deleted successfully');
    }
}