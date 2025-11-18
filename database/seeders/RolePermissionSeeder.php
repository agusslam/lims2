<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Define roles with their descriptions and access levels
        $roles = [
            [
                'code' => 'SUPERVISOR',
                'name' => 'Kepala Laboratorium', 
                'description' => 'Full access to all laboratory operations',
                'level' => 1,
                'permissions' => 'all'
            ],
            [
                'code' => 'TECH_AUDITOR',
                'name' => 'Koordinator Teknis',
                'description' => 'Technical review and coordination',
                'level' => 2,
                'permissions' => '1,3,5,8,9'
            ],
            [
                'code' => 'QUALITY_AUDITOR', 
                'name' => 'Koordinator Mutu',
                'description' => 'Quality review and assurance',
                'level' => 2,
                'permissions' => '1,3,5,8,9'
            ],
            [
                'code' => 'SUPERVISOR_ANALYST',
                'name' => 'Penyelia',
                'description' => 'Supervision and senior analyst duties',
                'level' => 3,
                'permissions' => '1,3,5,8,9'
            ],
            [
                'code' => 'ANALYST',
                'name' => 'Teknisi Laboratorium',
                'description' => 'Laboratory testing and analysis',
                'level' => 4,
                'permissions' => '4'
            ],
            [
                'code' => 'ADMIN',
                'name' => 'Customer Service',
                'description' => 'Administrative and customer service',
                'level' => 3,
                'permissions' => '1,2,6,7,8,9'
            ],
            [
                'code' => 'DEVEL',
                'name' => 'Developer',
                'description' => 'System developer with full access',
                'level' => 0,
                'permissions' => 'all'
            ]
        ];

        // Clear existing roles
        DB::table('roles')->truncate();

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'code' => $role['code'],
                'name' => $role['name'],
                'description' => $role['description'],
                'level' => $role['level'],
                'permissions' => $role['permissions'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Define modules and their permissions
        $modules = [
            [
                'id' => 1,
                'name' => 'List New Sample',
                'code' => 'sample_list',
                'description' => 'View and manage new sample requests'
            ],
            [
                'id' => 2,
                'name' => 'Codification',
                'code' => 'codification',
                'description' => 'Sample codification process'
            ],
            [
                'id' => 3,
                'name' => 'Assignment',
                'code' => 'assignment',
                'description' => 'Assign samples to analysts'
            ],
            [
                'id' => 4,
                'name' => 'Testing',
                'code' => 'testing',
                'description' => 'Laboratory testing process'
            ],
            [
                'id' => 5,
                'name' => 'Review',
                'code' => 'review',
                'description' => 'Review test results'
            ],
            [
                'id' => 6,
                'name' => 'Certificates',
                'code' => 'certificates',
                'description' => 'Generate and manage certificates'
            ],
            [
                'id' => 7,
                'name' => 'Invoice',
                'code' => 'invoice',
                'description' => 'Billing and invoicing'
            ],
            [
                'id' => 8,
                'name' => 'Parameter',
                'code' => 'parameter',
                'description' => 'Manage test parameters'
            ],
            [
                'id' => 9,
                'name' => 'User Management',
                'code' => 'user_management',
                'description' => 'Manage system users'
            ],
            [
                'id' => 10,
                'name' => 'System Settings',
                'code' => 'system_settings',
                'description' => 'System configuration'
            ]
        ];

        // Clear existing modules
        DB::table('modules')->truncate();

        foreach ($modules as $module) {
            DB::table('modules')->insert([
                'id' => $module['id'],
                'name' => $module['name'],
                'code' => $module['code'],
                'description' => $module['description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('Roles and modules seeded successfully');
    }
}
