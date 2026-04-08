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
            // Admin
            $adminUser = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@hr.com',
                'contact_no' => '9876543210',
                'address' => 'Admin Address',
                'password' => Hash::make('12345678'),
                'role' => 'Admin',
            ]);

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

            // HR
            $hrUser = User::create([
                'name' => 'HR Manager',
                'email' => 'hr@hr.com',
                'contact_no' => '9876500000',
                'address' => 'HR Address',
                'password' => Hash::make('12345678'),
                'role' => 'HR',
            ]);

            Employee::create([
                'user_id' => $hrUser->id,
                'contact_no' => '9876500000',
                'address' => 'HR Address',
                'department' => 'HR',
                'designation' => 'HR Manager',
                'date_of_join' => now(),
                'salary' => 0,
                'profile_photo' => null,
                'resume' => null,
                'certificates' => null,
                'id_proof' => null,
            ]);

            // Employee
            $employeeUser = User::create([
                'name' => 'Test Employee',
                'email' => 'employee@hr.com',
                'contact_no' => '9000000000',
                'address' => 'Employee Address',
                'password' => Hash::make('12345678'),
                'role' => 'Employee',
            ]);

            Employee::create([
                'user_id' => $employeeUser->id,
                'contact_no' => '9000000000',
                'address' => 'Employee Address',
                'department' => 'Operations',
                'designation' => 'Employee',
                'date_of_join' => now(),
                'salary' => 0,
                'profile_photo' => null,
                'resume' => null,
                'certificates' => null,
                'id_proof' => null,
            ]);

            DB::commit();
            $this->command->info('Users and Employees seeded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Seeding failed: ' . $e->getMessage());
        }
    }
}