<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SampleRequest;
use App\Models\SampleType;
use App\Models\Parameter;
use Carbon\Carbon;

class SampleRequestSeeder extends Seeder
{
    public function run()
    {
        $sampleRequests = [
            [
                'tracking_code' => 'TRK' . date('Ymd') . '001',
                'contact_person' => 'John Doe',
                'company_name' => 'PT. Air Bersih Indonesia',
                'phone' => '0218765432',
                'email' => 'john@airbersih.com',
                'address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'city' => 'Jakarta',
                'sample_type' => 'AIR_MINUM',
                'quantity' => 5,
                'customer_requirements' => 'Pengujian untuk sertifikasi PDAM',
                'status' => 'pending',
                'urgent' => false,
                'submitted_at' => now()->subDays(2),
                'parameters' => ['TURBIDITY', 'PH', 'E_COLI', 'TOTAL_COLIFORM', 'FREE_CHLORINE']
            ],
            [
                'tracking_code' => 'TRK' . date('Ymd') . '002',
                'contact_person' => 'Jane Smith',
                'company_name' => 'CV. Makanan Sehat',
                'phone' => '0217654321',
                'email' => 'jane@makanansehat.com',
                'address' => 'Jl. Gatot Subroto No. 456, Jakarta Selatan',
                'city' => 'Jakarta',
                'sample_type' => 'MAKANAN_OLAHAN',
                'quantity' => 3,
                'customer_requirements' => 'Analisis nutrisi untuk label produk',
                'status' => 'registered',
                'urgent' => true,
                'submitted_at' => now()->subDays(1),
                'parameters' => ['PROTEIN', 'FAT', 'CARBOHYDRATE', 'MOISTURE', 'SALMONELLA']
            ],
            [
                'tracking_code' => 'TRK' . date('Ymd') . '003',
                'contact_person' => 'Ahmad Rahman',
                'company_name' => 'PT. Industri Kimia',
                'phone' => '0213456789',
                'email' => 'ahmad@industrikimia.com',
                'address' => 'Kawasan Industri Pulogadung, Jakarta Timur',
                'city' => 'Jakarta',
                'sample_type' => 'AIR_LIMBAH',
                'quantity' => 10,
                'customer_requirements' => 'Monitoring limbah bulanan sesuai regulasi',
                'status' => 'testing',
                'urgent' => false,
                'submitted_at' => now()->subDays(5),
                'parameters' => ['PH', 'BOD', 'COD', 'TSS', 'LEAD', 'MERCURY']
            ],
            [
                'tracking_code' => 'TRK' . date('Ymd') . '004',
                'contact_person' => 'Siti Nurhaliza',
                'company_name' => 'Beauty Care Indonesia',
                'phone' => '0219876543',
                'email' => 'siti@beautycare.com',
                'address' => 'Mall Taman Anggrek, Jakarta Barat',
                'city' => 'Jakarta',
                'sample_type' => 'KOSMETIK',
                'quantity' => 2,
                'customer_requirements' => 'Uji keamanan produk kosmetik baru',
                'status' => 'completed',
                'urgent' => false,
                'submitted_at' => now()->subDays(10),
                'parameters' => ['LEAD', 'MERCURY', 'CADMIUM', 'MOISTURE']
            ]
        ];

        foreach ($sampleRequests as $requestData) {
            // Check if already exists
            $existing = SampleRequest::where('tracking_code', $requestData['tracking_code'])->first();
            if ($existing) {
                continue;
            }

            $parameterCodes = $requestData['parameters'];
            $sampleTypeCode = $requestData['sample_type'];
            unset($requestData['parameters'], $requestData['sample_type']);

            // Get sample type ID
            $sampleType = SampleType::where('code', $sampleTypeCode)->first();
            if (!$sampleType) {
                continue;
            }
            $requestData['sample_type_id'] = $sampleType->id;

            // Create sample request
            $sampleRequest = SampleRequest::create($requestData);

            // Attach parameters
            $parameters = Parameter::whereIn('code', $parameterCodes)->get();
            if ($parameters->count() > 0) {
                $sampleRequest->parameters()->attach($parameters->pluck('id')->toArray());
            }

            $this->command->info("Created sample request: {$requestData['tracking_code']}");
        }
    }
}
