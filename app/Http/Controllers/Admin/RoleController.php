<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:role.view')
            ->only(['index', 'show']);

        $this->middleware('permission:role.create')
            ->only(['create', 'store']);

        $this->middleware('permission:role.edit')
            ->only(['edit', 'update']);

        $this->middleware('permission:role.delete')
            ->only(['destroy']);
    }


    /**
     * Role index
     */
    public function index(): View
    {
        $roles = Role::with('users')
            ->orderBy('id', 'DESC')
            ->get();

        return view(
            'Admin.Role.index',
            compact('roles')
        );
    }


    /**
     * Role creation page
     */
    public function create(): View
    {
        $permissions = Permission::all();

        return view(
            'Admin.Role.create',
            compact('permissions')
        );
    }


    /**
     * Store Role
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $role = Role::create([
            'name' => $validated['name'],
        ]);

        $role->syncPermissions(
            $validated['permissions'] ?? []
        );

        return redirect()
            ->route('Role')
            ->with(
                'success',
                'Role created successfully'
            );
    }


    /**
     * Show Role
     */
    public function show($id): View
    {
        $role = Role::findOrFail($id);

        $rolePermissions = $role
            ->permissions()
            ->get();

        return view(
            'Admin.Role.show',
            compact(
                'role',
                'rolePermissions'
            )
        );
    }


    /**
     * Edit Role
     */
    public function edit($id): View
    {
        $role = Role::findOrFail($id);

        $permissions = Permission::all();

        $rolePermissions = $role
            ->permissions
            ->pluck('name')
            ->toArray();

        return view(
            'Admin.Role.edit',
            compact(
                'role',
                'permissions',
                'rolePermissions'
            )
        );
    }


    /**
     * Update Role
     */
    public function update(
        UpdateRoleRequest $request,
        $id
    ): RedirectResponse {

        $role = Role::findOrFail($id);

        $validated = $request->validated();

        $role->update([
            'name' => $validated['name'],
        ]);

        $role->syncPermissions(
            $validated['permissions'] ?? []
        );

        return redirect()
            ->route('Role')
            ->with(
                'success',
                'Role updated successfully!'
            );
    }


    /**
     * Delete Role
     */
    public function destroy($id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        $role->delete();

        return redirect()
            ->route('Role')
            ->with(
                'success',
                'Role deleted successfully'
            );
    }
}