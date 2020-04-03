<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('schedules', function (Blueprint $table) {
      $table->increments('id');
      $table->string('vessel');
      $table->date('etd');
      $table->string('transit_time');
      $table->string('type');
      $table->date('eta');
      $table->integer('quote_id')->unsigned();
      $table->foreign('quote_id')->references('id')->on('quote_v2s')->onDelete('cascade');
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
    Schema::dropIfExists('schedules');
  }
}
