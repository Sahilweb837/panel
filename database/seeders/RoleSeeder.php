<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['role_name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'Full system administrator.'],
            ['role_name' => 'Root Admin', 'slug' => 'root-admin', 'description' => 'Root-level institute administrator.'],
            ['role_name' => 'Admin', 'slug' => 'admin', 'description' => 'Institute operations administrator.'],
            ['role_name' => 'Staff', 'slug' => 'staff', 'description' => 'Teaching or office staff.'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
