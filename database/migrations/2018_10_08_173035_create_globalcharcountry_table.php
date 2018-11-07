<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalcharcountryTable extends Migration
{
	/**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		Schema::create('globalcharcountry', function (Blueprint $table) {
			$table->integer('country_orig')->unsigned();
			$table->integer('country_dest')->unsigned();
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
		Schema::dropIfExists('globalcharcountry');
	}
}
