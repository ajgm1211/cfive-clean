<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsTermconditionsQuote extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::table('quote_v2s', function (Blueprint $table) {

      //$table->renameColumn('terms_and_conditions','terms_spanish');
      $table->text('terms_english')->nullable()->after('terms_and_conditions');
      $table->text('terms_portuguese')->nullable()->after('terms_english');
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
