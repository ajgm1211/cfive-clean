<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesLevelDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices_level_details', function (Blueprint $table) {
            $table->increments('id');
            $table->json('amount');
            $table->integer('currency_id')->unsigned();
            $table->integer('price_level_id')->unsigned();
            $table->integer('price_level_applied_id')->unsigned();


            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prices_level_details');
    }
}
