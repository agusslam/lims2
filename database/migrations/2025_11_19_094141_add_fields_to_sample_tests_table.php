<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToSampleTestsTable extends Migration
{
    public function up()
    {
        Schema::table('sample_tests', function (Blueprint $table) {
            // tambahkan kolom yang dibutuhkan (sesuaikan tipe jika perlu)
            $table->text('result')->nullable()->after('parameters_id');
            $table->string('method')->nullable()->after('notes');
            $table->unsignedBigInteger('tested_by')->nullable()->after('method_used');
            $table->timestamp('tested_at')->nullable()->after('tested_by');

            // optional: FK ke users untuk tested_by
            $table->foreign('tested_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('sample_tests', function (Blueprint $table) {
            $table->dropForeign(['tested_by']);
            $table->dropColumn(['result', 'method', 'tested_by', 'tested_at']);
        });
    }
}
