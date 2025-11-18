<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'locked_at')) {
                $table->timestamp('locked_at')->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('users', 'failed_login_attempts')) {
                $table->integer('failed_login_attempts')->default(0)->after('locked_at');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'locked_at')) {
                $table->dropColumn('locked_at');
            }
            if (Schema::hasColumn('users', 'failed_login_attempts')) {
                $table->dropColumn('failed_login_attempts');
            }
        });
    }
};
