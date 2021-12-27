<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeCompanyInGroupUsersCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_users_companies', function (Blueprint $table) {
            $table->dropForeign('group_users_companies_company_id_foreign');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_users_companies', function (Blueprint $table) {
        });
    }
}
