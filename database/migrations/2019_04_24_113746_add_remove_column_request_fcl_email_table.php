<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRemoveColumnRequestFclEmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('newcontractrequests', function (Blueprint $table) {
            $table->boolean('sentemail')->default(false)->after('time_star_one');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('newcontractrequests', function ($table) {
            $table->dropColumn('sentemail');
        });
    }
}
