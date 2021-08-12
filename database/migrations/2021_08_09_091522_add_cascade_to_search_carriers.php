<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeToSearchCarriers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_carriers', function (Blueprint $table) {
            $table->dropForeign(['search_rate_id']);
            $table->foreign('search_rate_id')
            ->references('id')->on('search_rates')
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
        Schema::table('search_carriers', function (Blueprint $table) {
            //
        });
    }
}
