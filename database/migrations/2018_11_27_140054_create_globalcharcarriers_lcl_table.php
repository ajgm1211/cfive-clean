<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalcharcarriersLclTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('globalcharcarriers_lcl', function (Blueprint $table) {
      $table->integer('carrier_id')->unsigned();
      $table->integer('globalchargelcl_id')->unsigned();
      $table->foreign('carrier_id')->references('id')->on('carriers');
      $table->foreign('globalchargelcl_id')->references('id')->on('globalcharges_lcl')->onDelete('cascade');
    });
  }

  /**
     * Reverse the migrations.
     *
     * @return void
     */
  public function down()
  {
    Schema::dropIfExists('globalcharcarriers_lcl');
  }
}
