<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['HR', 'HR', 'Human Resources'],
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
    }
}
