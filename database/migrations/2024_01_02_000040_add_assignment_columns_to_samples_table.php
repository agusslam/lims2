<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('samples', function (Blueprint $table) {
            if (!Schema::hasColumn('samples', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->after('registered_at')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('samples', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('assigned_to');
            }
            if (!Schema::hasColumn('samples', 'assigned_by')) {
                $table->foreignId('assigned_by')->nullable()->after('assigned_at')->constrained('users')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('samples', function (Blueprint $table) {
            $columnsToCheck = ['assigned_to', 'assigned_at', 'assigned_by'];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('samples', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
