<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractApisTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */



  public function up()
  {
    Schema::create('contract_apis', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name');
      $table->string('number');
      $table->date('validity');
      $table->date('expire');
      $table->enum('status',['publish','draft','incomplete','expired','api'])->default('api');
      $table->string('remarks')->nullable();
      $table->integer('company_user_id')->unsigned()->nullable();
      $table->integer('account_id')->unsigned()->nullable();
      $table->integer('direction_id')->unsigned()->nullable();
      $table->foreign('company_user_id')->references('id')->on('company_users')->onDelete('cascade');  
      $table->foreign('account_id')->references('id')->on('accounts_import_cfcl')->onDelete('cascade');
      $table->foreign('direction_id')->references('id')->on('directions')->onDelete('cascade');
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
    Schema::dropIfExists('contract_apis');
  }
}
