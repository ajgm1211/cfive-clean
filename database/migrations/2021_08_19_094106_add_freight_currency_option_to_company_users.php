<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFreightCurrencyOptionToCompanyUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_users', function (Blueprint $table) {
            $companies = DB::table('company_users')->get();

            foreach($companies as $company){

                $options = json_decode($company->options,true);

                $options['totals_in_freight_currency'] = 1;

                $options_json = json_encode($options);

                DB::table('company_users')
                    ->where('id', $company->id)
                    ->update(['options' => $options_json]);
            }
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
            //
        });
    }
}
