<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewStatusToContractsLcl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE contracts_lcl MODIFY status ENUM('publish','draft','incomplete','expired','Clarification needed') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE contracts_lcl MODIFY status ENUM('publish','draft','incomplete','expired') DEFAULT 'draft'");
    }
}
