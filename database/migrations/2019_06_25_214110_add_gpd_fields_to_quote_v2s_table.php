<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGpdFieldsToQuoteV2sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quote_v2s', function (Blueprint $table) {
            $table->boolean('gdp')->nullable()->after('kind_of_cargo');
            $table->string('risk_level')->nullable()->after('gdp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quote_v2s', function (Blueprint $table) {
            //
        });
    }
}
