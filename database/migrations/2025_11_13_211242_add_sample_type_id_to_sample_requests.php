<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sample_requests', function (Blueprint $table) {
            // tambahkan kolom sample_type_id jika belum ada
            if (!Schema::hasColumn('sample_requests', 'sample_type_id')) {
                // tipe sesuai kebutuhan: biasanya big integer unsigned jika mengacu ke id di sample_types
                $table->unsignedBigInteger('sample_type_id')->nullable()->after('city');

                // jika tabel sample_types ada, tambahkan foreign key (opsional)
                if (Schema::hasTable('sample_types')) {
                    $table->foreign('sample_type_id')
                          ->references('id')
                          ->on('sample_types')
                          ->onDelete('set null')
                          ->onUpdate('cascade');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('sample_requests', function (Blueprint $table) {
            // drop foreign key jika ada, lalu kolom
            if (Schema::hasColumn('sample_requests', 'sample_type_id')) {
                // drop foreign key safely (name may vary across DBs)
                try {
                    $table->dropForeign(['sample_type_id']);
                } catch (\Throwable $e) {
                    // ignore if FK doesn't exist
                }
                $table->dropColumn('sample_type_id');
            }
        });
    }
};
