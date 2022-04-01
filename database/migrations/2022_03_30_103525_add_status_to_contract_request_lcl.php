<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToContractRequestLcl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE new_contract_request_lcl MODIFY status ENUM('Pending','Processing','Done','Review','Clarification needed')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE new_contract_request_lcl MODIFY status ENUM('Pending','Processing','Done','Review')");
    }
}
