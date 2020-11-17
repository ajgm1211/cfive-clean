<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleTermSurchargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_term_surcharges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sale_term_id')->unsigned();
            $table->foreign('sale_term_id')->references('id')->on('sale_terms')->onDelete('cascade');
            $table->integer('surcharge_id')->unsigned();
            $table->foreign('surcharge_id')->references('id')->on('surcharges')->onDelete('cascade');
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
        Schema::dropIfExists('sale_term_surcharges');
    }
}
