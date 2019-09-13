<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleTermV2ChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_term_v2_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sale_term_id')->unsigned();
            $table->foreign('sale_term_id')->references('id')->on('sale_term_v2s')->onDelete('cascade');
            $table->string('charge')->nullable();
            $table->string('detail')->nullable();
            $table->float('c20')->nullable();
            $table->float('c40')->nullable();
            $table->float('c40hc')->nullable();
            $table->float('c40nor')->nullable();
            $table->float('c45')->nullable();
            $table->float('units')->nullable();
            $table->float('rate')->nullable();
            $table->float('markup')->nullable();
            $table->integer('currency_id')->unsigned()->nullable();
            $table->foreign('currency_id')->references('id')->on('currency')->onDelete('cascade');
            $table->float('total')->nullable();
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
        Schema::dropIfExists('sale_term_v2_charges');
    }
}
