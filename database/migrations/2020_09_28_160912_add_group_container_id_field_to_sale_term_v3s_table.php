<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupContainerIdFieldToSaleTermV3sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_term_v3s', function (Blueprint $table) {
            $table->integer('group_container_id')->unsigned();
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
        Schema::table('sale_term_v3s', function (Blueprint $table) {
            //
        });
    }
}
