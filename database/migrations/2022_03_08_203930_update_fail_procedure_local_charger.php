<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFailProcedureLocalCharger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP procedure IF exists proc_fails_surchargers_fcl; CREATE PROCEDURE `proc_fails_surchargers_fcl`(IN contact_id_sc INT) SELECT * FROM failes_surcharges flc left join fail_overweight_ranges owr on (owr.model_id=flc.id) and (owr.model_type='App\\FailSurCharge') WHERE contract_id=contact_id_sc;");
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
