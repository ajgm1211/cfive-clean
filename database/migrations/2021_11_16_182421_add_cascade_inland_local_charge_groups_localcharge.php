<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeInlandLocalChargeGroupsLocalcharge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inland_local_charge_groups', function (Blueprint $table) {
            $table->dropForeign(['local_charge_quote_id']);
            $table->foreign('local_charge_quote_id')
            ->references('id')->on('local_charge_quotes')
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
        Schema::table('inland_local_charge_groups', function (Blueprint $table) {
            //
        });
    }
}
