<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalcharIdsView extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    DB::statement("
      CREATE OR REPLACE VIEW views_localcharges_ids AS
      (
select lc.id, lc.contract_id, sr.id as surcharge,(SELECT GROUP_CONCAT(DISTINCT(har.id) SEPARATOR ', ') FROM localcharports lcP INNER JOIN harbors har on har.id = lcP.port_orig WHERE lcP.localcharge_id = lc.id ) as port_orig , (SELECT GROUP_CONCAT(DISTINCT(har.id) SEPARATOR ', ') FROM localcharports lcP INNER JOIN harbors har on har.id = lcP.port_dest WHERE lcP.localcharge_id = lc.id ) as port_dest, (SELECT GROUP_CONCAT(DISTINCT(coun.id) SEPARATOR ', ') FROM localcharcountry lcCO INNER JOIN countries coun on coun.id = lcCO.country_orig WHERE lcCO.localcharge_id = lc.id ) as country_orig ,(SELECT GROUP_CONCAT(DISTINCT(counD.id) SEPARATOR ', ') FROM localcharcountry lcCD INNER JOIN countries counD on counD.id = lcCD.country_dest WHERE lcCD.localcharge_id = lc.id ) as country_dest ,td.id changetype,(SELECT GROUP_CONCAT(DISTINCT(carr.id) SEPARATOR ', ') FROM localcharcarriers lcC INNER JOIN carriers carr on carr.id = lcC.carrier_id WHERE lcC.localcharge_id = lc.id ) as carrier,ctype.id calculation_type,cur.id as currency,lc.ammount from localcharges lc INNER JOIN surcharges sr on sr.id = lc.surcharge_id INNER JOIN typedestiny td on td.id = lc.typedestiny_id INNER JOIN currency cur on cur.id = lc.currency_id INNER JOIN calculationtype ctype on ctype.id = lc.calculationtype_id
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
    DB::statement('DROP VIEW IF EXISTS views_localcharges_ids');
  }
}
