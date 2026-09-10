<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@mtbilling.test'],
            [
                'name' => 'MT Administrator',
                'password' => Hash::make('Admin@12345'),
                'role' => 'admin',
            ]
        );

        $employee = User::updateOrCreate(
            ['email' => 'employee@mtbilling.test'],
            [
                'name' => 'John Employee',
                'password' => Hash::make('Employee@12345'),
                'role' => 'employee',
            ]
        );

        Employee::updateOrCreate(
            ['user_id' => $employee->id],
            [
                'employee_code' => 'EMP001',
                'phone' => '9876543210',
                'department' => 'Sales',
                'designation' => 'Billing Executive',
                'is_active' => true,
            ]
        );
    }
}