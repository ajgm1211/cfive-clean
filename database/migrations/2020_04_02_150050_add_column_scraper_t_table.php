<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnScraperTTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scrapers_tokens', function (Blueprint $table){
            $table->integer('carrier_id')->nullable()->after('token')->unsigned();
            $table->boolean('validator')->default(false)->after('carrier_id');
            $table->boolean('duplicated')->default(false)->after('validator');
            $table->foreign('carrier_id')->references('id')->on('carriers');
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
