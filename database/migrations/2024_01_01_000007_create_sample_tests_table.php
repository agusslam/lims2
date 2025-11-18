<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sample_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sample_id')->constrained();
            $table->foreignId('test_parameter_id')->constrained();
            $table->string('result_value')->nullable();
            $table->string('unit')->nullable();
            $table->string('method')->nullable();
            $table->enum('status', ['pending', 'testing', 'completed', 'review_tech', 'review_quality', 'validated', 'rejected']);
            $table->text('notes')->nullable();
            $table->json('instrument_files')->nullable();
            $table->timestamp('tested_at')->nullable();
            $table->foreignId('tested_by')->nullable()->constrained('users');
            $table->foreignId('reviewed_by_tech')->nullable()->constrained('users');
            $table->foreignId('reviewed_by_quality')->nullable()->constrained('users');
            $table->timestamp('tech_review_at')->nullable();
            $table->timestamp('quality_review_at')->nullable();
            $table->text('tech_review_notes')->nullable();
            $table->text('quality_review_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sample_tests');
    }
};
