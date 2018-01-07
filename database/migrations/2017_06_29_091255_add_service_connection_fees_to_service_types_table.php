<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServiceConnectionFeesToServiceTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->float('connection',8,2)->default(0)->after('distance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->dropColumn('connection');
        });
    }
}
