<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('parameters', function (Blueprint $table) {
            // Change decimal precision to handle larger values
            $table->decimal('min_value', 15, 4)->nullable()->change();
            $table->decimal('max_value', 15, 4)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('parameters', function (Blueprint $table) {
            $table->decimal('min_value', 10, 4)->nullable()->change();
            $table->decimal('max_value', 10, 4)->nullable()->change();
        });
    }
};
