<?php

use Illuminate\Database\Seeder;

class CompanyUserApiProvidersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = DB::table('company_users')->get();
        $providers = DB::table('api_providers')->get();

        $companies_with_providers = [
            'maersk' => [ 88 ],
            'cmacgm' => [],            
        ];

        $companies_with_providers['sealand'] = $companies_with_providers['maersk'];

        foreach($companies as $company){
            $final_providers = [];

            foreach($providers as $provider){
                if(in_array($company->id, $companies_with_providers[$provider->code])){
                    array_push($final_providers,$provider->id);
                }
            }

            $options = json_decode($company->options,true);

            $options['api_providers'] = $final_providers;

            $options_json = json_encode($options);

            DB::table('company_users')
                ->where('id', $company->id)
                ->update(['options' => $options_json]);
        }
    }
}
