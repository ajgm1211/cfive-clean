<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyUserIdToApiIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_integrations', function (Blueprint $table) {
            $table->integer('company_user_id')->after('type')->unsigned()->nullable();
            $table->foreign('company_user_id')->references('id')->on('company_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_integrations', function (Blueprint $table) {
            //
        });
    }
}
