<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProcedureRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS proc_rates_fcl; CREATE PROCEDURE proc_rates_fcl(IN contract_id INT) SELECT rt.id,hb_or.name as origin,hb_de.name as destiny,car.name as carrier,twuenty,forty,fortyhc,fortynor,fortyfive,containers,cur.alphacode as currency FROM rates rt    INNER JOIN harbors hb_or ON rt.origin_port=hb_or.id INNER JOIN harbors hb_de ON rt.destiny_port=hb_de.id INNER JOIN carriers car ON rt.carrier_id=car.id INNER JOIN currency cur ON rt.currency_id=cur.id WHERE rt.contract_id=contract_id;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
