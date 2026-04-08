<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // ----------------------
        // 1. Define all permissions
        // ----------------------
        $permissions = [
            // Users & Roles
            'user.create','user.view','user.edit','user.delete',
            'role.view','role.assign','permission.view',

            // Categories
            'category.create','category.view','category.edit','category.delete',

            // Products
            'product.create','product.view','product.edit','product.delete',

            // Vendors
            'vendor.create','vendor.view','vendor.edit','vendor.delete',

            // Purchases
            'purchase.create','purchase.view','purchase.edit','purchase.delete',
            'purchase.approve','purchase.print',

            // Stock
            'stock.in','stock.out','stock.view',

            // Delivery / Sales
            'delivery.create','delivery.view','delivery.edit','delivery.delete','delivery.print',

            // Reports
            'report.stock','report.purchase','report.sales','report.vendor','report.product','report.lowstock',

            // Activity Logs
            'activity.view','activity.delete',
        ];

        // ----------------------
        // 2. Create permissions
        // ----------------------
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ----------------------
        // 3. Create Roles
        // ----------------------
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $staff = Role::firstOrCreate(['name' => 'Staff']);

        // ----------------------
        // 4. Assign permissions to roles
        // ----------------------
        // Admin: All permissions
        $admin->syncPermissions(Permission::all());

        // Manager: Everything except user & role management
        $managerPermissions = [
            // Categories
            'category.create','category.view','category.edit','category.delete',
            // Products
            'product.create','product.view','product.edit','product.delete',
            // Vendors
            'vendor.create','vendor.view','vendor.edit','vendor.delete',
            // Purchases
            'purchase.create','purchase.view','purchase.edit','purchase.delete','purchase.approve','purchase.print',
            // Stock
            'stock.in','stock.out','stock.view',
            // Delivery / Sales
            'delivery.create','delivery.view','delivery.edit','delivery.delete','delivery.print',
            // Reports
            'report.stock','report.purchase','report.sales','report.vendor','report.product','report.lowstock',
        ];
        $manager->syncPermissions($managerPermissions);

        // Staff: Only stock in/out, delivery creation, and basic reports
        $staffPermissions = [
            'stock.in','stock.out','stock.view',
            'delivery.create','delivery.view',
            'report.stock','report.lowstock',
        ];
        $staff->syncPermissions($staffPermissions);

        $this->command->info('Roles & Permissions seeded successfully!');
    }
}