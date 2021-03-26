<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChargesInfoToSearchRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_rates', function (Blueprint $table) {
            $table->boolean('origin_charges')->nullable()->after('type');
            $table->boolean('destination_charges')->nullable()->after('origin_charges');            
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
