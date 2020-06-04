<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInlandAdditionalKmsTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    
    Schema::disableForeignKeyConstraints();
    Schema::dropIfExists('inland_additional_kms');

    Schema::create('inland_additional_kms', function (Blueprint $table) {
      $table->increments('id');
      $table->double('km_20')->default(0);
      $table->double('km_40')->default(0);
      $table->double('km_40hc')->default(0);
      $table->integer('currency_id')->unsigned();
      $table->integer('inland_id')->unsigned();
      $table->foreign('inland_id')->references('id')->on('inlands')->onDelete('cascade');
      $table->foreign('currency_id')->references('id')->on('currency');
    });
  }

  /**
     * Reverse the migrations.
     *
     * @return void
     */
  public function down()
  {
    Schema::dropIfExists('inland_additional_kms');
  }
}
