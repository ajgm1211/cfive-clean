<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSaleTermCodeIdToLocalChargeQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*Schema::table('local_charge_quotes', function (Blueprint $table) {
            $table->integer('sale_term_code_id')->after('surcharge_id')->unsigned()->nullable();
            $table->foreign('sale_term_code_id')->references('id')->on('sale_term_codes');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('local_charge_quotes', function (Blueprint $table) {
            //
        });
    }
}
