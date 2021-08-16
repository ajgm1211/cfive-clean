<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeToAutomaticRateTotals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automatic_rate_totals', function (Blueprint $table) {
            Schema::table('automatic_rate_totals', function (Blueprint $table) {
                $table->dropForeign(['quote_id']);
                $table->foreign('quote_id')
                ->references('id')->on('quote_v2s')
                ->onDelete('cascade');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('automatic_rate_totals', function (Blueprint $table) {
            //
        });
    }
}
