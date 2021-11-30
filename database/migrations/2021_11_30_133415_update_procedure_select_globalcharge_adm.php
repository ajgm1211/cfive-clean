<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProcedureSelectGlobalchargeAdm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS select_globalcharge_adm ;
                        CREATE PROCEDURE select_globalcharge_adm(IN comp_id INT, IN carr_code INT)
                        SELECT 
                        gc.id AS id,
                        sr.name AS charge,
                        td.description AS charge_type, 
                        ctype.name AS calculation_type, 
                        (SELECT GROUP_CONCAT(DISTINCT har.code SEPARATOR ', ') FROM globalcharport gcP 
                        JOIN harbors har ON ((har.id = gcP.port_orig)) WHERE (gcP.globalcharge_id = gc.id)) AS origin_port,
                        (SELECT GROUP_CONCAT(DISTINCT har.code SEPARATOR ', ')FROM (globalcharport gcP  
                        JOIN harbors har ON ((har.id = gcP.port_dest))) WHERE(gcP.globalcharge_id = gc.id)) AS destination_port, 
                        (SELECT GROUP_CONCAT(DISTINCT coun.name SEPARATOR ', ') FROM (globalcharcountry gcCO 
                        JOIN countries coun ON ((coun.id = gcCO.country_orig)))  WHERE (gcCO.globalcharge_id = gc.id)) AS origin_country,
                        (SELECT GROUP_CONCAT(DISTINCT counD.name SEPARATOR ', ') FROM (globalcharcountry gcCD 
                        JOIN countries counD ON ((counD.id = gcCD.country_dest))) WHERE (gcCD.globalcharge_id = gc.id)) AS destination_country, 
                        (SELECT GROUP_CONCAT(DISTINCT(portPC.name)   SEPARATOR ', ') FROM global_char_port_countries gcPC INNER 
                        JOIN harbors portPC on portPC.id = gcPC.port_orig WHERE gcPC.globalcharge_id = gc.id ) as portcountry_orig , 
                        (SELECT GROUP_CONCAT(DISTINCT(counPC.name)   SEPARATOR ', ') FROM global_char_port_countries gcPCd INNER 
                        JOIN countries counPC on counPC.id = gcPCd.country_dest WHERE gcPCd.globalcharge_id = gc.id ) as portcountry_dest ,
                        (SELECT GROUP_CONCAT(DISTINCT(counCP.name)   SEPARATOR ', ') FROM global_char_country_ports gcCP INNER 
                        JOIN countries counCP on counCP.id = gcCP.country_orig WHERE gcCP.globalcharge_id = gc.id ) as countryport_orig , 
                        (SELECT GROUP_CONCAT(DISTINCT(counCPd.name)   SEPARATOR ', ') FROM global_char_country_ports gcCPd INNER 
                        JOIN harbors counCPd on counCPd.id = gcCPd.port_dest WHERE gcCPd.globalcharge_id = gc.id ) as countryport_dest ,
                        (SELECT GROUP_CONCAT(DISTINCT carr.name SEPARATOR ', ')    FROM(globalcharcarrier gcC 
                        JOIN carriers carr ON ((carr.id = gcC.carrier_id)))    WHERE (gcC.globalcharge_id = gc.id)) AS carrier, 
                        (SELECT GROUP_CONCAT(DISTINCT carr.uncode SEPARATOR ', ')    FROM(globalcharcarrier gcC 
                        JOIN carriers carr ON ((carr.id =  gcC.carrier_id)))   WHERE(gcC.globalcharge_id = gc.id)) AS carriers,
                        gc.ammount AS amount,
                        cur.alphacode AS currency_code,
                        gc.validity AS valid_from,
                        gc.expire AS valid_until,
                        gc.company_user_id AS company_user_id,
                        cmpu.name AS company_user    
                        FROM (((((globalcharges gc 
                                    JOIN surcharges sr ON ((sr.id = gc.surcharge_id)))
                                    JOIN typedestiny td ON ((td.id = gc.typedestiny_id))) 
                                    JOIN currency cur ON ((cur.id = gc.currency_id))) 
                                    JOIN calculationtype ctype ON ((ctype.id = gc.calculationtype_id))) 
                                    JOIN company_users cmpu ON ((cmpu.id = gc.company_user_id)) 
                                    JOIN globalcharcarrier gcC ON ((gc.id = gcC.globalcharge_id))) 
                        WHERE cmpu.id = comp_id 
                        AND IF (carr_code = '0', gcC.carrier_id is not null, gcC.carrier_id = carr_code)");
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
