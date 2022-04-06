<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurchargePerContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surcharge_per_contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('charge');
            $table->string('type');
            $table->json('rates');
            $table->string('calculation_type');
            $table->string('currency');
            $table->string('origin_port');
            $table->string('destination_port');
            $table->integer('contract_id')->unsigned();
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
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
        Schema::dropIfExists('surcharge_per_contracts');
    }
}
