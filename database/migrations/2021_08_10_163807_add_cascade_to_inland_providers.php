<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeToInlandProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inland_providers', function (Blueprint $table) {
            $table->dropForeign(['automatic_inland_id']);
            $table->foreign('automatic_inland_id')
            ->references('id')->on('automatic_inlands')
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
        Schema::table('inland_providers', function (Blueprint $table) {
            //
        });
    }
}
