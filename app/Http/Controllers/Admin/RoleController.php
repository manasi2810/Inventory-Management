<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    /**
     * Constructor to apply middleware
     */
    

    public function index(Request $request): View
        {
            $roles = Role::with('users')->orderBy('id', 'DESC')->get();
            return view('admin.role.index', compact('roles'));
        }

    public function create()
        { 
            $permissions = Permission::all(); 
            return view('admin.role.create', compact('permissions'));
        }

    public function store(Request $request)
        {
            $request->validate([
                'name' => 'required|string|unique:roles,name',
                'permissions' => 'nullable|array',
                'permissions.*' => 'string|exists:permissions,name', // validate names
            ]);

            // Create role
            $role = Role::create(['name' => $request->name]);

            // Assign permissions
            if($request->has('permissions')) {
                $role->syncPermissions($request->permissions); // now it works
            }
            return redirect('/Role')->with('success', 'Role created successfully');
        }

    public function show($id): View
        {
            $role = Role::findOrFail($id);
            $rolePermissions = $role->permissions()->get();

            return view('admin.role.show', compact('role', 'rolePermissions'));
        }

    public function edit($id)
        {
            $role = Role::findOrFail($id);
            $permissions = Permission::all(); 
            $rolePermissions = $role->permissions->pluck('name')->toArray();

            return view('admin.role.edit', compact('role', 'permissions', 'rolePermissions'));
        }

    public function update(Request $request, $id)
        {
            // Validate input
            $request->validate([
                'name' => 'required|string|unique:roles,name,' . $id, 
                'permissions' => 'nullable|array',
                'permissions.*' => 'string|exists:permissions,name',  
            ]); 
            $role = Role::findOrFail($id); 
            $role->name = $request->name;
            $role->save(); 
            $permissions = $request->permissions ?? [];  
            $role->syncPermissions($permissions);  
            // Redirect back with success message
            return redirect()->route('Role')->with('success', 'Role updated successfully!');
        }

    public function destroy($id): RedirectResponse
        {
            $role = Role::findOrFail($id);
            $role->delete(); 
            return redirect()->route('roles.index')
                ->with('success', 'Role deleted successfully');
        }
}