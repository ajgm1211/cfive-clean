<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlertCompanyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alert_company_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_user_id')->unsigned();
            $table->integer('alert_dp_id')->unsigned();
            $table->integer('n_global')->default(0);
            $table->integer('n_group')->default(0);
            $table->foreign('company_user_id')->references('id')->on('company_users');
            $table->foreign('alert_dp_id')->references('id')->on('alerts_duplicates_gc_fcl')->onDelete('cascade');
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
        Schema::dropIfExists('alert_company_users');
    }
}
