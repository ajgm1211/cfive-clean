<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressesToSearchRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_rates', function (Blueprint $table) {
            $table->text('origin_address')->nullable()->after('destination_charges');
            $table->text('destination_address')->nullable()->after('origin_address');
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
