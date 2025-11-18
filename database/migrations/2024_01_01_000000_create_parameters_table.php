<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('parameters')) {
            Schema::create('parameters', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('category');
                $table->text('description')->nullable();
                $table->string('unit')->nullable();
                $table->decimal('min_value', 15, 4)->nullable();
                $table->decimal('max_value', 15, 4)->nullable();
                $table->string('data_type')->default('numeric');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('parameters');
    }
};
