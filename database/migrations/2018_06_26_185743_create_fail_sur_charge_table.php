<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFailSurChargeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('failes_surcharges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('surcharge_id')->nullable();
            $table->string('port_orig')->nullable();
            $table->string('port_dest')->nullable();
            $table->string('typedestiny_id')->nullable();
            $table->integer('contract_id')->unsigned();
            $table->string('calculationtype_id')->nullable();
            $table->string('ammount')->nullable();
            $table->string('currency_id')->nullable();
            $table->string('carrier_id')->nullable();
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
        Schema::dropIfExists('failes_surcharges');
    }
}
