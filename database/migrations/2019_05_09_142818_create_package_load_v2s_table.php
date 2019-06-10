<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageLoadV2sTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('package_load_v2s', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('type_cargo');
      $table->integer('quantity');
      $table->float('height');
      $table->float('width');
      $table->float('large');
      $table->float('weight');
      $table->float('total_weight');
      $table->float('volume');
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
    Schema::dropIfExists('package_load_v2s');
  }
}
