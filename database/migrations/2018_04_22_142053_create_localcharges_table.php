<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalchargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('localcharges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('surcharge_id')->unsigned();
            $table->integer('typedestiny_id')->unsigned();
            $table->integer('contract_id')->unsigned();
            $table->integer('calculationtype_id')->unsigned();
            $table->double('ammount');
            $table->integer('currency_id')->unsigned();
            $table->foreign('surcharge_id')->references('id')->on('surcharges');
            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->foreign('calculationtype_id')->references('id')->on('calculationtype');
            $table->foreign('currency_id')->references('id')->on('currency');
            $table->foreign('typedestiny_id')->references('id')->on('typedestiny')->onDelete('cascade');
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
        Schema::dropIfExists('localcharges');
    }
}
