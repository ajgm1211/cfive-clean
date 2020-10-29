<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableGloblalChargeApiExceptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_chargesapi_port_exception', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('port_orig')->unsigned()->nullable();
            $table->integer('port_dest')->unsigned()->nullable();
            $table->integer('globalchargeapi_id')->unsigned();
            $table->foreign('port_orig')->references('id')->on('harbors');
            $table->foreign('port_dest')->references('id')->on('harbors');
            $table->foreign('globalchargeapi_id')->references('id')->on('global_charges_api')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_globlal_charge_api_exceptions');
    }
}
