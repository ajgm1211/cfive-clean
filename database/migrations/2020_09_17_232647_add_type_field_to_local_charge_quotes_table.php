<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeFieldToLocalChargeQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('local_charge_quotes', function (Blueprint $table) {
            $table->integer('type_id')->unsigned()->after('quote_id');
            $table->foreign('type_id')->references('id')->on('typedestiny');
        });
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
