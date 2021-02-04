<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPdfTypeFieldToCompanyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_users', function (Blueprint $table) {
            $table->tinyInteger('type_pdf')->nullable()->after('pdf_language');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_users', function (Blueprint $table) {
            $table->dropColumn('type_pdf');
        });
    }
}
