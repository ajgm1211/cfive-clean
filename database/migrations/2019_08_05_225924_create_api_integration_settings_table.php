<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiIntegrationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_integration_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_user_id')->unsigned();
            $table->foreign('company_user_id')->references('id')->on('company_users')->onDelete('cascade');
            $table->integer('api_integration_id')->unsigned();
            $table->foreign('api_integration_id')->references('id')->on('api_integrations')->onDelete('cascade');
            $table->string('api_key');
            $table->boolean('enable')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_integration_settings');
    }
}
