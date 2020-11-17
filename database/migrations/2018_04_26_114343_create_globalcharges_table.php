<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalchargesTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('globalcharges', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('surcharge_id')->unsigned();
      $table->integer('typedestiny_id')->unsigned();
      $table->integer('calculationtype_id')->unsigned();
      $table->double('ammount');
      $table->integer('currency_id')->unsigned();
      $table->integer('company_user_id')->unsigned()->nullable();
      $table->foreign('company_user_id')->references('id')->on('company_users')->onDelete('cascade');
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
    Schema::dropIfExists('globalcharges');
  }
}
