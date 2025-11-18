<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add LIMS-specific fields to existing users table
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', [
                    'ADMIN', 'SUPERVISOR', 'SUPERVISOR_ANALYST', 
                    'ANALYST', 'TECH_AUDITOR', 'QUALITY_AUDITOR', 'DEVEL'
                ])->default('ANALYST')->after('password');
                $table->boolean('is_active')->default(true)->after('role');
                $table->json('specializations')->nullable()->after('is_active');
                $table->string('phone', 20)->nullable()->after('specializations');
                $table->timestamp('last_login_at')->nullable()->after('phone');
                $table->timestamp('password_changed_at')->nullable()->after('last_login_at');
                
                $table->index(['role', 'is_active'], 'users_role_active_idx');
            });
        }

        // Create sample_types table if not exists
        if (!Schema::hasTable('sample_types')) {
            Schema::create('sample_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                
                $table->index(['is_active', 'sort_order']);
            });
        }

        // Create test_parameters table if not exists
        if (!Schema::hasTable('test_parameters')) {
            Schema::create('test_parameters', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('unit', 50)->nullable();
                $table->string('category', 100)->default('General');
                $table->text('method')->nullable();
                $table->decimal('price', 10, 2)->default(0.00);
                $table->json('specialist_roles')->nullable();
                $table->boolean('is_required')->default(true);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                
                $table->index(['category', 'is_active']);
                $table->index(['is_active', 'sort_order']);
            });
        }

        // Create sample_requests table if not exists
        if (!Schema::hasTable('sample_requests')) {
            Schema::create('sample_requests', function (Blueprint $table) {
                $table->id();
                $table->string('tracking_code', 20)->unique();
                $table->string('customer_name');
                $table->string('company_name');
                $table->string('phone', 20);
                $table->string('email');
                $table->text('address');
                $table->string('city', 100);
                $table->foreignId('sample_type_id')->constrained('sample_types');
                $table->string('custom_sample_type')->nullable();
                $table->integer('quantity')->default(1);
                $table->text('customer_requirements')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected', 'archived'])->default('pending');
                $table->text('rejection_reason')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('rejected_at')->nullable();
                $table->timestamp('archived_at')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('archived_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                
                $table->index(['status', 'submitted_at']);
                $table->index(['tracking_code']);
            });
        }

        // Create samples table if not exists  
        if (!Schema::hasTable('samples')) {
            Schema::create('samples', function (Blueprint $table) {
                $table->id();
                $table->string('sample_code', 20)->unique();
                $table->foreignId('sample_request_id')->constrained('sample_requests');
                $table->string('tracking_code', 20)->index();
                $table->string('customer_name');
                $table->string('company_name');
                $table->string('phone', 20);
                $table->string('email');
                $table->text('address');
                $table->string('city', 100);
                $table->foreignId('sample_type_id')->constrained('sample_types');
                $table->integer('quantity')->default(1);
                $table->text('customer_requirements')->nullable();
                $table->enum('status', [
                    'pending', 'registered', 'codified', 'assigned', 'testing',
                    'review_tech', 'review_quality', 'validated', 'certificated',
                    'completed', 'archived', 'retesting'
                ])->default('pending');
                $table->integer('revision_count')->default(0);
                
                // Workflow timestamps
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
                
                // User references
                $table->foreignId('registered_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('codified_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('testing_started_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('testing_completed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('tech_reviewed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('quality_reviewed_by')->nullable()->constrained('users')->onDelete('set null');
                
                // Additional fields
                $table->text('codification_notes')->nullable();
                $table->text('special_requirements')->nullable();
                $table->text('testing_notes')->nullable();
                $table->boolean('certificate_required')->default(true);
                $table->text('retesting_reason')->nullable();
                $table->text('retesting_required')->nullable();
                $table->foreignId('parent_sample_id')->nullable()->constrained('samples')->onDelete('set null');
                $table->string('archived_reason')->nullable();
                $table->timestamps();
                
                $table->index(['status', 'created_at']);
                $table->index(['assigned_to', 'status']);
            });
        }

        // Create sample_parameter_requests table if not exists
        if (!Schema::hasTable('sample_parameter_requests')) {
            Schema::create('sample_parameter_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sample_request_id')->constrained('sample_requests')->onDelete('cascade');
                $table->foreignId('test_parameter_id')->constrained('test_parameters')->onDelete('cascade');
                $table->decimal('price', 10, 2)->default(0.00);
                $table->boolean('is_additional')->default(false);
                $table->timestamps();
                
                $table->unique(['sample_request_id', 'test_parameter_id'], 'sample_param_req_unique');
            });
        }

        // Create sample_tests table if not exists
        if (!Schema::hasTable('sample_tests')) {
            Schema::create('sample_tests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sample_id')->constrained('samples')->onDelete('cascade');
                $table->foreignId('test_parameter_id')->constrained('test_parameters')->onDelete('cascade');
                $table->text('result')->nullable();
                $table->text('notes')->nullable();
                $table->string('method_used')->nullable();
                $table->foreignId('tested_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('tested_at')->nullable();
                $table->timestamps();
                
                $table->unique(['sample_id', 'test_parameter_id'], 'sample_test_unique');
                $table->index(['tested_by', 'tested_at']);
            });
        }

        // Create audit_logs table if not exists
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->string('user_id')->nullable();
                $table->string('event');
                $table->string('auditable_type');
                $table->unsignedBigInteger('auditable_id');
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('url')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent')->nullable();
                $table->string('tags')->nullable();
                $table->timestamps();
                
                $table->index(['auditable_type', 'auditable_id']);
                $table->index(['user_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        // Drop tables in reverse order
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('sample_tests');
        Schema::dropIfExists('sample_parameter_requests');
        Schema::dropIfExists('samples');
        Schema::dropIfExists('sample_requests');
        Schema::dropIfExists('test_parameters');
        Schema::dropIfExists('sample_types');

        // Remove added columns from users table
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('users_role_active_idx');
                $table->dropColumn([
                    'role', 'is_active', 'specializations', 'phone', 
                    'last_login_at', 'password_changed_at'
                ]);
            });
        }
    }
};
