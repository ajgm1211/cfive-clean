<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalContainersFieldsToQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('qty_20_refeer')->nullable()->after('qty_40_nor');
            $table->string('qty_40_refeer')->nullable()->after('qty_20_refeer');
            $table->string('qty_40_hc_refeer')->nullable()->after('qty_40_refeer');
            $table->string('qty_20_open_top')->nullable()->after('qty_40_hc_refeer');
            $table->string('qty_40_hc_open_top')->nullable()->after('qty_20_open_top');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn('qty_20_refeer');
            $table->dropColumn('qty_40_refeer');
            $table->dropColumn('qty_40_hc_refeer');
            $table->dropColumn('qty_20_open_top');
            $table->dropColumn('qty_40_hc_open_top');
        });
    }
}
