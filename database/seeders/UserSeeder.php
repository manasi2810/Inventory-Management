<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {

            // Admin User
            $adminUser = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@123.com',
                'contact_no' => '9876543210',
                'address' => 'Admin Address',
                'password' => Hash::make('12345678'),
            ]);

            $adminUser->assignRole('Admin');

            Employee::create([
                'user_id' => $adminUser->id,
                'contact_no' => '9876543210',
                'address' => 'Admin Address',
                'department' => 'Management',
                'designation' => 'Admin',
                'date_of_join' => now(),
                'salary' => 0,
                'profile_photo' => null,
                'resume' => null,
                'certificates' => null,
                'id_proof' => null,
            ]);

            // Manager User
            $managerUser = User::create([
                'name' => 'Manager User',
                'email' => 'manager@123.com',
                'contact_no' => '9876500000',
                'address' => 'Manager Address',
                'password' => Hash::make('12345678'),
            ]);

            $managerUser->assignRole('Manager');

            Employee::create([
                'user_id' => $managerUser->id,
                'contact_no' => '9876500000',
                'address' => 'Manager Address',
                'department' => 'Management',
                'designation' => 'Manager',
                'date_of_join' => now(),
                'salary' => 0,
                'profile_photo' => null,
                'resume' => null,
                'certificates' => null,
                'id_proof' => null,
            ]);

            // Staff User
            $staffUser = User::create([
                'name' => 'Staff User',
                'email' => 'staff@123.com',
                'contact_no' => '9000000000',
                'address' => 'Staff Address',
                'password' => Hash::make('12345678'),
            ]);

            $staffUser->assignRole('Staff');

            Employee::create([
                'user_id' => $staffUser->id,
                'contact_no' => '9000000000',
                'address' => 'Staff Address',
                'department' => 'Operations',
                'designation' => 'Staff',
                'date_of_join' => now(),
                'salary' => 0,
                'profile_photo' => null,
                'resume' => null,
                'certificates' => null,
                'id_proof' => null,
            ]);

            // Accounts User
            $accountsUser = User::create([
                'name' => 'Accounts User',
                'email' => 'accounts@123.com',
                'contact_no' => '9000000001',
                'address' => 'Accounts Address',
                'password' => Hash::make('12345678'),
            ]);

            $accountsUser->assignRole('Accounts');

            // Viewer User
            $viewerUser = User::create([
                'name' => 'Viewer User',
                'email' => 'viewer@123.com',
                'contact_no' => '9000000002',
                'address' => 'Viewer Address',
                'password' => Hash::make('12345678'),
            ]);

            $viewerUser->assignRole('Viewer');

            DB::commit();

            $this->command->info('Users and Employees seeded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Seeding failed: ' . $e->getMessage());
        }
    }
}