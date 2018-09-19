<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('contracts', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name');
      $table->string('number');
      $table->date('validity');
      $table->date('expire');
      $table->integer('free_days')->nullable();
      $table->enum('status',['publish','draft','incomplete'])->default('draft');
      $table->integer('company_user_id')->unsigned()->nullable();
      $table->foreign('company_user_id')->references('id')->on('company_users')->onDelete('cascade');  
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
    Schema::dropIfExists('contracts');
  }
}
