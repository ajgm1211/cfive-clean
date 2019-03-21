<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalcharportsLclTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('globalcharports_lcl', function (Blueprint $table) {
      $table->integer('port_orig')->unsigned();
      $table->integer('port_dest')->unsigned();
      $table->integer('globalchargelcl_id')->unsigned();
      $table->foreign('port_orig')->references('id')->on('harbors');
      $table->foreign('port_dest')->references('id')->on('harbors');
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
    Schema::dropIfExists('globalcharports_lcl');
  }
}
