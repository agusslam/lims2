<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration is not needed - audit_logs table already exists from 2024_01_01_000010_create_audit_logs_table.php
        // We'll use the existing audit_logs table structure
    }

    public function down(): void
    {
        // No action needed
    }
};