<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProcedureLocalCharge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS proc_localchar;");
        DB::unprepared("CREATE PROCEDURE `proc_localchar`(IN idcontract INT) select lc.id, lc.contract_id, sr.name as surcharge, (SELECT GROUP_CONCAT(DISTINCT(har.display_name) SEPARATOR ', ') FROM localcharports lcP INNER JOIN harbors har on har.id = lcP.port_orig WHERE lcP.localcharge_id = lc.id ) as port_orig , (SELECT GROUP_CONCAT(DISTINCT(har.display_name) SEPARATOR ', ') FROM localcharports lcP INNER JOIN harbors har on har.id = lcP.port_dest WHERE lcP.localcharge_id = lc.id ) as port_dest, (SELECT GROUP_CONCAT(DISTINCT(coun.name) SEPARATOR ', ') FROM localcharcountry lcCO INNER JOIN countries coun on coun.id = lcCO.country_orig WHERE lcCO.localcharge_id = lc.id ) as country_orig ,(SELECT GROUP_CONCAT(DISTINCT(counD.name) SEPARATOR ', ') FROM localcharcountry lcCD INNER JOIN countries counD on counD.id = lcCD.country_dest WHERE lcCD.localcharge_id = lc.id ) as country_dest , td.description changetype,(SELECT GROUP_CONCAT(DISTINCT(carr.name) SEPARATOR ', ') FROM localcharcarriers lcC INNER JOIN carriers carr on carr.id = lcC.carrier_id WHERE lcC.localcharge_id = lc.id ) as carrier,ctype.name calculation_type,cur.alphacode as currency,lc.ammount, owr.lower_limit,owr.upper_limit from localcharges lc INNER JOIN surcharges sr on sr.id = lc.surcharge_id INNER JOIN typedestiny td on td.id = lc.typedestiny_id INNER JOIN currency cur on cur.id = lc.currency_id INNER JOIN calculationtype ctype on ctype.id = lc.calculationtype_id left join overweight_ranges owr on (owr.model_id=lc.id) and (owr.model_type='App\\LocalCharge') WHERE lc.contract_id = idcontract;");
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
