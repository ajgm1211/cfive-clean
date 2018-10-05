<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldsGlobalchargesTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */


  public function up()
  {
    Schema::table('globalcharges', function (Blueprint $table) {
      $table->date('validity')->nullable()->after('ammount');
      $table->date('expire')->nullable()->after('validity');
    });
  }

  /**
     * Reverse the migrations.
     *
     * @return void
     */
  public function down()
  {
    Schema::table('globalcharges', function($table) {
      $table->dropColumn('validity');
      $table->dropColumn('expire');


    });
  }
}
