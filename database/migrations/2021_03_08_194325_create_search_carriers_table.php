<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSearchCarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_carriers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('search_rate_id')->unsigned();
            $table->foreign('search_rate_id')->references('id')->on('search_rates');
            $table->integer('carrier_id')->unsigned();
            $table->foreign('carrier_id')->references('id')->on('carriers');
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
        Schema::dropIfExists('search_carriers');
    }
}
