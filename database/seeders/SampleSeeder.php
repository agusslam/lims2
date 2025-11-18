<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sample;
use App\Models\SampleType;
use App\Models\Parameter;
use App\Models\User;

class SampleSeeder extends Seeder
{
    public function run()
    {
        $analyst = User::where('role', 'ANALYST')->first();
        $supervisor = User::where('role', 'SUPERVISOR')->first();

        $samples = [
            [
                'tracking_code' => 'TRK' . date('Ymd') . '101',
                'sample_code' => 'SPL' . date('Ymd') . '001',
                'customer_name' => 'PT. Pharmaceutical Indo',
                'company_name' => 'PT. Pharmaceutical Indo',
                'phone' => '0218887777',
                'email' => 'lab@pharmaindo.com',
                'address' => 'Jl. HR Rasuna Said, Jakarta',
                'city' => 'Jakarta',
                'sample_type' => 'OBAT_OBATAN',
                'quantity' => 1,
                'customer_requirements' => 'Quality control batch testing',
                'status' => 'registered',
                'registered_by' => $supervisor->id ?? 1,
                'registered_at' => now()->subDays(3),
                'parameters' => ['MOISTURE', 'LEAD', 'MERCURY']
            ],
            [
                'tracking_code' => 'TRK' . date('Ymd') . '102',
                'sample_code' => 'SPL' . date('Ymd') . '002',
                'customer_name' => 'Hotel Grand Indonesia',
                'company_name' => 'Hotel Grand Indonesia',
                'phone' => '0212233445',
                'email' => 'engineering@grandindo.com',
                'address' => 'Jl. MH Thamrin, Jakarta Pusat',
                'city' => 'Jakarta',
                'sample_type' => 'AIR_KOLAM',
                'quantity' => 3,
                'customer_requirements' => 'Weekly pool water monitoring',
                'status' => 'codified',
                'registered_by' => $supervisor->id ?? 1,
                'registered_at' => now()->subDays(2),
                'codified_by' => $analyst->id ?? 2,
                'codified_at' => now()->subDays(1),
                'parameters' => ['PH', 'FREE_CHLORINE', 'E_COLI', 'TOTAL_COLIFORM']
            ]
        ];

        foreach ($samples as $sampleData) {
            // Check if already exists
            $existing = Sample::where('sample_code', $sampleData['sample_code'])->first();
            if ($existing) {
                continue;
            }

            $parameterCodes = $sampleData['parameters'];
            $sampleTypeCode = $sampleData['sample_type'];
            unset($sampleData['parameters'], $sampleData['sample_type']);

            // Get sample type ID
            $sampleType = SampleType::where('code', $sampleTypeCode)->first();
            if (!$sampleType) {
                continue;
            }
            $sampleData['sample_type_id'] = $sampleType->id;

            // Create sample
            $sample = Sample::create($sampleData);

            // Attach parameters
            $parameters = Parameter::whereIn('code', $parameterCodes)->get();
            if ($parameters->count() > 0) {
                $sample->parameters()->attach($parameters->pluck('id')->toArray());
            }

            $this->command->info("Created sample: {$sampleData['sample_code']}");
        }
    }
}
