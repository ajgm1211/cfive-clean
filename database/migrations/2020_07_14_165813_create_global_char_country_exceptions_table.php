<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalCharCountryExceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_char_country_exceptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('country_orig')->unsigned()->nullable();
            $table->integer('country_dest')->unsigned()->nullable();
            $table->integer('globalcharge_id')->unsigned();
            $table->foreign('country_orig')->references('id')->on('countries');
            $table->foreign('country_dest')->references('id')->on('countries');
            $table->foreign('globalcharge_id')->references('id')->on('globalcharges')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_char_country_exceptions');
    }
}
