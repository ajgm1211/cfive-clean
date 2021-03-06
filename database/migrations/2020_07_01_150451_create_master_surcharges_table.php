<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterSurchargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_surcharges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('surcharge_id')->unsigned();
            $table->integer('carrier_id')->unsigned();
            $table->integer('typedestiny_id')->unsigned();
            $table->integer('calculationtype_id')->unsigned();
            $table->integer('direction_id')->unsigned();

            $table->foreign('surcharge_id')->references('id')->on('surcharges');
            $table->foreign('typedestiny_id')->references('id')->on('typedestiny');
            $table->foreign('calculationtype_id')->references('id')->on('calculationtype');
            $table->foreign('carrier_id')->references('id')->on('carriers');
            $table->foreign('direction_id')->references('id')->on('directions');
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
        Schema::dropIfExists('master_surcharges');
    }
}
