<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create pivot table using existing table names
        if (!Schema::hasTable('sample_request_parameters')) {
            Schema::create('sample_request_parameters', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sample_request_id')->constrained('sample_requests')->onDelete('cascade');
                $table->foreignId('test_parameter_id')->constrained('test_parameters')->onDelete('cascade');
                $table->timestamps();
                
                $table->unique(['sample_request_id', 'test_parameter_id'], 'sample_request_parameter_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sample_request_parameters');
    }
};
