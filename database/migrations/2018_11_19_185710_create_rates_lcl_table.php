<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatesLclTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('rates_lcl', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('origin_port')->unsigned();
      $table->integer('destiny_port')->unsigned();
      $table->integer('carrier_id')->unsigned();
      $table->integer('contractlcl_id')->unsigned();
      $table->string('uom');
      $table->string('minimum');
      $table->integer('currency_id')->unsigned();
      $table->softDeletes();
      $table->foreign('origin_port')->references('id')->on('harbors');
      $table->foreign('destiny_port')->references('id')->on('harbors');
      $table->foreign('carrier_id')->references('id')->on('carriers');
      $table->foreign('contractlcl_id')->references('id')->on('contracts_lcl')->onDelete('cascade');
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
    Schema::dropIfExists('rates_lcl');
  }
}
