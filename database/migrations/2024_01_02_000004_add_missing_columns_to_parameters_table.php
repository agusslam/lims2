<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('parameters', function (Blueprint $table) {
            if (!Schema::hasColumn('parameters', 'category')) {
                $table->string('category')->after('code');
            }
            if (!Schema::hasColumn('parameters', 'unit')) {
                $table->string('unit')->nullable()->after('description');
            }
            if (!Schema::hasColumn('parameters', 'data_type')) {
                $table->string('data_type')->default('numeric')->after('max_value');
            }
            if (!Schema::hasColumn('parameters', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('data_type');
            }
        });
    }

    public function down()
    {
        Schema::table('parameters', function (Blueprint $table) {
            $columnsToCheck = ['category', 'unit', 'data_type', 'is_active'];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('parameters', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
