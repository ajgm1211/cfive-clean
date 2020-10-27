<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaleTermChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_term_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->json('json_containers')->nullable();
            $table->float('amount');
            $table->integer('sale_term_id')->unsigned();
            $table->integer('calculation_type_id')->unsigned();
            $table->integer('currency_id')->unsigned();

            $table->foreign('sale_term_id')->references('id')->on('sale_term_v3s')->onDelete('cascade');
            $table->foreign('calculation_type_id')->references('id')->on('calculationtype');
            $table->foreign('currency_id')->references('id')->on('currency');
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
        Schema::dropIfExists('sale_term_charges');
    }
}
