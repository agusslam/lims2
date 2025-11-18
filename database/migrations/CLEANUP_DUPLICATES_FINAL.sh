#!/bin/bash

echo "🧹 LIMS Migration Final Cleanup Script"
echo "======================================"

# List of ALL duplicate migration files to disable
DUPLICATE_MIGRATIONS=(
    "2024_12_10_000000_create_users_table.php"
    "2024_12_10_000002_create_parameters_table.php"
    "2024_12_10_000003_create_sample_types_table.php"
    "2024_12_10_000004_create_sample_requests_table.php"
    "2024_12_10_000006_create_samples_table.php"
    "2024_12_10_000007_create_sample_parameters_table.php"
    "2024_12_10_000008_create_sample_assignments_table.php"
    "2024_12_10_000009_create_sample_results_table.php"
    "2024_12_10_000012_create_audit_logs_table.php"
)

echo "Disabling duplicate migrations..."
echo "Found ${#DUPLICATE_MIGRATIONS[@]} duplicate migration files"

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
echo "✅ Final cleanup completed!"
echo "Total duplicates disabled: ${#DUPLICATE_MIGRATIONS[@]}"
echo ""
echo "🔧 Next steps:"
echo "1. Run: php artisan migrate:status"
echo "2. Run: php artisan migrate"
echo "3. Verify tables with: php artisan tinker"
echo ""
echo "✅ SAFE MIGRATIONS REMAINING:"
echo "- 2024_12_10_000001_create_code_sequences_table.php"
echo "- 2024_12_10_000005_create_sample_request_parameters_table.php"
echo "- 2024_12_10_000010_create_sample_files_table.php"
echo "- 2024_12_10_000011_create_sample_reviews_table.php"
echo "- 2024_12_10_000013_add_lims_fields_to_existing_tables.php"
echo "- 2024_12_10_000014_cleanup_migration_conflicts.php"
