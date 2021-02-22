<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOndeleteToAutomaticInlands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('automatic_inland_lcl_airs', function (Blueprint $table) {
            $table->dropForeign('automatic_inland_lcl_airs_provider_id_foreign');
            $table->foreign('provider_id')
            ->references('id')->on('providers')
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
