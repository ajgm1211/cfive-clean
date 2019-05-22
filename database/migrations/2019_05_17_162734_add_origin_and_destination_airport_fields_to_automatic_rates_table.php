<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOriginAndDestinationAirportFieldsToAutomaticRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automatic_rates', function (Blueprint $table) {
            $table->integer('origin_port_id')->unsigned()->nullable()->change();
            $table->integer('destination_port_id')->unsigned()->nullable()->change();
            $table->integer('carrier_id')->unsigned()->nullable()->change();
            $table->integer('origin_airport_id')->unsigned()->after('destination_port_id')->nullable();
            $table->integer('destination_airport_id')->unsigned()->after('origin_airport_id')->nullable();
            $table->integer('airline_id')->unsigned()->after('carrier_id')->nullable();
            $table->foreign('origin_airport_id')->references('id')->on('airports')->onDelete('cascade');
            $table->foreign('destination_airport_id')->references('id')->on('airports')->onDelete('cascade');
            $table->foreign('airline_id')->references('id')->on('airlines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('automatic_rates', function (Blueprint $table) {
            //
        });
    }
}
