<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyUsersTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('company_users', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name');
      $table->string('address')->nullable();
      $table->string('phone')->nullable();
      $table->string('logo')->nullable();
      $table->string('hash')->nullable();
      $table->integer('currency_id')->unsigned()->nullable();
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
    Schema::dropIfExists('company_users');
  }
}
