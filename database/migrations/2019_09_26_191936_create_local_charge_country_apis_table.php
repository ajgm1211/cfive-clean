<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalChargeCountryApisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_charge_country_apis', function (Blueprint $table) {
            $table->integer('country_orig')->unsigned();
            $table->integer('country_dest')->unsigned();
            $table->integer('localcharge_id')->unsigned();
            $table->foreign('country_orig')->references('id')->on('countries');
            $table->foreign('country_dest')->references('id')->on('countries');
            $table->foreign('localcharge_id')->references('id')->on('local_charge_apis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('local_charge_country_apis');
    }
}
