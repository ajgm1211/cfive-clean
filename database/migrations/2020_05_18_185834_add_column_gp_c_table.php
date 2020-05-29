<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnGpCTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calculationtype', function (Blueprint $table) {
            $table->integer('group_container_id')->nullable()->unsigned()->after('gp_pcontainer');
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
