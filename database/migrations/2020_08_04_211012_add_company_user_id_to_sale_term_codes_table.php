<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyUserIdToSaleTermCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_term_codes', function (Blueprint $table) {
            $table->integer('company_user_id')->unsigned()->after('description')->nullable();
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
        Schema::table('sale_term_codes', function (Blueprint $table) {
            //
        });
    }
}
