<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProcesdureSurchargeList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS surcharge_list_proc;CREATE PROCEDURE surcharge_list_proc (IN company_user int) SELECT sr.id,sr.name,sr.description,(CASE WHEN strm.name != '' THEN strm.name ELSE '-------' END) as sale_term,REPLACE(REPLACE(sr.variation->'$.type','[',''),']','') as variations,(CASE WHEN cmpu.name != '' THEN cmpu.name ELSE 'General' END) as company_user from surcharges sr LEFT JOIN company_users cmpu ON cmpu.id=sr.company_user_id LEFT JOIN sale_terms strm ON strm.id=sr.sale_term_id WHERE sr.company_user_id IS NULL OR sr.company_user_id=company_user;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
