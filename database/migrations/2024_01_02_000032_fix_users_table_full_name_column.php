<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if full_name column exists and make it nullable
            if (Schema::hasColumn('users', 'full_name')) {
                $table->string('full_name')->nullable()->change();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'full_name')) {
                $table->string('full_name')->nullable(false)->change();
            }
        });
    }
};
