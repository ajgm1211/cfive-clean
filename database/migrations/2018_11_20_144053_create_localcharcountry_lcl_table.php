<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalcharcountryLclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('localcharcountry_lcl', function (Blueprint $table) {
            $table->integer('country_orig')->unsigned();
			$table->integer('country_dest')->unsigned();
			$table->integer('localchargelcl_id')->unsigned();
			$table->foreign('country_orig')->references('id')->on('countries');
			$table->foreign('country_dest')->references('id')->on('countries');
			$table->foreign('localchargelcl_id')->references('id')->on('localcharges_lcl')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('localcharcountry_lcl');
    }
}
