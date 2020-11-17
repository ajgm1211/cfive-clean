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
            $table->string('tag_name');
            $table->string('company_name');
            $table->string('client_name');
            $table->string('client_phone');
            $table->string('client_email');
            $table->integer('quote_number')->nullable();
            $table->float('quote_total')->nullable();
            $table->string('destination')->nullable();
            $table->string('origin')->nullable();
            $table->string('carrier')->nullable();
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
