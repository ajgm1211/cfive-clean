<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAcountGloblachargerLclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('globalcharges_lcl', function (Blueprint $table){
			$table->integer('account_imp_gclcl_id')->nullable()->after('company_user_id')->unsigned();
			$table->foreign('account_imp_gclcl_id')->references('id')->on('account_importation_global_charger_lcls')->onDelete('cascade');
		});
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
