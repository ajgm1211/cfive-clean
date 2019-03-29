<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('automatic_rate_id')->unsigned();
            $table->foreign('automatic_rate_id')->references('id')->on('automatic_rates')->onDelete('cascade');
            $table->integer('type_id')->unsigned();
            $table->foreign('type_id')->references('id')->on('typedestiny')->onDelete('cascade');
            $table->integer('surcharge_id')->unsigned();
            $table->foreign('surcharge_id')->references('id')->on('surcharges')->onDelete('cascade');
            $table->integer('calculation_type_id')->unsigned();
            $table->foreign('calculation_type_id')->references('id')->on('calculationtype')->onDelete('cascade');
            $table->longText('amount');
            $table->longText('markups');
            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currency')->onDelete('cascade');
            $table->longText('total');
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
        Schema::dropIfExists('charges');
    }
}
