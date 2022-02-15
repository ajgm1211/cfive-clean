<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('qty_20_reefer')->nullable()->after('qty_40_nor');
            $table->string('qty_40_reefer')->nullable()->after('qty_20_reefer');
            $table->string('qty_40_hc_reefer')->nullable()->after('qty_40_reefer');
            $table->string('qty_20_open_top')->nullable()->after('qty_40_hc_reefer');
            $table->string('qty_40_open_top')->nullable()->after('qty_20_open_top');
            $table->string('qty_40_hc_open_top')->nullable()->after('qty_40_open_top');
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
            $table->dropColumn('qty_20_reefer');
            $table->dropColumn('qty_40_reefer');
            $table->dropColumn('qty_40_hc_reefer');
            $table->dropColumn('qty_20_open_top');
            $table->dropColumn('qty_40_open_top');
            $table->dropColumn('qty_40_hc_open_top');
        });
    }
}
