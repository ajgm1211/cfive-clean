<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\StatusQuote;

class DeleteOldStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(StatusQuote::where('name', 'Negotiated')->first() != null){
            StatusQuote::where('name', 'Negotiated')->first()->delete();
        }
        if(StatusQuote::where('name', 'Lost')->first() != null){
            StatusQuote::where('name', 'Lost')->first()->delete();
        }
        if(DB::table('status_quotes')->where('name', 'Win') != null){
            DB::table('status_quotes')->where('name', 'Win')->update(['name' => 'Won']);
        }
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
