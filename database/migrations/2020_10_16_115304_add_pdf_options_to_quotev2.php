<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPdfOptionsToQuotev2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quote_v2s', function (Blueprint $table) {
            $table->json('pdf_options')->nullable()->after('terms_portuguese');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quote_v2s', function (Blueprint $table) {
            //
        });
    }
}
