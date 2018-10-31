<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalcharcarrierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('globalcharcarrier', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('carrier_id')->unsigned();
            $table->integer('globalcharge_id')->unsigned();
            $table->foreign('carrier_id')->references('id')->on('carriers');
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
        Schema::dropIfExists('globalcharcarrier');
    }
}
