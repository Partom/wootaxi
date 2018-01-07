<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTrackingToUserRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_requests', function (Blueprint $table) {
            $table->double('travel_distance', 15, 8)->default(0)->after('d_latitude');
            $table->double('travel_latitude', 15, 8)->default(0)->after('travel_distance');
            $table->double('travel_longitude', 15, 8)->default(0)->after('travel_latitude');
            $table->timestamp('accepted_at')->nullable()->after('assigned_at');
            $table->timestamp('arrived_at')->nullable()->after('accepted_at');
            $table->timestamp('cancelled_at')->nullable()->after('arrived_at');
            $table->smallInteger('arrived_time')->default(0)->nullable()->after('distance');
            $table->smallInteger('traveled_time')->default(0)->nullable()->after('arrived_time');
            $table->smallInteger('cancelled_time')->default(0)->nullable()->after('traveled_time');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_requests', function (Blueprint $table) {
            $table->dropColumn('travel_distance');
            $table->dropColumn('travel_latitude');
            $table->dropColumn('travel_longitude');
            $table->dropColumn('accepted_at');
            $table->dropColumn('arrived_at');
            $table->dropColumn('cancelled_at');
            $table->dropColumn('arrived_time');
            $table->dropColumn('traveled_time');
            $table->dropColumn('cancelled_time');
        });
    }
}
