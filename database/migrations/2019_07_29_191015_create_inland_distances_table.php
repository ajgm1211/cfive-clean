<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInlandDistancesTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('inland_distances', function (Blueprint $table) {
      $table->increments('id');
      $table->string('zip');
      $table->string('address');
      $table->string('distance');
      $table->integer('harbor_id')->unsigned();
      $table->integer('province_id')->unsigned()->nullable();
      $table->foreign('harbor_id')->references('id')->on('harbors')->onDelete('cascade');
      $table->foreign('province_id')->references('id')->on('inland_locations')->onDelete('cascade');   

    });
  }

  /**
     * Reverse the migrations.
     *
     * @return void
     */
  public function down()
  {
    Schema::dropIfExists('inland_distances');
  }
}
