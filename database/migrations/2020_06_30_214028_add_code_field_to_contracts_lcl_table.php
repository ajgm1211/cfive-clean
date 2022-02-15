<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodeFieldToContractsLclTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts_lcl', function (Blueprint $table) {
            $table->string('type')->after('direction_id')->nullable();
            $table->string('code')->after('type')->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts_lcl', function (Blueprint $table) {
            //
        });
    }
}
