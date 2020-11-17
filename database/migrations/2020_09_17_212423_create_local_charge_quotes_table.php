<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalChargeQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_charge_quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->json('price')->nullable();
            $table->json('profit')->nullable();
            $table->integer('surcharge_id')->unsigned()->nullable();
            $table->foreign('surcharge_id')->references('id')->on('surcharges');
            $table->integer('calculation_type_id')->unsigned()->nullable();
            $table->foreign('calculation_type_id')->references('id')->on('calculationtype');
            $table->integer('currency_id')->unsigned()->nullable();
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
        Schema::dropIfExists('local_charge_quotes');
    }
}
