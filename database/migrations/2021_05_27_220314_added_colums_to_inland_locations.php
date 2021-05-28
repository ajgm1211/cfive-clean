<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedColumsToInlandLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inland_locations', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('region');
            $table->dropColumn('country_id');
            $table->dropColumn('company_user_id');
            $table->string('name');
            $table->json('json_container')->nullable();
            $table->integer('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currency');
            $table->integer('harbor_id')->unsigned();
            $table->foreign('harbor_id')->references('id')->on('harbors');
            $table->integer('inland_id')->unsigned();
            $table->integer('location_id')->unsigned();
            $table->foreign('location_id')->references('id')->on('location');
            $table->integer('type_id')->unsigned();
            $table->foreign('type_id')->references('id')->on('typedestiny');

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
        Schema::table('inland_locations', function (Blueprint $table) {
            //
        });
    }
}
