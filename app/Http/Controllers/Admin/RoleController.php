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
  
    public function __construct()
        {
            $this->middleware('permission:role.view')->only(['index', 'show']);
            $this->middleware('permission:role.create')->only(['create', 'store']);
            $this->middleware('permission:role.edit')->only(['edit', 'update']);
            $this->middleware('permission:role.delete')->only(['destroy']);
        }
    
        // Role index
    public function index(Request $request): View
        {
            $roles = Role::with('users')->orderBy('id', 'DESC')->get();
            return view('Admin.Role.index', compact('roles'));
        }

        // Role creation
    public function create()
        { 
            $permissions = Permission::all(); 
            return view('Admin.Role.create', compact('permissions'));
        }

        // Role store
    public function store(Request $request)
        {
            $request->validate([
                'name' => 'required|string|unique:roles,name',
                'permissions' => 'nullable|array',
                'permissions.*' => 'string|exists:permissions,name',  
            ]);  
            $role = Role::create(['name' => $request->name]); 
            if($request->has('permissions')) {
                $role->syncPermissions($request->permissions);  
            }
            return redirect('/Role')->with('success', 'Role created successfully');
        }

        // show saved data                                                                                                                           
    public function show($id): View
        {
            $role = Role::findOrFail($id);
            $rolePermissions = $role->permissions()->get();  
            return view('Admin.Role.show', compact('role', 'rolePermissions'));
        }

        // Edit Role
    public function edit($id)
        {
            $role = Role::findOrFail($id);
            $permissions = Permission::all(); 
            $rolePermissions = $role->permissions->pluck('name')->toArray();
            return view('Admin.Role.edit', compact('role', 'permissions', 'rolePermissions'));
        }

        // Update Created Role 
    public function update(Request $request, $id)
        { 
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
            return redirect()->route('Role')->with('success', 'Role updated successfully!');
        }
        // Delete Role 
    public function destroy($id): RedirectResponse
        {
            $role = Role::findOrFail($id);
            $role->delete(); 
            return redirect()->route('Role')
                ->with('success', 'Role deleted successfully');
        }
        
}