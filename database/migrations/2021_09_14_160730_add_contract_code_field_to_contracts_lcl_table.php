<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContractCodeFieldToContractsLclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts_lcl', function (Blueprint $table) {
            $table->string('contract_code')->after('code')->nullable();
            $table->unique(['contract_code', 'company_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts_lcl', function (Blueprint $table) {
            //
        });
    }
}
