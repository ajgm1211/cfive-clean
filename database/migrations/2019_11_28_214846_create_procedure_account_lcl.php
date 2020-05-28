<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcedureAccountLcl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      
        DB::unprepared("DROP PROCEDURE IF EXISTS proc_account_lcl;CREATE PROCEDURE proc_account_lcl() SELECT ac.id,ac.name,ac.date, IFNULL(ac.requestlcl_id, 'manual') as request_id  , IFNULL(ct.status, 'Contract erased') as status ,comp.name as company_name,ct.id as contract_id FROM accounts_import_clcl ac inner join contracts_lcl ct on ac.id = ct.account_id inner join company_users comp on ac.company_user_id = comp.id ;");
        
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
