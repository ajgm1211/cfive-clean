<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePivotLocalChargeLclQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_local_charge_lcl_quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('charge_lcl_air_id')->unsigned();
            $table->integer('local_charge_quote_lcl_id')->unsigned();
            $table->integer('quote_id')->unsigned();
            $table->foreign('charge_lcl_air_id')
                ->references('id')->on('charge_lcl_airs')
                ->onDelete('cascade');
            $table->foreign('local_charge_quote_lcl_id')
                    ->references('id')->on('local_charge_quote_lcls')
                    ->onDelete('cascade');
            $table->foreign('quote_id')
                            ->references('id')->on('quote_v2s')
                            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pivot_local_charge_lcl_quotes');
    }
}
