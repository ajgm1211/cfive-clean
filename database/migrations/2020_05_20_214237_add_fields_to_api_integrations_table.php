<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToApiIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_integrations', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('api_key')->after('url');
            $table->enum('module',['Contacts','Companies'])->after('url');
            $table->integer('api_integration_setting_id')->unsigned()->after('module');
            $table->foreign('api_integration_setting_id')->references('id')->on('api_integration_settings');
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
