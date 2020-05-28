<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransitTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transit_times', function (Blueprint $table) {
            
            $table->increments('id');
            $table->integer('origin_id')->nullable()->unsigned();
            $table->integer('destination_id')->nullable()->unsigned();
            $table->integer('carrier_id')->nullable()->unsigned();
            $table->integer('service_id')->nullable()->unsigned();            
            $table->string('transit_time');
            $table->string('via');
            $table->timestamps();

            $table->foreign('origin_id')->references('id')->on('harbors');
            $table->foreign('destination_id')->references('id')->on('harbors');
            $table->foreign('carrier_id')->references('id')->on('carriers');
            $table->foreign('service_id')->references('id')->on('destination_types');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transit_times');
    }
}
