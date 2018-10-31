<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFortyNorAndFiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('failes_rates', function (Blueprint $table) {
            $table->string('fortynor')->after('fortyhc');
            $table->string('fortyfive')->after('fortyhc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('failes_rates');
    }
}
