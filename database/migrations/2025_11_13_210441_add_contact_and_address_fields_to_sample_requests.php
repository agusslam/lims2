<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sample_requests', function (Blueprint $table) {
            // Periksa dan tambahkan kolom hanya jika belum ada
            if (!Schema::hasColumn('sample_requests', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('id');
            }

            if (!Schema::hasColumn('sample_requests', 'company_name')) {
                $table->string('company_name')->nullable()->after('contact_person');
            }

            if (!Schema::hasColumn('sample_requests', 'phone')) {
                $table->string('phone')->nullable()->after('company_name');
            }

            if (!Schema::hasColumn('sample_requests', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('sample_requests', 'address')) {
                $table->text('address')->nullable()->after('email');
            }

            if (!Schema::hasColumn('sample_requests', 'city')) {
                $table->string('city', 100)->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sample_requests', function (Blueprint $table) {
            // Hati-hati: hanya drop kolom yang kita tambahkan
            if (Schema::hasColumn('sample_requests', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('sample_requests', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('sample_requests', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('sample_requests', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('sample_requests', 'company_name')) {
                $table->dropColumn('company_name');
            }
            if (Schema::hasColumn('sample_requests', 'contact_person')) {
                $table->dropColumn('contact_person');
            }
        });
    }
};
