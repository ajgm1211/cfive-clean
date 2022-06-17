<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateQuoteV2sView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_requests', function (Blueprint $table) {
            $table->dropForeign('api_requests_price_level_id_foreign');
            $table->foreign('price_level_id')->references('id')->on('price_levels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_requests', function (Blueprint $table) {
            $table->dropForeign('api_requests_price_level_id_foreign');
            $table->foreign('price_level_id')->references('id')->on('price_levels');
        });
    }
}
