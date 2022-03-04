<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFailOverweightRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fail_overweight_ranges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('lower_limit')->nullable();
            $table->string('upper_limit')->nullable();
            $table->morphs('model');
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
        Schema::dropIfExists('fail_overweight_ranges');
    }
}
