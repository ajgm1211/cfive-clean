<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeSurchargeIdToLocalChargeQuoteLclsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('local_charge_quote_lcls', function (Blueprint $table) {
            $table->dropForeign('local_charge_quote_lcls_surcharge_id_foreign');
            $table->dropIndex('local_charge_quote_lcls_surcharge_id_foreign');
            $table->foreign('surcharge_id')->references('id')->on('surcharges')->onDelete('cascade');
        });
        
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('local_charge_quote_lcls');
    }
}
