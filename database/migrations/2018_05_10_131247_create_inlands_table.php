<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInlandsTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('inlands', function (Blueprint $table) {
      $table->increments('id');
      $table->string('provider');
      $table->string('type');
      $table->date('validity');
      $table->date('expire');
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
    Schema::dropIfExists('inlands');
  }
}
