<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalcharportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('localcharports', function (Blueprint $table) {
            $table->integer('port_orig')->unsigned();
            $table->integer('port_dest')->unsigned();
            $table->integer('localcharge_id')->unsigned();
            $table->foreign('port_orig')->references('id')->on('harbors');
            $table->foreign('port_dest')->references('id')->on('harbors');
            $table->foreign('localcharge_id')->references('id')->on('localcharges')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('localcharports');
    }
}
