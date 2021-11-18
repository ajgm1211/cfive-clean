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
        Schema::create('price_level_details', function (Blueprint $table) {
            $table->increments('id');
            $table->json('amount');
            $table->integer('price_level_id')->unsigned();
            $table->foreign('price_level_id')->references('id')->on('price_levels')->onDelete('cascade');
            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currency');
            $table->integer('direction_id')->unsigned();
            $table->foreign('direction_id')->references('id')->on('directions');
            $table->integer('price_level_apply_id')->unsigned();
            $table->foreign('price_level_apply_id')->references('id')->on('price_level_applies');
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
        Schema::dropIfExists('price_level_details');
    }
}
