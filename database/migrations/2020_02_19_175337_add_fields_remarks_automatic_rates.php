<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsRemarksAutomaticRates extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::table('automatic_rates', function (Blueprint $table) {
      $table->text('remarks_spanish')->nullable()->after('remarks');
      $table->text('remarks_english')->nullable()->after('remarks_spanish');
      $table->text('remarks_portuguese')->nullable()->after('remarks_english');
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
