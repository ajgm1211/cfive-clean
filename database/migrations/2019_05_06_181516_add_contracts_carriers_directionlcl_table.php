<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContractsCarriersDirectionlclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts_carriers_lcl', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('carrier_id')->unsigned();
            $table->integer('contract_id')->unsigned();
            $table->foreign('carrier_id')->references('id')->on('carriers')->onDelete('cascade');
            $table->foreign('contract_id')->references('id')->on('contracts_lcl')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('contracts_lcl', function (Blueprint $table){
            $table->integer('direction_id')->nullable()->after('account_id')->unsigned();
            $table->foreign('direction_id')->references('id')->on('directions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts_carriers_lcl');
        Schema::dropIfExists('contracts_lcl');
    }
}
