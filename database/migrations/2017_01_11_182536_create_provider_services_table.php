<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProviderServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider_services', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('provider_id');
            $table->integer('service_type_id');
            $table->integer('car_categories_id');
            $table->enum('status', ['active', 'offline','riding']);
            $table->enum('property', ['own', 'rental']);
            $table->string('service_number')->nullable();
            $table->string('service_model')->nullable();
            $table->string('service_color')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provider_services');
    }
}
