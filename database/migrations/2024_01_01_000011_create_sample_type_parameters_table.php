<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sample_type_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sample_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_parameter_id')->constrained()->onDelete('cascade');
            $table->boolean('is_required')->default(true);
            $table->decimal('default_price', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['sample_type_id', 'test_parameter_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('sample_type_parameters');
    }
};
