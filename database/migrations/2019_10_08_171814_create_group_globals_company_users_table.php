<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupGlobalsCompanyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_globals_company_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('alert_cmpuser_id')->unsigned();
            $table->integer('status_alert_id')->unsigned();
            $table->integer('n_global')->default(0);
            $table->integer('global_id')->unsigned();
            $table->foreign('global_id')->references('id')->on('globalcharges')->onDelete('cascade');
            $table->foreign('alert_cmpuser_id')->references('id')->on('alert_company_users')->onDelete('cascade');
            $table->foreign('status_alert_id')->references('id')->on('status_alerts');
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
        Schema::dropIfExists('group_globals_company_users');
    }
}
