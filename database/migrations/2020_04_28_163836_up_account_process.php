<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpAccountProcess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS proc_account_fcl; CREATE PROCEDURE proc_account_fcl(IN date_start VARCHAR(30),IN date_end VARCHAR(30)) SELECT ac.id,ac.name,ac.namefile,ac.date, IFNULL(ac.request_id, 'manual') as request_id  , IFNULL(ct.status, 'Contract erased') as status ,comp.name as company_name,ct.id as contract_id,ac.request_dp_id FROM accounts_import_cfcl ac inner join contracts ct on ac.id = ct.account_id inner join company_users comp on ac.company_user_id = comp.id WHERE ac.date BETWEEN date_start AND date_end;");
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
