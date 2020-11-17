<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchPortsTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('search_ports', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('search_rate_id')->unsigned();
      $table->integer('port_orig')->unsigned();
      $table->integer('port_dest')->unsigned();
      $table->foreign('search_rate_id')->references('id')->on('search_rates')->onDelete('cascade');
    });
  }

  /**
     * Reverse the migrations.
     *
     * @return void
     */
  public function down()
  {
    Schema::dropIfExists('search_ports');
  }
}
