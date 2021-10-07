<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesLevelAppliedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_level_applies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        DB::table('price_level_applies')->insert([
            ['name' => 'Freight'],
            ['name' => 'Surcharge'],
            ['name' => 'Inland'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_level_applies');
    }
}
