<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProcedureLcl extends Migration
{

  public function up()
  {
    DB::unprepared("DROP PROCEDURE IF EXISTS proc_localchar_lcl;
  CREATE PROCEDURE proc_localchar_lcl(IN idcontract INT)
    select lc.id, lc.contractlcl_id, sr.name as surcharge,(SELECT GROUP_CONCAT(DISTINCT(har.display_name) SEPARATOR ', ') FROM localcharports_lcl lcP INNER JOIN harbors har on har.id = lcP.port_orig WHERE lcP.localchargelcl_id = lc.id ) as port_orig , (SELECT GROUP_CONCAT(DISTINCT(har.display_name) SEPARATOR ', ') FROM localcharports_lcl lcP INNER JOIN harbors har on har.id = lcP.port_dest WHERE lcP.localchargelcl_id = lc.id ) as port_dest, (SELECT GROUP_CONCAT(DISTINCT(coun.name) SEPARATOR ', ') FROM localcharcountry_lcl lcCO INNER JOIN countries coun on coun.id = lcCO.country_orig WHERE lcCO.localchargelcl_id = lc.id ) as country_orig ,(SELECT GROUP_CONCAT(DISTINCT(counD.name) SEPARATOR ', ') FROM localcharcountry_lcl lcCD INNER JOIN countries counD on counD.id = lcCD.country_dest WHERE lcCD.localchargelcl_id = lc.id ) as country_dest ,td.description changetype,(SELECT GROUP_CONCAT(DISTINCT(carr.name) SEPARATOR ', ') FROM localcharcarriers_lcl lcC INNER JOIN carriers carr on carr.id = lcC.carrier_id WHERE lcC.localchargelcl_id = lc.id ) as carrier,ctype.name calculation_type,cur.alphacode as currency,lc.ammount,lc.minimum  from localcharges_lcl lc INNER JOIN surcharges sr on sr.id = lc.surcharge_id INNER JOIN typedestiny td on td.id = lc.typedestiny_id INNER JOIN currency cur on cur.id = lc.currency_id INNER JOIN calculationtypelcl ctype on ctype.id = lc.calculationtypelcl_id WHERE lc.contractlcl_id = idcontract ;");
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
