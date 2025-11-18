<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactPersonAndContactInfoToSampleRequests extends Migration
{
    public function up()
    {
        Schema::table('sample_requests', function (Blueprint $table) {
            // tambahkan kolom yang dibutuhkan — sesuaikan tipe dan posisi jika perlu
            $table->string('contact_person')->nullable()->after('id');
            $table->string('company_name')->nullable()->after('contact_person');
            $table->string('phone')->nullable()->after('company_name');
            $table->string('email')->nullable()->after('phone');
            // jika address sudah ada, jangan tambahkan lagi; kalau belum:
            if (!Schema::hasColumn('sample_requests', 'address')) {
                $table->text('address')->nullable()->after('email');
            }
        });
    }

    public function down()
    {
        Schema::table('sample_requests', function (Blueprint $table) {
            $table->dropColumn(['contact_person', 'company_name', 'phone', 'email']);
            // drop address only if you added it above (be careful)
        });
    }
}
