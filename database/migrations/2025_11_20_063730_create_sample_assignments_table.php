<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sample_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->unsignedBigInteger('analyst_id');
            $table->unsignedBigInteger('assigned_by');

            $table->string('priority')->default('normal'); // low|normal|high|urgent
            $table->date('deadline')->nullable();
            $table->text('assignment_notes')->nullable();

            $table->timestamp('assigned_at')->nullable();
            $table->string('status')->default('assigned'); // kalau mau

            $table->timestamps();

            // Foreign keys (opsional tapi bagus)
            $table->foreign('sample_id')->references('id')->on('samples')->onDelete('cascade');
            $table->foreign('analyst_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sample_assignments');
    }
};
