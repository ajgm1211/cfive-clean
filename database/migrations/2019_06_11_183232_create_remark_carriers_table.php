<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRemarkCarriersTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('remark_carriers', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('carrier_id')->unsigned();
      $table->integer('remark_condition_id')->unsigned();
      $table->foreign('carrier_id')->references('id')->on('carriers')->onDelete('cascade');
      $table->foreign('remark_condition_id')->references('id')->on('remark_conditions')->onDelete('cascade');
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
    Schema::dropIfExists('remark_carriers');
  }
}
