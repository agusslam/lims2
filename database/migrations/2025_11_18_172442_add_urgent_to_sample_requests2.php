<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrgentToSampleRequests2 extends Migration
{
    public function up()
    {
        Schema::table('sample_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('sample_requests', 'urgent')) {
                $table->boolean('urgent')->default(false)->after('customer_requirements');
            }
        });
    }

    public function down()
    {
        Schema::table('sample_requests', function (Blueprint $table) {
            if (Schema::hasColumn('sample_requests', 'urgent')) {
                $table->dropColumn('urgent');
            }
        });
    }
}
