<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalchargesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
      CREATE VIEW views_localcharges AS
      (
       select lc.id, lc.contract_id, sr.name as surcharge,(SELECT GROUP_CONCAT(DISTINCT(har.display_name) SEPARATOR ', ') FROM localcharports lcP INNER JOIN harbors har on har.id = lcP.port_orig WHERE lcP.localcharge_id = lc.id ) as port_orig , (SELECT GROUP_CONCAT(DISTINCT(har.display_name) SEPARATOR ', ') FROM localcharports lcP INNER JOIN harbors har on har.id = lcP.port_dest WHERE lcP.localcharge_id = lc.id ) as port_dest,td.description changetype,(SELECT GROUP_CONCAT(DISTINCT(carr.name) SEPARATOR ', ') FROM localcharcarriers lcC INNER JOIN carriers carr on carr.id = lcC.carrier_id WHERE lcC.localcharge_id = lc.id ) as carrier,ctype.name calculation_type,cur.alphacode as currency,lc.ammount from localcharges lc INNER JOIN surcharges sr on sr.id = lc.surcharge_id INNER JOIN typedestiny td on td.id = lc.typedestiny_id INNER JOIN currency cur on cur.id = lc.currency_id INNER JOIN calculationtype ctype on ctype.id = lc.calculationtype_id
      )
    ");
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
