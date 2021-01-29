<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTotalsIdFromAutomaticInlandsLcl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automatic_inland_lcl_airs', function (Blueprint $table) {
            $table->integer('inland_totals_id')->unsigned()->nullable()->after('automatic_rate_id');
            $table->foreign('inland_totals_id')
            ->references('id')->on('automatic_inland_totals')
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
