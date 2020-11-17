<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdateContractRatesFclView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE OR REPLACE VIEW views_contract_rates AS ( SELECT ra.id,cont.company_user_id,cont.id as contract_id ,cont.name as name , cont.number,cont.validity as validy,cont.expire,cont.status as status, har_orig.display_name as port_orig , har_dest.display_name as port_dest , car.name as carrier , ra.twuenty,ra.forty,ra.fortyhc ,ra.fortynor,ra.fortyfive, curr.alphacode as currency, sh.name AS schedule_type, ra.transit_time, ra.via from rates ra INNER JOIN harbors har_orig ON har_orig.id = ra.origin_port INNER JOIN harbors har_dest ON har_dest.id = ra.destiny_port INNER JOIN carriers car on car.id = ra.carrier_id INNER JOIN currency curr on curr.id = ra.currency_id LEFT JOIN schedule_type sh ON sh.id = ra.schedule_type_id INNER JOIN contracts cont on cont.id = ra.contract_id)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS views_contract_rates');
    }
}
