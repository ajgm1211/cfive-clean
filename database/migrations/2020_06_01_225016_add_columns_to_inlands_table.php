<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToInlandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inlands', function (Blueprint $table) {
            $table->integer('direction_id')->nullable()->unsigned();
            $table->foreign('direction_id')->references('id')->on('directions');
            $table->integer('inland_type_id')->nullable()->unsigned();
            $table->foreign('inland_type_id')->references('id')->on('inland_types');
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
