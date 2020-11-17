<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtraFieldsQuotesTermTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::table('quotes', function (Blueprint $table) {
      $table->text('term')->nullable()->after('status_quote_id');

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
      $table->dropColumn('term');

    });
  }
}