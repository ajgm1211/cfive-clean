<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateGlobalchargerViewallView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE OR REPLACE VIEW views_globalcharges AS(select gc.id AS id,sr.name AS charge,td.description AS charge_type,ctype.name AS calculation_type,(select group_concat(distinct har.code separator ', ') from (globalcharport gcP join harbors har on((har.id = gcP.port_orig))) where (gcP.globalcharge_id = gc.id)) AS origin_port,(select group_concat(distinct har.code separator ', ') from (globalcharport gcP join harbors har on((har.id = gcP.port_dest))) where (gcP.globalcharge_id = gc.id)) AS destination_port,(select group_concat(distinct coun.name separator ', ') from (globalcharcountry gcCO join countries coun on((coun.id = gcCO.country_orig))) where (gcCO.globalcharge_id = gc.id)) AS origin_country,(select group_concat(distinct counD.name separator ', ') from (globalcharcountry gcCD join countries counD on((counD.id = gcCD.country_dest))) where (gcCD.globalcharge_id = gc.id)) AS destination_country,(select group_concat(distinct carr.name separator ', ') from (globalcharcarrier gcC join carriers carr on((carr.id = gcC.carrier_id))) where (gcC.globalcharge_id = gc.id)) AS carrier,(select group_concat(distinct carr.uncode separator ', ') from (globalcharcarrier gcC join carriers carr on((carr.id = gcC.carrier_id))) where (gcC.globalcharge_id = gc.id)) AS carriers,gc.ammount AS amount,cur.alphacode AS currency_code,gc.validity AS valid_from,gc.expire AS valid_until,gc.company_user_id AS company_user_id,cmpu.name AS company_user from ((((globalcharges gc join surcharges sr on((sr.id = gc.surcharge_id))) join typedestiny td on((td.id = gc.typedestiny_id))) join currency cur on((cur.id = gc.currency_id))) join calculationtype ctype on((ctype.id = gc.calculationtype_id)))join company_users cmpu on((cmpu.id = gc.company_user_id)))");
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
