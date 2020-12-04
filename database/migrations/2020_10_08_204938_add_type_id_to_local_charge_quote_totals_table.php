<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeIdToLocalChargeQuoteTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('local_charge_quote_totals', function (Blueprint $table) {
            $table->integer('type_id')->unsigned()->after('quote_id')->nullable();
            $table->foreign('type_id')->references('id')->on('typedestiny');
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
