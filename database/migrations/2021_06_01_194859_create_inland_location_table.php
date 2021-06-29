<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInlandLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inland_location', function (Blueprint $table) {
            $table->increments('id');
            $table->json('json_container')->nullable();
            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currency');
            $table->integer('harbor_id')->unsigned();
            $table->foreign('harbor_id')->references('id')->on('harbors');
            $table->integer('inland_id')->unsigned();
            $table->foreign('inland_id')->references('id')->on('inlands');
            $table->integer('location_id')->unsigned();
            $table->foreign('location_id')->references('id')->on('locations');
            $table->integer('service_id')->unsigned();
            $table->foreign('service_id')->references('id')->on('inland_services');
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
        Schema::dropIfExists('inland_location');
    }
}
