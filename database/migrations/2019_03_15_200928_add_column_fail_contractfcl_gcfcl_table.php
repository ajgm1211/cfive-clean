<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnFailContractfclGcfclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('failes_surcharges', function (Blueprint $table) {
            $table->integer('differentiator')->nullable()->after('carrier_id');
        });
        Schema::table('failed_globalchargers', function (Blueprint $table) {
            $table->integer('differentiator')->nullable()->after('account_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('failes_surcharges', function ($table) {
            $table->dropColumn('differentiator');
        });
        Schema::table('failed_globalchargers', function ($table) {
            $table->dropColumn('differentiator');
        });
    }
}
