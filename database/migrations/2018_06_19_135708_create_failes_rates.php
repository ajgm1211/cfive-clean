<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFailesRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('failes_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('origin_port')->nullable();
            $table->string('destiny_port')->nullable();
            $table->string('carrier_id')->nullable();
            $table->integer('contract_id')->unsigned();
            $table->string('twuenty')->nullable();
            $table->string('forty')->nullable();
            $table->string('fortyhc')->nullable();
            $table->string('currency_id')->nullable();
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
            $table->softDeletes();
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
        Schema::dropIfExists('failes_rates');
    }
}
