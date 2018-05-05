<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInlandChargeMarkupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inland_charge_markups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('percent_markup')->nullable();
            $table->string('fixed_markup')->nullable();
            $table->string('currency')->nullable();
            $table->integer('price_subtype_id')->unsigned();
            $table->foreign('price_subtype_id')->references('id')->on('price_subtypes');
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
        Schema::dropIfExists('inland_charge_markups');
    }
}
