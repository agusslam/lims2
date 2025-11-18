<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sample_types', function (Blueprint $table) {
            if (!Schema::hasColumn('sample_types', 'category')) {
                $table->string('category')->nullable()->after('description');
            }
        });
    }

    public function down()
    {
        Schema::table('sample_types', function (Blueprint $table) {
            if (Schema::hasColumn('sample_types', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
