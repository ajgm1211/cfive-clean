<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMasterSurcharger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_surcharges', function (Blueprint $table) {
            $table->integer('group_container_id')->unsigned()->after('direction_id')->nullable();
            $table->foreign('group_container_id')->references('id')->on('group_containers');
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
