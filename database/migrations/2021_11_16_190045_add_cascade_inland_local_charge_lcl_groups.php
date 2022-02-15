<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeInlandLocalChargeLclGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inland_local_charge_lcl_groups', function (Blueprint $table) {
            $table->dropForeign(['automatic_inland_lcl_id']);
            $table->foreign('automatic_inland_lcl_id')
            ->references('id')->on('automatic_inland_lcl_airs')
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
        Schema::table('inland_local_charge_lcl_groups', function (Blueprint $table) {
            //
        });
    }
}
