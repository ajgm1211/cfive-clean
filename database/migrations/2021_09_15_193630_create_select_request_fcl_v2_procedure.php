<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSelectRequestFclV2Procedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS select_request_fcl_v2; CREATE PROCEDURE select_request_fcl_v2(IN date_start VARCHAR(30),IN date_end VARCHAR(30))SELECT nr.id,cpu.name as company_user,nr.namecontract,nr.numbercontract,nr.data as request_data ,dr.name as direction,nr.namefile,(SELECT GROUP_CONCAT( DISTINCT(cr.name) SEPARATOR ', ') FROM request_fcl_carriers rcarr INNer JOIN carriers cr ON rcarr.carrier_id=cr.id WHERE nr.id=rcarr.request_id) as carriers, nr.validation,nr.created,CONCAT(us.name,CONCAT(' ',us.lastname)) as user,nr.time_total as time_elapsed,nr.username_load,nr.status,nr.contract_id as contract,cts.validator as validator_contract, cts.contract_code as contract_code,cts.name as contract_ref,nr.erased_contract FROM newcontractrequests nr INNER JOIN company_users cpu ON nr.company_user_id = cpu.id LEFT JOIN directions dr ON  dr.id = nr.direction_id LEFT JOIN contracts cts on nr.contract_id=cts.id INNER JOIN users us ON nr.user_id=us.id WHERE nr.created BETWEEN date_start AND date_end and nr.status_erased = '0'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('select_request_fcl_v2_procedure');
    }
}
