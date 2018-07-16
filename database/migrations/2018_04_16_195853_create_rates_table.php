<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('origin_port')->unsigned();
            $table->integer('destiny_port')->unsigned();
            $table->integer('carrier_id')->unsigned();
            $table->integer('contract_id')->unsigned();
            $table->string('twuenty');
            $table->string('forty');
            $table->string('fortyhc');
            $table->integer('currency_id')->unsigned();
            $table->softDeletes();
            $table->foreign('origin_port')->references('id')->on('harbors');
            $table->foreign('destiny_port')->references('id')->on('harbors');
            $table->foreign('carrier_id')->references('id')->on('carriers');
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currency');
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
        Schema::dropIfExists('rates');
    }
}
