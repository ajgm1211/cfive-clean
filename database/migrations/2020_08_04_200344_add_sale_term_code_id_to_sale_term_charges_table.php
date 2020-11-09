<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSaleTermCodeIdToSaleTermChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_term_charges', function (Blueprint $table) {
            $table->integer('sale_term_code_id')->unsigned()->after('amount')->nullable();
            $table->foreign('sale_term_code_id')->references('id')->on('sale_term_codes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_term_charges', function (Blueprint $table) {
            //
        });
    }
}
