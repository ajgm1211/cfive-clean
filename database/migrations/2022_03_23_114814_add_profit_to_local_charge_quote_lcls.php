<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProfitToLocalChargeQuoteLcls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('local_charge_quote_lcls', function (Blueprint $table) {
            $table->double('profit',8,2)->after('price');
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
