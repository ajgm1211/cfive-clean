<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCarrierToInlands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inlands', function (Blueprint $table) {
            $table->integer('carrier_id')->unsigned()->nullable()->after('inland_type_id');
            $table->foreign('carrier_id')->references('id')->on('carriers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inlands', function (Blueprint $table) {
            //
        });
    }
}
