<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeSurchargeIdToLocalChargeQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('local_charge_quotes', function (Blueprint $table) {
            $table->dropForeign('local_charge_quotes_surcharge_id_foreign');
            $table->dropIndex('local_charge_quotes_surcharge_id_foreign');
            $table->dropForeign('local_charge_quotes_port_id_foreign');
            $table->dropIndex('local_charge_quotes_port_id_foreign');
            $table->foreign('surcharge_id')->references('id')->on('surcharges')->onDelete('cascade');
            $table->foreign('port_id')->references('id')->on('harbors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('local_charge_quotes', function (Blueprint $table) {
            //
        });
    }
}
