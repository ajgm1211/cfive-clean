<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsLoadsQuoteV2 extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    //

    Schema::table('quote_v2s', function (Blueprint $table){
      $table->integer('total_quantity')->nullable()->after('equipment');
      $table->float('total_weight')->nullable()->after('total_quantity');
      $table->float('total_volume')->nullable()->after('total_weight');
      //$table->float('chargeable_weight')->nullable()->after('total_volume');
      
      

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
