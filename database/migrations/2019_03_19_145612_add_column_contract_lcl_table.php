<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnContractLclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts_lcl', function (Blueprint $table) {
            $table->integer('account_id')->nullable()->after('company_user_id')->unsigned();
            $table->foreign('account_id')->references('id')->on('accounts_import_clcl')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts_lcl', function ($table) {
            $table->dropColumn('account_id');
        });
    }
}
