<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CodeSequenceSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        
        // Initialize public code sequence (monthly reset)
        DB::table('code_sequences')->insertOrIgnore([
            'type' => 'public',
            'reset_date' => $now->startOfMonth()->format('Y-m-d'),
            'current_number' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Initialize internal code sequence (daily reset)
        DB::table('code_sequences')->insertOrIgnore([
            'type' => 'internal', 
            'reset_date' => $now->startOfDay()->format('Y-m-d'),
            'current_number' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
