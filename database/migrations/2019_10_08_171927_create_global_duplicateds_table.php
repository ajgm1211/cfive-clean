<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalDuplicatedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_duplicateds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('global_id')->unsigned();
            $table->integer('global_dp_id')->unsigned();
            $table->integer('gp_global_dp_id')->unsigned();
            $table->foreign('global_id')->references('id')->on('globalcharges')->onDelete('cascade');
            $table->foreign('global_dp_id')->references('id')->on('globalcharges')->onDelete('cascade');
            $table->foreign('gp_global_dp_id')->references('id')->on('group_globals_company_users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_duplicateds');
    }
}
