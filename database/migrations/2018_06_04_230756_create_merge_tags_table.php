<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMergeTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mergeTags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name');
            $table->string('client_name');
            $table->string('client_phone');
            $table->string('client_email');
            $table->string('quote_number');
            $table->string('quote_total');
            $table->string('destination');
            $table->string('origin');
            $table->string('carrier');
            $table->string('user_name');
            $table->string('user_email');
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
        Schema::dropIfExists('mergeTags');
    }
}
