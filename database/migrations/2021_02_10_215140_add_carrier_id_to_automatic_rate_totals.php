<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCarrierIdToAutomaticRateTotals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automatic_rate_totals', function (Blueprint $table) {
            $table->integer('carrier_id')->unsigned()->nullable()->after('origin_port_id');
            $table->foreign('carrier_id')
            ->references('id')->on('carriers')
            ->onDelete('cascade');
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
