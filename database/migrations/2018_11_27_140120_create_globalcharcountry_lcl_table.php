<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalcharcountryLclTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('globalcharcountry_lcl', function (Blueprint $table) {
      $table->integer('country_orig')->unsigned();
      $table->integer('country_dest')->unsigned();
      $table->integer('globalchargelcl_id')->unsigned();
      $table->foreign('country_orig')->references('id')->on('countries');
      $table->foreign('country_dest')->references('id')->on('countries');
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
    Schema::dropIfExists('globalcharcountry_lcl');
  }
}
