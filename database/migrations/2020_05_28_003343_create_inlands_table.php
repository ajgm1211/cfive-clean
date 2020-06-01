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
    Schema::disableForeignKeyConstraints();
    Schema::dropIfExists('inlands');
    Schema::enableForeignKeyConstraints();

    Schema::create('inlands', function (Blueprint $table) {
      $table->increments('id');
      $table->string('reference');
      $table->string('status');
      $table->integer('inland_type_id')->unsigned()->nullable();
      $table->integer('gp_container_id')->unsigned()->nullable();
      $table->date('validity');
      $table->date('expire');
      $table->integer('direction_id')->nullable()->unsigned();
      $table->integer('company_user_id')->unsigned()->nullable();
      $table->timestamps();

      $table->foreign('inland_type_id')->references('id')->on('inland_types')->onDelete('cascade');
      $table->foreign('company_user_id')->references('id')->on('company_users')->onDelete('cascade');
      $table->foreign('gp_container_id')->references('id')->on('group_containers');
      $table->foreign('direction_id')->references('id')->on('directions');

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
