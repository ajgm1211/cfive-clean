<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalchargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('globalcharges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('surcharge_id')->unsigned();
            $table->integer('port')->unsigned();
            $table->string('changetype');
            $table->integer('carrier_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('calculationtype_id')->unsigned();
            $table->double('ammount');
            $table->integer('currency_id')->unsigned();
            $table->foreign('surcharge_id')->references('id')->on('surcharge');
            $table->foreign('port')->references('id')->on('harbors');
            $table->foreign('carrier_id')->references('id')->on('carriers');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('calculationtype_id')->references('id')->on('calculationtype');
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
        Schema::dropIfExists('globalcharges');
    }
}
