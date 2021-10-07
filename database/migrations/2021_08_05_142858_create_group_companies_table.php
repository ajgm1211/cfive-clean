<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('status');
            $table->integer('company_user_id')->unsigned();
            $table->foreign('company_user_id')->references('id')->on('company_users');

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
        Schema::dropIfExists('company_groups');
    }
}
