<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMintutesPricesToUserrequestPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_request_payments', function (Blueprint $table) { 
            $table->float('minutes',8,2)->default(0)->after('distance');
            $table->float('tips',8,2)->default(0)->after('minutes');
            $table->float('connection',8,2)->default(0)->after('tips');
            $table->float('gross_total',8,2)->default(0)->after('total');
            $table->float('paid',8,2)->default(0)->after('gross_total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_request_payments', function (Blueprint $table) {
            $table->dropColumn('minutes');
            $table->dropColumn('tips');
            $table->dropColumn('connection');
            $table->dropColumn('gross_total');
            $table->dropColumn('paid');
        });
    }
}
