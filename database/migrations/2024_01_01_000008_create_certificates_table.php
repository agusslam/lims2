<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique();
            $table->foreignId('sample_id')->constrained();
            $table->string('template_type')->default('standard');
            $table->json('certificate_data');
            $table->string('file_path')->nullable();
            $table->enum('status', ['draft', 'issued', 'cancelled', 'expired']);
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users');
            $table->string('digital_signature')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificates');
    }
};
