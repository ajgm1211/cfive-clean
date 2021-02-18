<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractRateApisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_rate_fcl_apis', function (Blueprint $table) {
            $table->increments('id');
            $table->string('origin_port');
            $table->string('destiny_port');
            $table->string('via')->nullable();
            $table->string('20DV')->nullable();
            $table->string('40DV')->nullable();
            $table->string('40HC')->nullable();
            $table->string('45HC')->nullable();
            $table->string('40NOR')->nullable();
            $table->string('currency');
            $table->string('transit_time')->nullable();
            $table->string('remarks')->nullable();
            $table->unsignedInteger('contract_id');
            $table->foreign('contract_id')
            ->references('id')->on('contracts')
            ->onDelete('cascade');
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
        Schema::dropIfExists('contract_rate_apis');
    }
}
