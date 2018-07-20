<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('contract_id')->nullable();
            $table->string('calculationtype_id')->nullable();
            $table->string('ammount')->nullable();
            $table->string('currency_id')->nullable();
            $table->string('carrier_id')->nullable();
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
