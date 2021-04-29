<?php

use App\Carrier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FormatSearchCarriersToPolymorphic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_carriers', function (Blueprint $table) {
            $table->dropForeign(['carrier_id']);
            $table->dropIndex('search_carriers_carrier_id_foreign');
            $table->renameColumn('carrier_id', 'provider_id');
            
            
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_carriers', function (Blueprint $table) {
            //
        });
    }
}
