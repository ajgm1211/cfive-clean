<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalChargeQuoteLclTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_charge_quote_lcl_totals', function (Blueprint $table) {
            $table->increments('id');
            $table->float('total');
            $table->integer('quote_id')->unsigned();
            $table->foreign('quote_id')->references('id')->on('quote_v2s');
            $table->integer('port_id')->unsigned();
            $table->foreign('port_id')->references('id')->on('harbors');
            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currency');
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
        Schema::dropIfExists('local_charge_quote_lcl_totals');
    }
}
