<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPricelevelIdToSearchRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_rates', function (Blueprint $table) {
            $table->integer('price_level_id')->unsigned()->nullable()->after('contact_id');
            $table->foreign('price_level_id')
            ->references('id')->on('prices')
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
        Schema::table('search_rates', function (Blueprint $table) {
            //
        });
    }
}
