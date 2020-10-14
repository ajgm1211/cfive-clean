<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLanguageToQuotev2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quote_v2s', function (Blueprint $table) {
            $table->integer('language_id')->unsigned()->nullable()->after('contact_id');
            $table->foreign('language_id')->references('id')->on('languages');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quote_v2s', function (Blueprint $table) {
            //
        });
    }
}
