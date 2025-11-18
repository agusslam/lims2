<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixFkSampleParameterRequests extends Migration
{
    public function up()
    {
        // 1) drop old FK if exists (use try/catch because name may differ)
        try {
            Schema::table('sample_parameter_requests', function (Blueprint $table) {
                $table->dropForeign(['test_parameter_id']); // if old column name exists with FK
            });
        } catch (\Throwable $e) {
            // ignore if not exist
        }

        // also try dropping any FK on parameter_id that wrongly references test_parameters
        // (we'll drop by enumerating constraints if necessary via raw SQL)
        // 2) ensure parameter_id column exists (nullable to avoid migration fail)
        if (!Schema::hasColumn('sample_parameter_requests', 'parameter_id')) {
            Schema::table('sample_parameter_requests', function (Blueprint $table) {
                $table->unsignedBigInteger('parameter_id')->nullable()->after('test_parameter_id');
            });
        }

        // 3) drop any existing foreign key on parameter_id (if it references wrong table)
        try {
            Schema::table('sample_parameter_requests', function (Blueprint $table) {
                $table->dropForeign(['parameter_id']);
            });
        } catch (\Throwable $e) {
            // ignore
        }

        // 4) create correct FK to parameters
        Schema::table('sample_parameter_requests', function (Blueprint $table) {
            $table->foreign('parameter_id')->references('id')->on('parameters')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('sample_parameter_requests', function (Blueprint $table) {
            try { $table->dropForeign(['parameter_id']); } catch (\Throwable $e) {}
            // optional: drop column if you want
            // $table->dropColumn('parameter_id');
        });
    }
}
