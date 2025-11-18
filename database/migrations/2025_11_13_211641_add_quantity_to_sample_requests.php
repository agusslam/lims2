<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sample_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('sample_requests', 'quantity')) {
                // quantity biasanya integer minimal 1 — sesuaikan jika perlu decimal
                $table->unsignedInteger('quantity')->default(1)->after('sample_type_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sample_requests', function (Blueprint $table) {
            if (Schema::hasColumn('sample_requests', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }
};
