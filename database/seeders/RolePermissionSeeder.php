<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    { 
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
            'stock.in','stock.out','stock.view','stock.adjust',

            // Sales / Delivery
            'delivery.create','delivery.view','delivery.edit','delivery.delete','delivery.print',

            // Reports
            'report.stock','report.purchase','report.sales','report.vendor','report.product','report.lowstock',

            // Activity Logs
            'activity.view','activity.delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
 
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $accounts = Role::firstOrCreate(['name' => 'Accounts']);
        $staff = Role::firstOrCreate(['name' => 'Staff']);
        $viewer = Role::firstOrCreate(['name' => 'Viewer']);
  
        $admin->syncPermissions(Permission::all());
 
        $manager->syncPermissions([
            'category.create','category.view','category.edit','category.delete',
            'product.create','product.view','product.edit','product.delete',
            'vendor.create','vendor.view','vendor.edit','vendor.delete',
            'purchase.create','purchase.view','purchase.edit','purchase.delete','purchase.approve','purchase.print',
            'stock.in','stock.out','stock.view','stock.adjust',
            'delivery.create','delivery.view','delivery.edit','delivery.delete','delivery.print',
            'report.stock','report.purchase','report.sales','report.vendor','report.product','report.lowstock',
        ]);
 
        $accounts->syncPermissions([
            'purchase.view','purchase.print',
            'delivery.view',
            'report.purchase','report.sales','report.stock'
        ]);
 
        $staff->syncPermissions([
            'stock.in','stock.out','stock.view',
            'delivery.create','delivery.view',
            'report.stock','report.lowstock'
        ]);
 
        $viewer->syncPermissions([
            'product.view',
            'stock.view',
            'report.stock','report.product'
        ]);

        $this->command->info('Roles & Permissions seeded successfully!');
    }
}