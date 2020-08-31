<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalCharPortExceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_char_port_exceptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('port_orig')->unsigned()->nullable();
            $table->integer('port_dest')->unsigned()->nullable();
            $table->integer('globalcharge_id')->unsigned();
            $table->foreign('port_orig')->references('id')->on('harbors');
            $table->foreign('port_dest')->references('id')->on('harbors');
            $table->foreign('globalcharge_id')->references('id')->on('globalcharges')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_char_port_exceptions');
    }
}
