<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContactIdToSearchRates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('search_rates', function (Blueprint $table) {
            $table->integer('contact_id')->unsigned()->nullable()->after('company_id');
            $table->foreign('contact_id')
            ->references('id')->on('contacts')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('search_rates', function (Blueprint $table) {
            //
        });
    }
}
