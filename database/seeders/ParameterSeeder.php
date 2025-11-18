<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parameter;
use App\Models\SampleType;
use Illuminate\Support\Facades\Schema;

class ParameterSeeder extends Seeder
{
    public function run()
    {
        // Check if required columns exist
        if (!Schema::hasColumn('parameters', 'category')) {
            $this->command->warn('Column "category" does not exist in parameters table. Please run migrations first.');
            return;
        }

        $parameters = [
            // Parameter Fisik - Air
            [
                'name' => 'Kekeruhan',
                'code' => 'TURBIDITY',
                'category' => 'Fisik',
                'unit' => 'NTU',
                'description' => 'Tingkat kekeruhan air',
                'min_value' => 0,
                'max_value' => 1000,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['AIR_MINUM', 'AIR_LIMBAH', 'AIR_TANAH', 'AIR_KOLAM']
            ],
            [
                'name' => 'Warna',
                'code' => 'COLOR',
                'category' => 'Fisik',
                'unit' => 'TCU',
                'description' => 'Tingkat warna air',
                'min_value' => 0,
                'max_value' => 500,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['AIR_MINUM', 'AIR_LIMBAH', 'AIR_TANAH']
            ],
            [
                'name' => 'Total Suspended Solid (TSS)',
                'code' => 'TSS',
                'category' => 'Fisik',
                'unit' => 'mg/L',
                'description' => 'Padatan tersuspensi total',
                'min_value' => 0,
                'max_value' => 10000,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['AIR_LIMBAH', 'AIR_TANAH']
            ],
            
            // Parameter Kimia - Air
            [
                'name' => 'pH',
                'code' => 'PH',
                'category' => 'Kimia',
                'unit' => 'unit pH',
                'description' => 'Derajat keasaman',
                'min_value' => 0,
                'max_value' => 14,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['AIR_MINUM', 'AIR_LIMBAH', 'AIR_TANAH', 'AIR_KOLAM']
            ],
            [
                'name' => 'Dissolved Oxygen (DO)',
                'code' => 'DO',
                'category' => 'Kimia',
                'unit' => 'mg/L',
                'description' => 'Oksigen terlarut',
                'min_value' => 0,
                'max_value' => 20,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['AIR_LIMBAH', 'AIR_TANAH']
            ],
            [
                'name' => 'Biochemical Oxygen Demand (BOD)',
                'code' => 'BOD',
                'category' => 'Kimia',
                'unit' => 'mg/L',
                'description' => 'Kebutuhan oksigen biokimia',
                'min_value' => 0,
                'max_value' => 1000,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['AIR_LIMBAH']
            ],
            [
                'name' => 'Chemical Oxygen Demand (COD)',
                'code' => 'COD',
                'category' => 'Kimia',
                'unit' => 'mg/L',
                'description' => 'Kebutuhan oksigen kimia',
                'min_value' => 0,
                'max_value' => 2000,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['AIR_LIMBAH']
            ],
            [
                'name' => 'Ammonia (NH3-N)',
                'code' => 'AMMONIA',
                'category' => 'Kimia',
                'unit' => 'mg/L',
                'description' => 'Kandungan ammonia',
                'min_value' => 0,
                'max_value' => 100,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['AIR_MINUM', 'AIR_LIMBAH', 'AIR_TANAH']
            ],
            [
                'name' => 'Nitrat (NO3-N)',
                'code' => 'NITRAT',
                'category' => 'Kimia',
                'unit' => 'mg/L',
                'description' => 'Kandungan nitrat',
                'min_value' => 0,
                'max_value' => 100,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['AIR_MINUM', 'AIR_LIMBAH', 'AIR_TANAH']
            ],
            [
                'name' => 'Klorin Bebas',
                'code' => 'FREE_CHLORINE',
                'category' => 'Kimia',
                'unit' => 'mg/L',
                'description' => 'Sisa klorin bebas',
                'min_value' => 0,
                'max_value' => 10,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['AIR_MINUM', 'AIR_KOLAM']
            ],
            
            // Parameter Mikrobiologi - Updated values
            [
                'name' => 'Escherichia Coli',
                'code' => 'E_COLI',
                'category' => 'Mikrobiologi',
                'unit' => 'CFU/100mL',
                'description' => 'Bakteri E. Coli',
                'min_value' => 0,
                'max_value' => 100000,  // Reduced from 1000000
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['AIR_MINUM', 'AIR_LIMBAH', 'AIR_KOLAM']
            ],
            [
                'name' => 'Coliform Total',
                'code' => 'TOTAL_COLIFORM',
                'category' => 'Mikrobiologi',
                'unit' => 'CFU/100mL',
                'description' => 'Total bakteri coliform',
                'min_value' => 0,
                'max_value' => 100000,  // Reduced from 1000000
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['AIR_MINUM', 'AIR_LIMBAH', 'AIR_KOLAM']
            ],
            [
                'name' => 'Salmonella',
                'code' => 'SALMONELLA',
                'category' => 'Mikrobiologi',
                'unit' => 'Positif/Negatif',
                'description' => 'Bakteri Salmonella',
                'data_type' => 'text',
                'is_active' => true,
                'sample_types' => ['MAKANAN_OLAHAN', 'DAGING_TERNAK', 'PRODUK_SUSU']
            ],
            
            // Parameter Makanan
            [
                'name' => 'Protein',
                'code' => 'PROTEIN',
                'category' => 'Nutrisi',
                'unit' => '%',
                'description' => 'Kandungan protein',
                'min_value' => 0,
                'max_value' => 100,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['MAKANAN_OLAHAN', 'DAGING_TERNAK', 'PRODUK_SUSU']
            ],
            [
                'name' => 'Lemak',
                'code' => 'FAT',
                'category' => 'Nutrisi',
                'unit' => '%',
                'description' => 'Kandungan lemak',
                'min_value' => 0,
                'max_value' => 100,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['MAKANAN_OLAHAN', 'DAGING_TERNAK', 'PRODUK_SUSU']
            ],
            [
                'name' => 'Karbohidrat',
                'code' => 'CARBOHYDRATE',
                'category' => 'Nutrisi',
                'unit' => '%',
                'description' => 'Kandungan karbohidrat',
                'min_value' => 0,
                'max_value' => 100,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['MAKANAN_OLAHAN']
            ],
            [
                'name' => 'Kadar Air',
                'code' => 'MOISTURE',
                'category' => 'Fisik',
                'unit' => '%',
                'description' => 'Kandungan air',
                'min_value' => 0,
                'max_value' => 100,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['MAKANAN_OLAHAN', 'KOSMETIK']
            ],
            
            // Parameter Logam Berat
            [
                'name' => 'Timbal (Pb)',
                'code' => 'LEAD',
                'category' => 'Logam Berat',
                'unit' => 'mg/L',
                'description' => 'Kandungan timbal',
                'min_value' => 0,
                'max_value' => 100,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['AIR_MINUM', 'AIR_LIMBAH', 'KOSMETIK']
            ],
            [
                'name' => 'Merkuri (Hg)',
                'code' => 'MERCURY',
                'category' => 'Logam Berat',
                'unit' => 'mg/L',
                'description' => 'Kandungan merkuri',
                'min_value' => 0,
                'max_value' => 10,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['AIR_MINUM', 'AIR_LIMBAH', 'KOSMETIK']
            ],
            [
                'name' => 'Kadmium (Cd)',
                'code' => 'CADMIUM',
                'category' => 'Logam Berat',
                'unit' => 'mg/L',
                'description' => 'Kandungan kadmium',
                'min_value' => 0,
                'max_value' => 10,
                'data_type' => 'numeric',
                'is_active' => true,
                'sample_types' => ['AIR_MINUM', 'AIR_LIMBAH', 'KOSMETIK']
            ]
        ];

        foreach ($parameters as $parameterData) {
            // Check if parameter already exists
            $existing = Parameter::where('code', $parameterData['code'])->first();
            if ($existing) {
                continue;
            }

            $sampleTypeCodes = $parameterData['sample_types'];
            unset($parameterData['sample_types']);
            
            try {
                $parameter = Parameter::create($parameterData);
                
                // Attach sample types
                $sampleTypes = SampleType::whereIn('code', $sampleTypeCodes)->get();
                if ($sampleTypes->count() > 0) {
                    $parameter->sampleTypes()->attach($sampleTypes->pluck('id')->toArray());
                }
                
                $this->command->info("Created parameter: {$parameterData['name']}");
            } catch (\Exception $e) {
                $this->command->error("Failed to create parameter: {$parameterData['name']} - " . $e->getMessage());
            }
        }
    }
}