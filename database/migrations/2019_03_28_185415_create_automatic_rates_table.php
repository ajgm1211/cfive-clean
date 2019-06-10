<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutomaticRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('automatic_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('quote_id')->unsigned();
            $table->foreign('quote_id')->references('id')->on('quote_v2s')->onDelete('cascade');
            $table->string('contract');
            $table->date('validity_start');
            $table->date('validity_end');
            $table->integer('origin_port_id')->unsigned()->nullable();
            $table->foreign('origin_port_id')->references('id')->on('harbors')->onDelete('cascade');
            $table->integer('destination_port_id')->unsigned()->nullable();
            $table->foreign('destination_port_id')->references('id')->on('harbors')->onDelete('cascade');
            $table->integer('carrier_id')->unsigned();
            $table->foreign('carrier_id')->references('id')->on('carriers')->onDelete('cascade');
            $table->json('rates');
            $table->json('markups');
            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currency')->onDelete('cascade');
            $table->json('total');
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
        Schema::dropIfExists('automatic_rates');
    }
}
