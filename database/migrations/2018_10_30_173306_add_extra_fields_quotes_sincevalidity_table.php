<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldsQuotesSincevalidityTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::table('quotes', function (Blueprint $table) {
      $table->date('since_validity')->nullable()->after('validity');

    });
  }

  /**
     * Reverse the migrations.
     *
     * @return void
     */
  public function down()
  {
    Schema::table('quotes', function (Blueprint $table) {
      $table->dropColumn('since_validity');

    }); 
  }
}
