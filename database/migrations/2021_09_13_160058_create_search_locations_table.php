<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('search_rate_id')->unsigned();
            $table->integer('location_orig')->unsigned()->nullable();
            $table->integer('location_dest')->unsigned()->nullable();
            $table->foreign('search_rate_id')->references('id')->on('search_rates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_locations');
    }
}
