<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->integer('contract_id')->nullable();
            $table->string('twuenty')->nullable();
            $table->string('forty')->nullable();
            $table->string('fortyhc')->nullable();
            $table->string('currency_id')->nullable();
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
