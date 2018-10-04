<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPdfAmmountsFieldToCompanyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_users', function (Blueprint $table) {
            $table->tinyInteger('pdf_ammounts')->nullable()->after('type_pdf');
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
            $table->dropColumn('pdf_ammounts');
        });
    }
}
