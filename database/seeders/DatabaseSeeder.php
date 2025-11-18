<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use App\Models\SampleType;
use App\Models\TestParameter;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting LIMS database seeding...');

        // 1. System Foundation
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // 2. Basic Master Data
        $this->call([
            UserSeeder::class,
            SampleTypeSeeder::class,
            ParameterSeeder::class,
        ]);

        // 3. Transactional Data
        $this->call([
            SampleRequestSeeder::class,
            SampleSeeder::class,
        ]);

        // 4. System Configuration
        $this->call([
            ConfigurationSeeder::class,
        ]);

        $this->command->info('LIMS database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('=== USER ACCOUNTS ===');
        $this->command->info('Developer (Full): pandu@lims.com / dev123456');
        $this->command->info('Kepala Lab (Full): trihandoyo@lims.com / supervisor123');
        $this->command->info('Customer Service: dinda@lims.com / admin123');
        $this->command->info('Penyelia: arin@lims.com / supervisor123');
        $this->command->info('Teknisi: aryo@lims.com, purwo@lims.com, rizki@lims.com, wulan@lims.com / analyst123');
        $this->command->info('Koordinator Teknis: fafan@lims.com / qc123456');
        $this->command->info('Koordinator Mutu: parawita@lims.com / qa123456');
    }
}