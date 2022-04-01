<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusToContractRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE newcontractrequests MODIFY status ENUM('Pending','Processing','Done','Review','Imp Finished','Clarification needed')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE newcontractrequests MODIFY status ENUM('Pending','Processing','Done','Review','Imp Finished')");
    }
}
