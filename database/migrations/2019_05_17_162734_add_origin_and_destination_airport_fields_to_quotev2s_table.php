<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOriginAndDestinationAirportFieldsToQuotev2sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quote_v2s', function (Blueprint $table) {
            $table->integer('origin_airport_id')->unsigned()->after('destination_port_id')->nullable();
            $table->integer('destination_airport_id')->unsigned()->after('origin_airport_id')->nullable();
            $table->foreign('origin_airport_id')->references('id')->on('airports')->onDelete('cascade');
            $table->foreign('destination_airport_id')->references('id')->on('airports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quote_v2s', function (Blueprint $table) {
            //
        });
    }
}
