<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInlandsCompanyRestrictionsTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('inlands_company_restrictions', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('company_id')->unsigned();
      $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
      $table->integer('inland_id')->unsigned();
      $table->foreign('inland_id')->references('id')->on('inlands')->onDelete('cascade');
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
    Schema::dropIfExists('inlands_company_restrictions');
  }
}
