<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add your custom columns here
            $table->string('full_name')->after('email');
            $table->enum('role', ['ADMIN', 'SUPERVISOR', 'SUPERVISOR_ANALYST', 'ANALYST', 'TECH_AUDITOR', 'QUALITY_AUDITOR', 'DEVEL'])->after('password');
            $table->json('permissions')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('permissions');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->integer('failed_login_attempts')->default(0)->after('last_login_ip');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'role',
                'permissions',
                'is_active',
                'last_login_at',
                'last_login_ip',
                'failed_login_attempts',
                'locked_until'
            ]);
        });
    }
};