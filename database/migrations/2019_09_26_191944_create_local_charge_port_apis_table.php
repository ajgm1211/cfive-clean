<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalChargePortApisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_charge_port_apis', function (Blueprint $table) {
            $table->integer('port_orig')->unsigned();
            $table->integer('port_dest')->unsigned();
            $table->integer('localcharge_id')->unsigned();
            $table->foreign('port_orig')->references('id')->on('harbors');
            $table->foreign('port_dest')->references('id')->on('harbors');
            $table->foreign('localcharge_id')->references('id')->on('local_charge_apis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('local_charge_port_apis');
    }
}
