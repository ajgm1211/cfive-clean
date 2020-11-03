<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangingWonToWinQuoteStatus extends Migration
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
        if(DB::table('status_quotes')->where('name', 'Won') != null){
            DB::table('status_quotes')->where('name', 'Won')->update(['name' => 'Win']);
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
