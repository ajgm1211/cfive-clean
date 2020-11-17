<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInlandRangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inland_ranges', function (Blueprint $table) {
            $table->dropColumn(['upper', 'lower']);
        });

        Schema::table('inland_ranges', function (Blueprint $table) {
            $table->integer('upper')->default(0);
            $table->integer('lower')->default(0);
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
