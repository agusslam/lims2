<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sample_reviews')) {
            Schema::create('sample_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sample_id')->constrained('samples')->onDelete('cascade');
                $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
                $table->enum('review_type', ['technical', 'quality']);
                $table->enum('status', ['approved', 'rejected']);
                $table->text('notes')->nullable();
                $table->text('recommendations')->nullable();
                $table->text('correction_required')->nullable();
                $table->timestamp('reviewed_at');
                $table->timestamps();
                
                $table->index(['sample_id', 'review_type']);
                $table->index(['reviewer_id', 'reviewed_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sample_reviews');
    }
};
