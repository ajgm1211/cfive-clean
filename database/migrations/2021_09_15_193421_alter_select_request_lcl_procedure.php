<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSelectRequestLclProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS select_request_lcl; CREATE PROCEDURE select_request_lcl()SELECT nr.id,cpu.name as company_user,nr.namecontract,dr.name as direction,(SELECT GROUP_CONCAT( DISTINCT(cr.name) SEPARATOR ', ')  FROM request_lcl_carriers rcarr INNER JOIN carriers cr ON rcarr.carrier_id=cr.id WHERE nr.id=rcarr.request_id) as carriers, nr.validation,nr.created,CONCAT(us.name,CONCAT(' ',us.lastname)) as user,nr.time_total as time_elapsed,nr.username_load,nr.status,nr.contract_id as contract,cts.contract_code as contract_code,cts.name as contract_ref FROM new_contract_request_lcl nr INNER JOIN company_users cpu ON nr.company_user_id = cpu.id LEFT JOIN directions dr ON  dr.id = nr.direction_id LEFT JOIN contracts_lcl cts on nr.contract_id=cts.id INNER JOIN users us ON nr.user_id=us.id");
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
