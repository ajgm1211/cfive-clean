<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSurchargeIdToLocalChargeQuoteLclsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('local_charge_quote_lcls', function (Blueprint $table) {
            $table->integer('surcharge_id')->unsigned()->nullable()->after('charge');
            $table->foreign('surcharge_id')
            ->references('id')->on('surcharges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('local_charge_quote_lcls', function (Blueprint $table) {
            //
        });
    }
}
