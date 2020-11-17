<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShowTotalFreightInFieldsInPdfOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pdf_options', function (Blueprint $table) {
            $table->boolean('show_total_freight_in')->nullable()->after('replace_total_title');
            $table->enum('show_total_freight_in_currency',['USD','EUR'])->nullable()->after('show_total_freight_in');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pdf_options', function (Blueprint $table) {
            //
        });
    }
}
