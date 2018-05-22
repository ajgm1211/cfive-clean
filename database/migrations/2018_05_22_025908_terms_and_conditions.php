<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TermsAndConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('termsAndConditions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('port')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('import');
            $table->string('export');
            $table->foreign('port')->references('id')->on('harbors');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('termsAndConditions');
    }
}
