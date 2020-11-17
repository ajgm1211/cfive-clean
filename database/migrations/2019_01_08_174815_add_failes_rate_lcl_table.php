<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFailesRateLclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('failes_rate_lcl', function (Blueprint $table) {
            $table->increments('id');
            $table->string('origin_port');
            $table->string('destiny_port');
            $table->string('carrier_id');
            $table->integer('contractlcl_id')->unsigned();
            $table->string('uom');
            $table->string('minimum');
            $table->string('currency_id');
            $table->foreign('contractlcl_id')->references('id')->on('contracts_lcl')->onDelete('cascade');
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
        Schema::dropIfExists('failes_rate_lcl');
    }
}
