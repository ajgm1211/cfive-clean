<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTermsAndPaymentsConditionsFieldsToQuotev2sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quote_v2s', function (Blueprint $table) {
            $table->string('payment_conditions',5000)->nullable()->after('remarks');
            $table->string('terms_and_conditions',5000)->nullable()->after('payment_conditions');
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
            $table->dropColumn('payment_conditions');
            $table->dropColumn('terms_and_conditions');
        });
    }
}
