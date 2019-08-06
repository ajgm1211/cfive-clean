<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInlandLocationsTable extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
      Schema::create('inland_locations', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->string('region');
        $table->integer('country_id')->unsigned();
        $table->integer('company_user_id')->unsigned()->nullable();
        $table->foreign('company_user_id')->references('id')->on('company_users')->onDelete('cascade');
        $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');   
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
    Schema::dropIfExists('inland_locations');
  }
}
