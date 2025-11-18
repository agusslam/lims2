<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigurationSeeder extends Seeder
{
    public function run()
    {
        $configurations = [
            // Company Information
            [
                'key' => 'company.name',
                'value' => 'Laboratorium Pengujian LIMS',
                'description' => 'Nama perusahaan laboratorium',
                'type' => 'text'
            ],
            [
                'key' => 'company.address',
                'value' => 'Jl. Teknologi No. 123, Jakarta Selatan 12345',
                'description' => 'Alamat laboratorium',
                'type' => 'text'
            ],
            [
                'key' => 'company.phone',
                'value' => '021-12345678',
                'description' => 'Nomor telepon laboratorium',
                'type' => 'text'
            ],
            [
                'key' => 'company.email',
                'value' => 'info@lims-lab.com',
                'description' => 'Email laboratorium',
                'type' => 'email'
            ],
            [
                'key' => 'company.website',
                'value' => 'https://lims-lab.com',
                'description' => 'Website laboratorium',
                'type' => 'url'
            ],

            // Certificate Settings
            [
                'key' => 'certificate.template_path',
                'value' => 'certificates/default_template.pdf',
                'description' => 'Path template sertifikat default',
                'type' => 'text'
            ],
            [
                'key' => 'certificate.signature_path',
                'value' => 'certificates/signatures/',
                'description' => 'Path folder tanda tangan digital',
                'type' => 'text'
            ],
            [
                'key' => 'certificate.validity_days',
                'value' => '365',
                'description' => 'Masa berlaku sertifikat (hari)',
                'type' => 'number'
            ],

            // Sample Settings
            [
                'key' => 'sample.auto_assign',
                'value' => 'true',
                'description' => 'Auto assign sampel ke analyst available',
                'type' => 'boolean'
            ],
            [
                'key' => 'sample.max_storage_days',
                'value' => '90',
                'description' => 'Maksimal penyimpanan sampel (hari)',
                'type' => 'number'
            ],
            [
                'key' => 'sample.urgent_notification',
                'value' => 'true',
                'description' => 'Notifikasi untuk sampel urgent',
                'type' => 'boolean'
            ],

            // System Settings
            [
                'key' => 'system.timezone',
                'value' => 'Asia/Jakarta',
                'description' => 'Timezone sistem',
                'type' => 'text'
            ],
            [
                'key' => 'system.date_format',
                'value' => 'd/m/Y',
                'description' => 'Format tanggal sistem',
                'type' => 'text'
            ],
            [
                'key' => 'system.backup_retention',
                'value' => '30',
                'description' => 'Retensi backup database (hari)',
                'type' => 'number'
            ],

            // Notification Settings
            [
                'key' => 'notification.email_enabled',
                'value' => 'true',
                'description' => 'Enable notifikasi email',
                'type' => 'boolean'
            ],
            [
                'key' => 'notification.sms_enabled',
                'value' => 'false',
                'description' => 'Enable notifikasi SMS',
                'type' => 'boolean'
            ]
        ];

        foreach ($configurations as $config) {
            DB::table('configurations')->updateOrInsert(
                ['key' => $config['key']],
                [
                    'value' => $config['value'],
                    'description' => $config['description'],
                    'type' => $config['type'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        $this->command->info('Configuration settings seeded successfully');
    }
}
