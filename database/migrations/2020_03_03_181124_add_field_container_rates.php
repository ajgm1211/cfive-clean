<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldContainerRates extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::table('rates', function (Blueprint $table){
      $table->json('containers')->nullable()->after('fortyfive');

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