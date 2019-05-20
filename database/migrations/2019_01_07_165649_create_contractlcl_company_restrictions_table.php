<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractlclCompanyRestrictionsTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('contractlcl_company_restrictions', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('company_id')->unsigned();
      $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
      $table->integer('contractlcl_id')->unsigned();
      $table->foreign('contractlcl_id')->references('id')->on('contracts_lcl')->onDelete('cascade');
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
    Schema::dropIfExists('contractlcl_company_restrictions');
  }
}
