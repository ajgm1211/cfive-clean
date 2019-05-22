<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFailedGlobalchargerstable extends Migration
{
	/**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		Schema::create('failed_globalchargers', function (Blueprint $table) {
			$table->increments('id');
			$table->string("surcharge");
			$table->string("origin");
			$table->string("destiny");
			$table->string("typedestiny");
			$table->string("calculationtype");
			$table->string("ammount");
			$table->string("currency");
			$table->string("carrier");
			$table->string("validityto");
			$table->string("validityfrom");
			$table->boolean("port")->default(false);
			$table->boolean("country")->default(false);
			$table->integer("company_user_id")->unsigned();
			$table->foreign('company_user_id')->references('id')->on('company_users');
			$table->integer("account_id")->unsigned();
			$table->foreign('account_id')->references('id')->on('account_importation_globalcharge')->onDelete('cascade');

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
		Schema::dropIfExists('failed_globalchargers');
	}
}
