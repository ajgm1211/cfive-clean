<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAcountRequestLclGcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts_import_clcl', function (Blueprint $table){
            $table->integer('requestlcl_id')->nullable()->after('company_user_id');
        });
        Schema::table('account_importation_globalcharge', function (Blueprint $table){
            $table->integer('requestgc_id')->nullable()->after('company_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts_import_clcl', function($table) {
            $table->dropColumn('requestlcl_id');
        });
        Schema::table('account_importation_globalcharge', function($table) {
            $table->dropColumn('requestgc_id');
        });
    }
}
