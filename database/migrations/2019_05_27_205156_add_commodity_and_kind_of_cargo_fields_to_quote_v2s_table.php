<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommodityAndKindOfCargoFieldsToQuoteV2sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quote_v2s', function (Blueprint $table) {
            $table->string('commodity')->nullable()->after('validity_end');
            $table->enum('kind_of_cargo',['General','Perishable','Dangerous','Valuable Cargo','All Live Animals','Human Remains','Pharma'])->nullable()->after('commodity');
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
            $table->dropColumn('commodity');
            $table->dropColumn('kind_of_cargo');
        });
    }
}
