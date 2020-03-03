<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalChargeApiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_charges_api', function (Blueprint $table) {
              $table->increments('id');
              $table->integer('surcharge_id')->unsigned();
              $table->integer('typedestiny_id')->unsigned();
              $table->integer('calculationtype_id')->unsigned();
              $table->integer('currency_id')->unsigned();
              $table->double('amount');
              $table->date('validity')->nullable();
              $table->date('expire')->nullable();
              $table->foreign('surcharge_id')->references('id')->on('surcharges');
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
        Schema::dropIfExists('global_charge_api');
    }
}
