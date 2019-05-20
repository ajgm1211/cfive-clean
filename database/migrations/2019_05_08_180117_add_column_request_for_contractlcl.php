<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnRequestForContractlcl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_contract_request_lcl', function(Blueprint $table){
            $table->integer('contract_id')->unsigned()->nullable()->after('sentemail');
            $table->foreign('contract_id')->references('id')->on('contracts_lcl')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_contract_request_lcl', function($table) {
            $table->dropColumn('contract_id');
        });
    }
}
