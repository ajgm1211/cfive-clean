<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalChargeQuoteTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_charge_quote_totals', function (Blueprint $table) {
            $table->increments('id');
            $table->json('total')->nullable();
            $table->integer('quote_id')->unsigned();
            $table->foreign('quote_id')->references('id')->on('quote_v2s');
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
        Schema::dropIfExists('local_charge_quote_totals');
    }
}
