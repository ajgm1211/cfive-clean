<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToAutomaticInlands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automatic_inlands', function (Blueprint $table) {
            $table->integer('provider_id')->unsigned()->nullable()->after('automatic_rate_id');
            $table->foreign('provider_id')->references('id')->on('providers')->onDelete('cascade');
            $table->text('charge')->nullable()->after('port_id');
            $table->integer('inland_address_id')->unsigned()->nullable()->after('quote_id');
            $table->foreign('inland_address_id')->references('id')->on('inland_addresses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('automatic_inlands', function (Blueprint $table) {
            //
        });
    }
}
