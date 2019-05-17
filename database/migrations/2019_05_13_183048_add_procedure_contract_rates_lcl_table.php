<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProcedureContractRatesLclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS select_rates_contract_lcl; CREATE PROCEDURE select_rates_contract_lcl(IN company_user_id int) SELECT ra.id,cont.company_user_id,cont.id as contract_id ,cont.name as name , cont.number,cont.validity as validy,cont.expire,cont.status as status, har_orig.display_name as port_orig , har_dest.display_name as port_dest , car.name as carrier , ra.uom,ra.minimum, curr.alphacode as currency from rates_lcl ra INNER JOIN harbors har_orig ON har_orig.id = ra.origin_port INNER JOIN harbors har_dest ON har_dest.id = ra.destiny_port INNER JOIN carriers car on car.id = ra.carrier_id INNER JOIN currency curr on curr.id = ra.currency_id INNER JOIN contracts_lcl cont on cont.id = ra.contractlcl_id WHERE cont.company_user_id = company_user_id");
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
