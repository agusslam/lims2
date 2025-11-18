<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Check if required columns exist
        $requiredColumns = ['role', 'department', 'phone', 'is_active'];
        foreach ($requiredColumns as $column) {
            if (!Schema::hasColumn('users', $column)) {
                $this->command->warn("Column '{$column}' does not exist in users table. Please run migrations first.");
                return;
            }
        }

        $users = [
            // Developer - Full Access
            [
                'name' => 'Pandu',
                'full_name' => 'Pandu Developer',
                'username' => 'pandu',
                'email' => 'pandu@lims.com',
                'password' => Hash::make('dev123456'),
                'role' => 'DEVEL',
                'department' => 'IT Development',
                'phone' => '081234567890',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            
            // Kepala Laboratorium - Full Access
            [
                'name' => 'Tri Handoyo',
                'full_name' => 'Tri Handoyo, S.T., M.Sc.',
                'username' => 'trihandoyo',
                'email' => 'trihandoyo@lims.com',
                'password' => Hash::make('supervisor123'),
                'role' => 'SUPERVISOR',
                'department' => 'Laboratory Management',
                'phone' => '081234567891',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            
            // Customer Service - Administration
            [
                'name' => 'Dinda',
                'full_name' => 'Dinda Sari',
                'username' => 'dinda',
                'email' => 'dinda@lims.com',
                'password' => Hash::make('admin123'),
                'role' => 'ADMIN',
                'department' => 'Customer Service',
                'phone' => '081234567892',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            
            // Penyelia - Supervision + Testing
            [
                'name' => 'Arin',
                'full_name' => 'Arin Supervisor',
                'username' => 'arin',
                'email' => 'arin@lims.com',
                'password' => Hash::make('supervisor123'),
                'role' => 'SUPERVISOR_ANALYST',
                'department' => 'Laboratory Analysis',
                'phone' => '081234567893',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            
            // Teknisi Laboratorium - Testing Only
            [
                'name' => 'Aryo',
                'full_name' => 'Aryo Technician',
                'username' => 'aryo',
                'email' => 'aryo@lims.com',
                'password' => Hash::make('analyst123'),
                'role' => 'ANALYST',
                'department' => 'Laboratory Testing',
                'phone' => '081234567894',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Purwo',
                'full_name' => 'Purwo Analyst',
                'username' => 'purwo',
                'email' => 'purwo@lims.com',
                'password' => Hash::make('analyst123'),
                'role' => 'ANALYST',
                'department' => 'Laboratory Testing',
                'phone' => '081234567895',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Rizki',
                'full_name' => 'Rizki Laboratory',
                'username' => 'rizki',
                'email' => 'rizki@lims.com',
                'password' => Hash::make('analyst123'),
                'role' => 'ANALYST',
                'department' => 'Laboratory Testing',
                'phone' => '081234567896',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Wulan',
                'full_name' => 'Wulan Technician',
                'username' => 'wulan',
                'email' => 'wulan@lims.com',
                'password' => Hash::make('analyst123'),
                'role' => 'ANALYST',
                'department' => 'Laboratory Testing',
                'phone' => '081234567897',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            
            // Koordinator Teknis - QC Access
            [
                'name' => 'Fafan',
                'full_name' => 'Fafan Technical Coordinator',
                'username' => 'fafan',
                'email' => 'fafan@lims.com',
                'password' => Hash::make('qc123456'),
                'role' => 'TECH_AUDITOR',
                'department' => 'Technical Coordination',
                'phone' => '081234567898',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            
            // Koordinator Mutu - QA Access
            [
                'name' => 'Parawita',
                'full_name' => 'Parawita Quality Coordinator',
                'username' => 'parawita',
                'email' => 'parawita@lims.com',
                'password' => Hash::make('qa123456'),
                'role' => 'QUALITY_AUDITOR',
                'department' => 'Quality Assurance',
                'phone' => '081234567899',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            // Check if user already exists
            $existingUser = User::where('email', $userData['email'])->first();
            if (!$existingUser) {
                try {
                    // If full_name column doesn't exist, use name as full_name
                    if (!Schema::hasColumn('users', 'full_name')) {
                        unset($userData['full_name']);
                    }
                    
                    User::create($userData);
                    $this->command->info("Created user: {$userData['name']} ({$userData['role']})");
                } catch (\Exception $e) {
                    $this->command->error("Failed to create user: {$userData['name']} - " . $e->getMessage());
                }
            }
        }
    }
}
