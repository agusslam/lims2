<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('parameter_sample_type')) {
            Schema::create('parameter_sample_type', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parameter_id')->constrained()->onDelete('cascade');
                $table->foreignId('sample_type_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                
                $table->unique(['parameter_id', 'sample_type_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('parameter_sample_type');
    }
};
