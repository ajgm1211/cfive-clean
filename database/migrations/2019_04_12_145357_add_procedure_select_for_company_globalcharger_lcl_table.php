<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProcedureSelectForCompanyGlobalchargerLclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS select_for_company_globalcharger_lcl; CREATE PROCEDURE select_for_company_globalcharger_lcl(IN company_user_id int) SELECT gbl.id, (SELECT GROUP_CONCAT(DISTINCT(har.display_name) SEPARATOR ' | ') FROM globalcharports_lcl gbp INNER JOIN harbors har on har.id = gbp.port_orig WHERE gbp.globalchargelcl_id = gbl.id ) as port_orig, (SELECT GROUP_CONCAT(DISTINCT(har.display_name) SEPARATOR ' | ') FROM globalcharports_lcl gbp INNER JOIN harbors har on har.id = gbp.port_dest WHERE gbp.globalchargelcl_id = gbl.id ) as port_dest, (SELECT GROUP_CONCAT(DISTINCT(coun.name) SEPARATOR ' | ') FROM globalcharcountry_lcl gbCD INNER JOIN countries coun on coun.id = gbCD.country_orig WHERE gbCD.globalchargelcl_id = gbl.id ) as country_orig , (SELECT GROUP_CONCAT(DISTINCT(counD.name) SEPARATOR ' | ') FROM globalcharcountry_lcl gbCD INNER JOIN countries counD on counD.id = gbCD.country_dest WHERE gbCD.globalchargelcl_id = gbl.id ) as country_dest, (SELECT GROUP_CONCAT(DISTINCT(carr.name) SEPARATOR ' | ') FROM globalcharcarriers_lcl gbC INNER JOIN carriers carr on carr.id = gbC.carrier_id WHERE gbC.globalchargelcl_id = gbl.id ) as carrier, sg.name as surcharges, td.description as typedestiny, ct.name as calculationtype, gbl.ammount, gbl.minimum, gbl.validity,gbl.expire, cy.alphacode AS currency, cmpu.name as company_user FROM globalcharges_lcl gbl INNER JOIN surcharges sg ON gbl.surcharge_id = sg.id INNER JOIN typedestiny td ON gbl.typedestiny_id = td.id INNER JOIN calculationtype ct ON gbl.calculationtypelcl_id = ct.id INNER JOIN currency cy ON gbl.currency_id = cy.id INNER JOIN company_users cmpu ON gbl.company_user_id = cmpu.id WHERE gbl.company_user_id = company_user_id;");
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
