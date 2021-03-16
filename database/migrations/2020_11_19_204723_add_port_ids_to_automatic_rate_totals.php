<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPortIdsToAutomaticRateTotals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automatic_rate_totals', function (Blueprint $table) {
            $table->integer('origin_port_id')->unsigned()->nullable()->after('automatic_rate_id');
            $table->foreign('origin_port_id')->references('id')->on('harbors');
            $table->integer('destination_port_id')->unsigned()->nullable()->after('automatic_rate_id');
            $table->foreign('destination_port_id')->references('id')->on('harbors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
