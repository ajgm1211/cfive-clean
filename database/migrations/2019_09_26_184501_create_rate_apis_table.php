<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRateApisTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('rate_apis', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('origin_port')->unsigned();
      $table->integer('destiny_port')->unsigned();
      $table->integer('carrier_id')->unsigned();
      $table->integer('contract_id')->unsigned();
      $table->string('twuenty')->nullable();
      $table->string('forty')->nullable();
      $table->string('fortyhc')->nullable();
      $table->string('fortynor')->nullable();
      $table->string('fortyfive')->nullable();
      $table->integer('currency_id')->unsigned();
      $table->integer('schedule_type_id')->unsigned()->nullable();
      $table->integer('transit_time')->nullable();;
      $table->string('via')->nullable();
      $table->foreign('origin_port')->references('id')->on('harbors');
      $table->foreign('destiny_port')->references('id')->on('harbors');
      $table->foreign('carrier_id')->references('id')->on('carriers');
      $table->foreign('contract_id')->references('id')->on('contract_apis')->onDelete('cascade');
      $table->foreign('currency_id')->references('id')->on('currency');
      $table->foreign('schedule_type_id')->references('id')->on('schedules');
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
    Schema::dropIfExists('rate_apis');
  }
}
