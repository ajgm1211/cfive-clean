<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcedureAccountFcl extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    DB::unprepared("DROP PROCEDURE IF EXISTS proc_account_fcl;CREATE PROCEDURE proc_account_fcl() SELECT ac.name,ac.date, IFNULL(ac.request_id, 'manual') as request_id  , IFNULL(ct.status, 'Contract erased') ,comp.name FROM accounts_import_cfcl ac inner join contracts ct on ac.id = ct.account_id inner join company_users comp on ac.company_user_id = comp.id ;");
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
