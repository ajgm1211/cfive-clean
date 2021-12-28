<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeToPriceLevelGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_level_groups', function (Blueprint $table) {
            $table->dropForeign(['price_level_id']);
            $table->foreign('price_level_id')
            ->references('id')->on('price_levels')
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
        Schema::table('price_level_groups', function (Blueprint $table) {
            //
        });
    }
}
