<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalchargesLclTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('globalcharges_lcl', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('surcharge_id')->unsigned();
      $table->integer('typedestiny_id')->unsigned();
      $table->integer('calculationtypelcl_id')->unsigned();
      $table->double('ammount');
      $table->double('minimum');
      $table->date('validity')->nullable();
      $table->date('expire')->nullable();
      $table->integer('currency_id')->unsigned();
      $table->integer('company_user_id')->unsigned()->nullable();
      $table->foreign('company_user_id')->references('id')->on('company_users')->onDelete('cascade');
      $table->foreign('surcharge_id')->references('id')->on('surcharges');
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
    Schema::dropIfExists('globalcharges_lcl');
  }
}
