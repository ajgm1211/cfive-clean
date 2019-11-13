<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProcedureGlobalcharge extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    DB::unprepared("DROP PROCEDURE IF EXISTS select_for_company_globalcharger;CREATE  PROCEDURE select_for_company_globalcharger(IN company_user_id INT)SELECT gb.id, (SELECT GROUP_CONCAT(DISTINCT(har.display_name) SEPARATOR ' | ') FROM globalcharport gbp INNER JOIN harbors har on har.id = gbp.port_orig WHERE gbp.globalcharge_id = gb.id ) as port_orig , (SELECT GROUP_CONCAT(DISTINCT(har.display_name) SEPARATOR ' | ') FROM globalcharport gbp INNER JOIN harbors har on har.id = gbp.port_dest WHERE gbp.globalcharge_id = gb.id ) as port_dest, (SELECT GROUP_CONCAT(DISTINCT(coun.name) SEPARATOR ' | ') FROM globalcharcountry gbCD INNER JOIN countries coun on coun.id = gbCD.country_orig WHERE gbCD.globalcharge_id = gb.id ) as country_orig , (SELECT GROUP_CONCAT(DISTINCT(counD.name) SEPARATOR ' | ') FROM globalcharcountry gbCD INNER JOIN countries counD on counD.id = gbCD.country_dest WHERE gbCD.globalcharge_id = gb.id ) as country_dest, (SELECT GROUP_CONCAT(DISTINCT(portPC.name) SEPARATOR ' | ') FROM global_char_port_countries gcPC INNER JOIN harbors portPC on portPC.id = gcPC.port_orig WHERE gcPC.globalcharge_id = gb.id ) as portcountry_orig , (SELECT GROUP_CONCAT(DISTINCT(counPC.name) SEPARATOR ' | ') FROM global_char_port_countries gbPCd INNER JOIN countries counPC on counPC.id = gbPCd.country_dest WHERE gbPCd.globalcharge_id = gb.id ) as portcountry_dest ,(SELECT GROUP_CONCAT(DISTINCT(counCP.name) SEPARATOR ' | ') FROM global_char_country_ports gbCP INNER JOIN countries counCP on counCP.id = gbCP.country_orig WHERE gbCP.globalcharge_id = gb.id ) as countryport_orig , (SELECT GROUP_CONCAT(DISTINCT(counCPd.name) SEPARATOR ' | ') FROM global_char_country_ports gbCPd INNER JOIN harbors counCPd on counCPd.id = gbCPd.port_dest WHERE gbCPd.globalcharge_id = gb.id ) as countryport_dest ,(SELECT GROUP_CONCAT(DISTINCT(carr.name) SEPARATOR ' | ') FROM globalcharcarrier gbC INNER JOIN carriers carr on carr.id = gbC.carrier_id WHERE gbC.globalcharge_id = gb.id ) as carrier, sg.name as surcharges, td.description as typedestiny, ct.name as calculationtype, gb.ammount, gb.validity,gb.expire, cy.alphacode AS currency, cmpu.name as company_user, gb.account_importation_globalcharge_id FROM globalcharges gb INNER JOIN surcharges sg ON gb.surcharge_id = sg.id INNER JOIN typedestiny td ON gb.typedestiny_id = td.id INNER JOIN calculationtype ct ON gb.calculationtype_id = ct.id INNER JOIN currency cy ON gb.currency_id = cy.id INNER JOIN company_users cmpu ON gb.company_user_id = cmpu.id WHERE gb.company_user_id = company_user_id");
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
