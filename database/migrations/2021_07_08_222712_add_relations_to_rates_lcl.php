<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelationsToRatesLcl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rates_lcl', function (Blueprint $table) {
            $table->integer('surcharge_id')->nullable()->after('schedule_type_id')->unsigned();
            $table->foreign('surcharge_id')->references('id')->on('surcharges');

            $table->integer('calculationtype_id')->nullable()->after('surcharge_id')->unsigned();
            $table->foreign('calculationtype_id')->references('id')->on('calculationtypelcl');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rates_lcl', function (Blueprint $table) {
            //
        });
    }
}
