<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemarksFieldAutomaticRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automatic_rates', function (Blueprint $table) {
            //$table->string('remarks',5000)->nullable()->after('markups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('automatic_rates', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
}
