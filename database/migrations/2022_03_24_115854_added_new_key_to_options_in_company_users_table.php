<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedNewKeyToOptionsInCompanyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
           $companyUser = DB::table('company_users')->get();

        foreach($companyUser as $company){
            $array=json_decode($company->options,true);
            $options = json_encode([
                "api_providers" => empty($array['api_providers'])==true ? [] : $array['api_providers'],
                "company_address_pdf"=> empty($array['company_address_pdf'])==true ? 1 : $array['company_address_pdf'],
                "store_hidden_charges" => empty($array['store_hidden_charges'])==true ? false : $array['store_hidden_charges'],
                "totals_in_freight_currency"=> empty($array['totals_in_freight_currency'])==true ? false : $array['totals_in_freight_currency'],
                "contract_upload" => 'web'
            ]);

            DB::table('company_users')
                ->where('id', $company->id)
                ->update(['options' => $options]);

        }
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