<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableContractLclComments extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    DB::statement("ALTER TABLE contracts_lcl CHANGE comments comments TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL");
    //
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
