<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sample_request_id')->constrained();
            
            // Code generation fields (PROJECT.md requirement)
            $table->string('tracking_code', 20)->index(); // UNEJ202510XXXXXX
            $table->string('sample_code')->unique(); // 20251010XXXXXX
            
            // Customer information (from sample_request but denormalized for workflow)
            $table->string('customer_name');
            $table->string('company_name');
            $table->string('phone', 20);
            $table->string('email');
            $table->text('address');
            $table->string('city', 100);
            
            // Sample details
            $table->foreignId('sample_type_id')->constrained();
            $table->string('custom_sample_type')->nullable();
            $table->integer('quantity');
            $table->text('customer_requirements')->nullable();
            
            // Workflow status (PROJECT.md requirement)
            $table->enum('status', [
                'pending', 'registered', 'codified', 'assigned', 'testing', 
                'review_tech', 'review_quality', 'validated', 'certificated', 
                'completed', 'archived', 'retesting'
            ]);
            
            $table->text('description')->nullable();
            
            // Assignment
            $table->foreignId('assigned_analyst_id')->nullable()->constrained('users');
            
            // Workflow timestamps (PROJECT.md requirement)
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('codified_at')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('testing_started_at')->nullable();
            $table->timestamp('testing_completed_at')->nullable();
            $table->timestamp('tech_reviewed_at')->nullable();
            $table->timestamp('quality_reviewed_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('certificated_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            
            // Workflow user references (PROJECT.md requirement)
            $table->foreignId('registered_by')->nullable()->constrained('users');
            $table->foreignId('codified_by')->nullable()->constrained('users');
            $table->foreignId('testing_started_by')->nullable()->constrained('users');
            $table->foreignId('testing_completed_by')->nullable()->constrained('users');
            $table->foreignId('tech_reviewed_by')->nullable()->constrained('users');
            $table->foreignId('quality_reviewed_by')->nullable()->constrained('users');
            
            // Workflow documentation (PROJECT.md requirement)
            $table->text('codification_notes')->nullable();
            $table->text('special_requirements')->nullable();
            $table->text('testing_notes')->nullable();
            $table->boolean('certificate_required')->default(true);
            
            // Retesting workflow (PROJECT.md requirement)
            $table->text('retesting_reason')->nullable();
            $table->text('retesting_required')->nullable();
            $table->foreignId('parent_sample_id')->nullable()->constrained('samples');
            $table->string('archived_reason')->nullable();
            
            // Revision tracking
            $table->json('revision_history')->nullable();
            $table->integer('revision_count')->default(0);
            
            $table->timestamps();
            
            // Performance indexes
            $table->index(['status', 'created_at']);
            $table->index(['tracking_code', 'sample_code']);
            $table->index(['assigned_analyst_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('samples');
    }
};
