<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run()
        { 
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
    
            $permissions = [ 
                // Dashboard
                'dashboard.view', 
                // Employees 
                'employee.create',
                'employee.view',
                'employee.edit',
                'employee.delete', 

                //  Roles 
                'role.view',
                'role.edit',
                'role.create',
                'role.delete',
                'role.assign', 
                'permission.view',

                // Categories
                'category.create',
                'category.view',
                'category.edit',
                'category.delete',

                // Products
                'product.create',
                'product.view',
                'product.edit',
                'product.delete',

                // Vendors
                'vendor.create',
                'vendor.view',
                'vendor.edit',
                'vendor.delete',

                // Customers
                'customer.create',
                'customer.view',
                'customer.edit',
                'customer.delete',

                // Purchases
                'purchase.create',
                'purchase.view',
                'purchase.edit',
                'purchase.delete',
                'purchase.approve',
                'purchase.print',
                'purchase.receive',
                'purchase.short-close',

                // Stock
                'stock.in',
                'stock.out',
                'stock.view',
                'stock.adjust',

                // Delivery Challan
                'delivery.create',
                'delivery.view',
                'delivery.edit',
                'delivery.delete',
                'delivery.print',
                'delivery.approve',
                'delivery.dispatch',
                'delivery.restore',
                'delivery.force-delete',
                'delivery.bulk-print',
                'delivery.trashed',

                // DC Return
                'dc-return.create',
                'dc-return.view',
                'dc-return.edit',
                'dc-return.delete',

                // Reports
                'report.stock',
                'report.purchase',
                'report.sales',
                'report.vendor',
                'report.product',
                'report.lowstock',
                'report.customer',
                'report.dc',
                'report.dcreturn',
                'report.ledger',

                // Activity
                'activity.view',
                'activity.delete',
            ];

            // CREATE PERMISSIONS
            foreach ($permissions as $perm) { 
                Permission::firstOrCreate([
                    'name' => $perm,
                    'guard_name' => 'web'
                ]);
            }

            // ---------------- ROLES ----------------

            $admin = Role::firstOrCreate([
                'name' => 'Admin',
                'guard_name' => 'web'
            ]); 
            $manager = Role::firstOrCreate([
                'name' => 'Manager',
                'guard_name' => 'web'
            ]); 
            $staff = Role::firstOrCreate([
                'name' => 'Staff',
                'guard_name' => 'web'
            ]); 
            $accounts = Role::firstOrCreate([
                'name' => 'Accounts',
                'guard_name' => 'web'
            ]); 
            $viewer = Role::firstOrCreate([
                'name' => 'Viewer',
                'guard_name' => 'web'
            ]);

            // ---------------- ROLE PERMISSIONS ----------------

            // ADMIN → FULL ACCESS
            $admin->syncPermissions(Permission::all());

            // MANAGER → FULL ACCESS
            $manager->syncPermissions(Permission::all());

            // STAFF
            $staff->syncPermissions([

                'dashboard.view', 
                'product.view', 
                'customer.view',
                'customer.create',
                'customer.edit', 
                'vendor.view', 
                'stock.in',
                'stock.out',
                'stock.view', 
                'delivery.view',
                'delivery.create',
                'delivery.edit',
                'delivery.print', 
                'dc-return.view',
                'dc-return.create', 
                'delivery.bulk-print',
                'report.stock',
                'report.lowstock',
            ]);

            // ACCOUNTS
            $accounts->syncPermissions([

                'dashboard.view', 
                'purchase.view',
                'purchase.create',
                'purchase.print',
                'purchase.receive', 
                'customer.view',
                'vendor.view', 
                'stock.view', 
                'report.purchase',
                'report.sales',
                'report.stock',
                'report.vendor',
                'report.customer',
                'report.product',
            ]);

            // VIEWER
            $viewer->syncPermissions([ 
                'dashboard.view', 
                'product.view',
                'category.view', 
                'customer.view',
                'vendor.view', 
                'stock.view', 
                'delivery.view', 
                'report.stock',
                'report.product',
                'report.vendor',
                'delivery.bulk-print',
            ]);

            $this->command->info('Roles & Permissions seeded successfully!');
        }
}