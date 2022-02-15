<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldInlands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inlands', function (Blueprint $table) {
            $table->enum('status', ['publish', 'expired']);
            $table->integer('gp_container_id')->nullable()->unsigned();
            $table->foreign('gp_container_id')->references('id')->on('group_containers');
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
