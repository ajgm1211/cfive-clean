<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    Schema::create('users', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name');
      $table->string('lastname');
      $table->string('email')->unique();
      $table->string('phone')->nullable();
      $table->string('password');
      $table->enum('type',['admin','company','subuser'])->default('company');
      $table->string('name_company')->nullable();
      $table->string('position')->nullable();
      $table->string('access')->nullable();
      $table->boolean('verified')->default(false);
      $table->boolean('state')->default(true);
      $table->integer('company_user_id')->unsigned()->nullable();
      //$table->foreign('company_user_id')->references('id')->on('company_users')->onDelete('cascade');
      $table->rememberToken();
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
    Schema::dropIfExists('users');
  }
}
