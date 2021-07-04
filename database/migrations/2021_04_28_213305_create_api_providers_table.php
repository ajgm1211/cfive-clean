<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('api_providers')) {
            Schema::create('api_providers', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('code');
                $table->string('description')->nullable();
                $table->boolean('status')->default(true)->nullable();
                $table->json('json_settings')->nullable();
                $table->string('url')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_providers');
    }
}
