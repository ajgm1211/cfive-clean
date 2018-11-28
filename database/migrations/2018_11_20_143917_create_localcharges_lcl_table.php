<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalchargesLclTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('localcharges_lcl', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('surcharge_id')->unsigned();
      $table->integer('typedestiny_id')->unsigned();
      $table->integer('contractlcl_id')->unsigned();
      $table->integer('calculationtypelcl_id')->unsigned();
      $table->double('ammount');
      $table->double('minimum');
      $table->softDeletes();
      $table->integer('currency_id')->unsigned();
      $table->foreign('surcharge_id')->references('id')->on('surcharges');
      $table->foreign('contractlcl_id')->references('id')->on('contracts_lcl')->onDelete('cascade');
      $table->foreign('calculationtypelcl_id')->references('id')->on('calculationtypelcl');
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
    Schema::dropIfExists('localcharges_lcl');
  }
}
