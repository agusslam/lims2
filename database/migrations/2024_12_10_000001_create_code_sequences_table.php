<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('code_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20); // 'public' or 'internal'
            $table->date('reset_date'); // Date when sequence resets
            $table->integer('current_number')->default(0);
            $table->timestamps();
            
            $table->unique(['type', 'reset_date']);
            $table->index(['type', 'reset_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('code_sequences');
    }
};
