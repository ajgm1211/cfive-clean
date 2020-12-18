<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAddressIdFromAutomaticInlandsLcl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automatic_inland_lcl_airs', function (Blueprint $table) {
            $table->dropForeign('automatic_inland_lcl_airs_inland_address_id_foreign');
            $table->dropColumn('inland_address_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
