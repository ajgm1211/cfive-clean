<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnGlobalchargeTable extends Migration
{
	public function up()
	{
		Schema::table('globalcharges', function (Blueprint $table){
			$table->integer('account_importation_globalcharge_id')->nullable()->after('company_user_id')->unsigned();
			$table->foreign('account_importation_globalcharge_id')->references('id')->on('account_importation_globalcharge')->onDelete('cascade');
		});
	}


	public function down()
	{
		Schema::table('globalcharges', function($table) {
			$table->dropColumn('account_importation_globalcharge_id');

		});
	}
}
