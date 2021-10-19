<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsertFieldsToPdfTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pdf_templates', function (Blueprint $table) {
            DB::table('pdf_templates')
            ->insert(  ["id" => 1,"name" => "No Header", "description"=>null]);
            DB::table('pdf_templates') 
            ->insert(  ["id" => 2,"name" => "With Header", "description"=>null]);     
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pdf_templates', function (Blueprint $table) {
            //
        });
    }
}
