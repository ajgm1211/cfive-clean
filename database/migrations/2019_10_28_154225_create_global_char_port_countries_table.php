<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalCharPortCountriesTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('global_char_port_countries', function (Blueprint $table) {
      $table->integer('port_orig')->unsigned();
      $table->integer('country_dest')->unsigned();
      $table->integer('globalcharge_id')->unsigned();
      $table->foreign('port_orig')->references('id')->on('harbors');
      $table->foreign('country_dest')->references('id')->on('countries');
      $table->foreign('globalcharge_id')->references('id')->on('globalcharges')->onDelete('cascade');
    });
  }

  /**
     * Reverse the migrations.
     *
     * @return void
     */
  public function down()
  {
    Schema::dropIfExists('global_char_port_countries');
  }
}
