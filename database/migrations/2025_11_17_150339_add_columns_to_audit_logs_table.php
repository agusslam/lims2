<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToAuditLogsTable extends Migration
{
    public function up()
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            // Tambah kolom jika belum ada (aman untuk migrasi berulang)
            if (!Schema::hasColumn('audit_logs', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('audit_logs', 'action')) {
                $table->string('action', 150)->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('audit_logs', 'table_name')) {
                $table->string('table_name', 150)->nullable()->after('action');
            }
            if (!Schema::hasColumn('audit_logs', 'record_id')) {
                $table->unsignedBigInteger('record_id')->nullable()->after('table_name');
            }
            if (!Schema::hasColumn('audit_logs', 'old_values')) {
                $table->longText('old_values')->nullable()->after('record_id');
            }
            if (!Schema::hasColumn('audit_logs', 'new_values')) {
                $table->longText('new_values')->nullable()->after('old_values');
            }
            if (!Schema::hasColumn('audit_logs', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('new_values'); // 45 untuk IPv6
            }
            if (!Schema::hasColumn('audit_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
            if (!Schema::hasColumn('audit_logs', 'created_at')) {
                $table->timestamp('created_at')->nullable()->useCurrent()->after('user_agent');
            }
        });
    }

    public function down()
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            // Drop kolom hanya jika ada
            foreach ([
                'created_at','user_agent','ip_address','new_values','old_values',
                'record_id','table_name','action','user_id'
            ] as $col) {
                if (Schema::hasColumn('audit_logs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
}
