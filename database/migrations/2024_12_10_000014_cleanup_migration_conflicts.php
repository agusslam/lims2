<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure all required tables exist with proper structure
        
        // 1. Verify code_sequences table exists
        if (!Schema::hasTable('code_sequences')) {
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

        // 2. Verify sample_request_parameters pivot table exists
        if (!Schema::hasTable('sample_request_parameters')) {
            Schema::create('sample_request_parameters', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sample_request_id')->constrained('sample_requests')->onDelete('cascade');
                $table->foreignId('test_parameter_id')->constrained('test_parameters')->onDelete('cascade');
                $table->timestamps();
                
                $table->unique(['sample_request_id', 'test_parameter_id'], 'sample_request_parameter_unique');
            });
        }

        // 3. Verify sample_files table exists
        if (!Schema::hasTable('sample_files')) {
            Schema::create('sample_files', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sample_id')->constrained('samples')->onDelete('cascade');
                $table->string('file_name');
                $table->string('file_path');
                $table->integer('file_size');
                $table->string('file_type', 100);
                $table->text('description')->nullable();
                $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
                $table->timestamp('uploaded_at');
                $table->timestamps();
                
                $table->index(['sample_id', 'uploaded_at']);
            });
        }

        // 4. Verify sample_reviews table exists  
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

        // 5. Add missing indexes for performance
        if (Schema::hasTable('samples')) {
            $indexes = [
                ['tracking_code'],
                ['sample_code'], 
                ['status', 'created_at'],
                ['assigned_analyst_id', 'status']
            ];
            
            foreach ($indexes as $index) {
                $indexName = 'samples_' . implode('_', $index) . '_index';
                if (!$this->indexExists('samples', $indexName)) {
                    Schema::table('samples', function (Blueprint $table) use ($index) {
                        $table->index($index);
                    });
                }
            }
        }

        // 6. Add missing indexes for sample_requests
        if (Schema::hasTable('sample_requests')) {
            $indexes = [
                ['tracking_code'],
                ['status', 'created_at']
            ];
            
            foreach ($indexes as $index) {
                $indexName = 'sample_requests_' . implode('_', $index) . '_index';
                if (!$this->indexExists('sample_requests', $indexName)) {
                    Schema::table('sample_requests', function (Blueprint $table) use ($index) {
                        $table->index($index);
                    });
                }
            }
        }
    }

    public function down(): void
    {
        // Drop new tables in reverse order
        Schema::dropIfExists('sample_reviews');
        Schema::dropIfExists('sample_files');
        Schema::dropIfExists('sample_request_parameters');
        Schema::dropIfExists('code_sequences');
    }

    /**
     * Check if index exists on table
     */
    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$index]);
        return !empty($indexes);
    }
};
