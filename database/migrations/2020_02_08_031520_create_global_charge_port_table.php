<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalChargePortTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_charge_port_api', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('port_orig')->unsigned();
            $table->integer('port_dest')->unsigned();
            $table->integer('globalcharge_id')->unsigned();
            $table->integer('typedestiny_id')->unsigned();
            $table->foreign('port_orig')->references('id')->on('harbors');
            $table->foreign('port_dest')->references('id')->on('harbors');
            $table->foreign('globalcharge_id')->references('id')->on('global_charges_api')->onDelete('cascade');
            $table->foreign('typedestiny_id')->references('id')->on('typedestiny')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_charge_port');
    }
}
