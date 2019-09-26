<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalChargeCarrierApisTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('local_charge_carrier_apis', function (Blueprint $table) {
      $table->integer('carrier_id')->unsigned();
      $table->integer('localcharge_id')->unsigned();
      $table->foreign('carrier_id')->references('id')->on('carriers');
      $table->foreign('localcharge_id')->references('id')->on('local_charge_apis')->onDelete('cascade');
    });
  }

  /**
     * Reverse the migrations.
     *
     * @return void
     */
  public function down()
  {
    Schema::dropIfExists('local_charge_carrier_apis');
  }
}
