<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOndeleteToAutomaticInlandTotals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automatic_inland_totals', function (Blueprint $table) {
            $table->dropForeign('automatic_inland_totals_inland_address_id_foreign');
            $table->foreign('inland_address_id')
            ->references('id')->on('inland_addresses')
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
        //
    }
}
