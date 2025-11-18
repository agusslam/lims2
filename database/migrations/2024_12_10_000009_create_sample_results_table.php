<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This functionality already exists in sample_tests table
        // We'll use the existing sample_tests table for storing results
    }

    public function down(): void
    {
        // No action needed
    }
};