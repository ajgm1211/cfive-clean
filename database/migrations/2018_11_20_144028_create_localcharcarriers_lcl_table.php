<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalcharcarriersLclTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('localcharcarriers_lcl', function (Blueprint $table) {
      $table->integer('carrier_id')->unsigned();
      $table->integer('localchargelcl_id')->unsigned();
      $table->foreign('carrier_id')->references('id')->on('carriers');
      $table->foreign('localchargelcl_id')->references('id')->on('localcharges_lcl')->onDelete('cascade');
    });
  }

  /**
     * Reverse the migrations.
     *
     * @return void
     */
  public function down()
  {
    Schema::dropIfExists('localcharcarriers_lcl');
  }
}
