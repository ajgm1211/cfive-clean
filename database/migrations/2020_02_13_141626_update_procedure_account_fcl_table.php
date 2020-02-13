<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProcedureAccountFclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS proc_account_fcl;CREATE PROCEDURE 	proc_account_fcl() SELECT ac.id,ac.name,ac.namefile,ac.date, IFNULL(ac.request_id, 'manual') as request_id  , IFNULL(ct.status, 'Contract erased') as status ,comp.name as company_name,ct.id as contract_id FROM accounts_import_cfcl ac inner join contracts ct on ac.id = ct.account_id inner join company_users comp on ac.company_user_id = comp.id");
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
