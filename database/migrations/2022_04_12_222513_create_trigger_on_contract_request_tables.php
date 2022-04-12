<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTriggerOnContractRequestTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER `update_fcl_request_quota_trigger` AFTER INSERT ON `newcontractrequests`
            FOR EACH ROW BEGIN
            UPDATE quota_requests
                SET remaining_quota = remaining_quota - 1
                WHERE company_user_id = new.company_user_id;
            END
        ');

        DB::unprepared('
        CREATE TRIGGER `update_lcl_request_quota_trigger` AFTER INSERT ON `new_contract_request_lcl`
            FOR EACH ROW BEGIN
            UPDATE quota_requests
                SET remaining_quota = remaining_quota - 1
                WHERE company_user_id = new.company_user_id;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('update_fcl_request_quota_trigger');
        Schema::dropIfExists('update_lcl_request_quota_trigger');
    }
}
