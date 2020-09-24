<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPortIdAndCurrencyIdToLocalChargeQuoteTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('local_charge_quote_totals', function (Blueprint $table) {
            $table->integer('port_id')->unsigned()->after('quote_id');
            $table->foreign('port_id')->references('id')->on('harbors');
            $table->integer('currency_id')->unsigned()->after('port_id');
            $table->foreign('currency_id')->references('id')->on('harbors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('local_charge_quote_totals', function (Blueprint $table) {
            //
        });
    }
}
