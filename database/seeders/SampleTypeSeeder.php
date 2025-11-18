<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SampleType;
use Illuminate\Support\Facades\Schema;

class SampleTypeSeeder extends Seeder
{
    public function run()
    {
        // Check if required columns exist
        if (!Schema::hasColumn('sample_types', 'category')) {
            $this->command->warn('Column "category" does not exist in sample_types table. Please run migrations first.');
            return;
        }

        $sampleTypes = [
            // Air dan Lingkungan
            [
                'name' => 'Air Minum',
                'code' => 'AIR_MINUM',
                'category' => 'Air dan Lingkungan',
                'description' => 'Sampel air untuk konsumsi manusia',
                'is_active' => true
            ],
            [
                'name' => 'Air Limbah',
                'code' => 'AIR_LIMBAH',
                'category' => 'Air dan Lingkungan',
                'description' => 'Sampel air limbah industri atau domestik',
                'is_active' => true
            ],
            [
                'name' => 'Air Tanah',
                'code' => 'AIR_TANAH',
                'category' => 'Air dan Lingkungan',
                'description' => 'Sampel air dari sumur atau sumber air tanah',
                'is_active' => true
            ],
            [
                'name' => 'Air Kolam Renang',
                'code' => 'AIR_KOLAM',
                'category' => 'Air dan Lingkungan',
                'description' => 'Sampel air untuk kolam renang dan rekreasi',
                'is_active' => true
            ],
            
            // Makanan dan Minuman
            [
                'name' => 'Makanan Olahan',
                'code' => 'MAKANAN_OLAHAN',
                'category' => 'Makanan dan Minuman',
                'description' => 'Produk makanan yang telah diproses',
                'is_active' => true
            ],
            [
                'name' => 'Minuman Kemasan',
                'code' => 'MINUMAN_KEMASAN',
                'category' => 'Makanan dan Minuman',
                'description' => 'Minuman dalam kemasan siap konsumsi',
                'is_active' => true
            ],
            [
                'name' => 'Produk Susu',
                'code' => 'PRODUK_SUSU',
                'category' => 'Makanan dan Minuman',
                'description' => 'Susu dan produk turunannya',
                'is_active' => true
            ],
            [
                'name' => 'Daging dan Hasil Ternak',
                'code' => 'DAGING_TERNAK',
                'category' => 'Makanan dan Minuman',
                'description' => 'Daging segar dan produk olahannya',
                'is_active' => true
            ],
            
            // Kosmetik dan Farmasi
            [
                'name' => 'Kosmetik',
                'code' => 'KOSMETIK',
                'category' => 'Kosmetik dan Farmasi',
                'description' => 'Produk perawatan kulit dan kecantikan',
                'is_active' => true
            ],
            [
                'name' => 'Obat-obatan',
                'code' => 'OBAT_OBATAN',
                'category' => 'Kosmetik dan Farmasi',
                'description' => 'Produk farmasi dan suplemen',
                'is_active' => true
            ],
            
            // Kimia dan Material
            [
                'name' => 'Bahan Kimia',
                'code' => 'BAHAN_KIMIA',
                'category' => 'Kimia dan Material',
                'description' => 'Bahan kimia industri dan laboratorium',
                'is_active' => true
            ],
            [
                'name' => 'Material Konstruksi',
                'code' => 'MATERIAL_KONSTRUKSI',
                'category' => 'Kimia dan Material',
                'description' => 'Bahan bangunan dan konstruksi',
                'is_active' => true
            ]
        ];

        foreach ($sampleTypes as $sampleType) {
            // Check if sample type already exists
            $existing = SampleType::where('code', $sampleType['code'])->first();
            if ($existing) {
                continue;
            }

            try {
                SampleType::create($sampleType);
            } catch (\Exception $e) {
                $this->command->error("Failed to create sample type: {$sampleType['name']} - " . $e->getMessage());
            }
        }
    }
}
