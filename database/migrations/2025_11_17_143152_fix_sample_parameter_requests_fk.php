<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixSampleParameterRequestsFk extends Migration
{
    public function up()
    {
        Schema::table('sample_parameter_requests', function (Blueprint $table) {
            // drop FK lama (menggunakan nama kolom)
            // pastikan kolom test_parameter_id ada
            if (Schema::hasColumn('sample_parameter_requests', 'test_parameter_id')) {
                // drop foreign key - this uses column name, Laravel will resolve constraint name
                $table->dropForeign(['test_parameter_id']);
                // rename column ke parameter_id
                $table->renameColumn('test_parameter_id', 'parameter_id');
                // pastikan tipe sesuai parent. Jika parent id adalah unsignedBigInteger:
                // NOTE: change() memerlukan doctrine/dbal
                $table->unsignedBigInteger('parameter_id')->change();
                // buat foreign key baru ke tabel parameters
                $table->foreign('parameter_id')->references('id')->on('parameters')->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('sample_parameter_requests', function (Blueprint $table) {
            if (Schema::hasColumn('sample_parameter_requests', 'parameter_id')) {
                $table->dropForeign(['parameter_id']);
                $table->renameColumn('parameter_id', 'test_parameter_id');
                // optionally change type back if needed
            }
        });
    }
}
