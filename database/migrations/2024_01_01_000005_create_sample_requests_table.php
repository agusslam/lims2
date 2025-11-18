<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sample_requests', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->unique(); // UNEJ202510XXXXXX
            $table->foreignId('customer_id')->constrained();
            $table->enum('status', ['pending', 'registered', 'verified', 'assigned', 'testing', 'review', 'validated', 'certificated', 'completed', 'archived']);
            $table->text('customer_requirements')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('registered_by')->nullable()->constrained('users');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->boolean('feedback_completed')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sample_requests');
    }
};
