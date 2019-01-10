<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractAddonsTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('contract_addons', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('base_port')->unsigned();
      $table->integer('port')->unsigned();
      $table->integer('carrier_id')->unsigned();
      $table->integer('contract_id')->unsigned();
      $table->string('twuenty_addons');
      $table->string('forty_addons');
      $table->string('fortyhc_addons');
      $table->string('fortynor_addons');
      $table->string('fortyfive_addons');
      $table->integer('currency_id')->unsigned();
      $table->softDeletes();
      $table->foreign('base_port')->references('id')->on('harbors');
      $table->foreign('port')->references('id')->on('harbors');
      $table->foreign('carrier_id')->references('id')->on('carriers');
      $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
      $table->foreign('currency_id')->references('id')->on('currency');
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
    Schema::dropIfExists('contract_addons');
  }
}
