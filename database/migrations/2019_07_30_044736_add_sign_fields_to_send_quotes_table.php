<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSignFieldsToSendQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('send_quotes', function (Blueprint $table) {
            $table->enum('sign_type', ['text', 'image'])->nullable()->after('quote_id');
            $table->string('sign')->nullable()->after('sign_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('send_quotes', function (Blueprint $table) {
            //
        });
    }
}
