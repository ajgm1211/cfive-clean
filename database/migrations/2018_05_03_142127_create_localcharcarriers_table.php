<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocalcharcarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('localcharcarriers', function (Blueprint $table) {
            $table->integer('carrier_id')->unsigned();
            $table->integer('localcharge_id')->unsigned();
            $table->foreign('carrier_id')->references('id')->on('carriers');
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
        Schema::dropIfExists('localcharcarriers');
    }
}
