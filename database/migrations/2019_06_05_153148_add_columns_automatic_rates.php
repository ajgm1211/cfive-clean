<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsAutomaticRates extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::table('automatic_rates', function (Blueprint $table) {
      $table->string('remarks')->nullable()->after('total');
      $table->string('schedule_type')->nullable()->after('remarks');
      $table->integer('transit_time')->nullable()->after('schedule_type');
      $table->string('via')->nullable()->after('transit_time');
    });
  }

  /**
     * Reverse the migrations.
     *
     * @return void
     */
  public function down()
  {
    //
  }
}
