<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreightMarkupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freight_markups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('percent_markup')->nullable();
            $table->string('fixed_markup')->nullable();
            $table->string('currency')->nullable();
            $table->integer('price_type_id')->unsigned();
            $table->foreign('price_type_id')->references('id')->on('price_types');
            $table->integer('price_id')->unsigned();
            $table->foreign('price_id')->references('id')->on('prices')->onDelete('cascade');
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
        Schema::dropIfExists('freight_markups');
    }
}
