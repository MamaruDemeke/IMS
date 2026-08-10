<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ItsmsSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['Information Technology', 'IT', 'Information Technology'],
            ['Human Resources', 'HR', 'Human Resources'],
            ['Finance', 'FIN', 'Finance'],
            ['Sales', 'SAL', 'Sales'],
            ['Warehouse', 'WH', 'Warehouse'],
            ['Store', 'STR', 'Store'],
            ['Procurement', 'PROC', 'Procurement'],
            ['Administration', 'ADM', 'Administration'],
            ['Marketing', 'MKT', 'Marketing'],
            ['Management', 'MGMT', 'Management'],
        ];

        foreach ($departments as $department) {
            Department::query()->firstOrCreate(
                ['code' => $department[1]],
                ['name' => $department[0], 'description' => $department[2]],
            );
        }

        User::query()->firstOrCreate([
            'email' => 'admin@yegnatrading.com',
        ], [
            'name' => 'System Admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'department_id' => Department::query()->where('code', 'ADM')->value('id'),
            'is_active' => true,
        ]);

        User::query()->firstOrCreate([
            'email' => 'itmanager@yegnatrading.com',
        ], [
            'name' => 'IT Manager',
            'password' => Hash::make('password'),
            'role' => 'it_manager',
            'department_id' => Department::query()->where('code', 'IT')->value('id'),
            'is_active' => true,
        ]);

        User::query()->firstOrCreate([
            'email' => 'itofficer@yegnatrading.com',
        ], [
            'name' => 'IT Officer',
            'password' => Hash::make('password'),
            'role' => 'it_officer',
            'department_id' => Department::query()->where('code', 'IT')->value('id'),
            'is_active' => true,
        ]);

        User::query()->firstOrCreate([
            'email' => 'employee@yegnatrading.com',
        ], [
            'name' => 'Employee User',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'department_id' => Department::query()->where('code', 'HR')->value('id'),
            'is_active' => true,
        ]);
    }
}
