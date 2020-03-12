<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnRquestDuplicatedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('accounts_import_cfcl', function (Blueprint $table){
            $table->integer('request_dp_id')->nullable()->after('request_id')->unsigned();
            $table->foreign('request_dp_id')->references('id')->on('newcontractrequests');
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
