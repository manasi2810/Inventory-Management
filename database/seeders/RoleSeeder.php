<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $admin = Role::create(['name' => 'Admin']);
        $hr = Role::create(['name' => 'HR']);
        $employee = Role::create(['name' => 'Employee']);

        // Create permissions (optional)
        Permission::create(['name' => 'manage employees']);
        Permission::create(['name' => 'view attendance']);
        Permission::create(['name' => 'approve leave']);
        Permission::create(['name' => 'generate payroll']);

        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all()); // Admin gets all
        $hr->givePermissionTo(['manage employees','view attendance','approve leave','generate payroll']);
        $employee->givePermissionTo(['view attendance']);
    }
}