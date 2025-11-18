#!/bin/bash

echo "🧹 LIMS Migration Cleanup Script"
echo "================================"

# List of duplicate migration files to disable
DUPLICATE_MIGRATIONS=(
    "2024_12_10_000000_create_users_table.php"
    "2024_12_10_000002_create_parameters_table.php"
    "2024_12_10_000003_create_sample_types_table.php"
    "2024_12_10_000004_create_sample_requests_table.php"
    "2024_12_10_000006_create_samples_table.php"
    "2024_12_10_000007_create_sample_parameters_table.php"
    "2024_12_10_000008_create_sample_assignments_table.php"
    "2024_12_10_000009_create_sample_results_table.php"
)

echo "Disabling duplicate migrations..."

for migration in "${DUPLICATE_MIGRATIONS[@]}"
do
    if [ -f "$migration" ]; then
        echo "Disabling: $migration"
        # Create backup
        cp "$migration" "${migration}.backup"
        
        # Replace content with empty migration
        cat > "$migration" << 'EOF'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration was disabled - duplicate table exists
        // Original file backed up with .backup extension
    }

    public function down(): void
    {
        // No action needed
    }
};
EOF
        echo "✅ Disabled: $migration (backup created)"
    else
        echo "⚠️  Not found: $migration"
    fi
done

echo ""
echo "✅ Cleanup completed!"
echo "Backups created with .backup extension"
echo "Run 'php artisan migrate:status' to verify"
