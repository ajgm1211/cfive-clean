<?php

use Illuminate\Database\Seeder;

class ApiProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      
        \DB::table('api_providers')->delete();
        
        \DB::table('api_providers')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'CMA RATES',
                'description' => '',
                'image' => 'cma.png',
                'status' => 1,
                'url' => 'https://digital-services-apis.cma-cgm.com/',
                'credentials' => '{"api_key": "40528c3f-adcd-4a33-9a6f-6306b4887aaa", "auth_uri": "https://auth.cma-cgm.com", "behalfOf": "0006645192", "client_id": "beapp-cargofive", "client_secret": "hngrzQDFZuDIUgcItqFDDAizTHpQBQrWbJhzwrzO"}',
                'created_at' => NULL,
                'updated_at' => NULL,
                'code' => 'cmacgm',
                'require_login' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'MAERSK SPOT',
                'description' => '',
                'image' => 'maersk.png',
                'status' => 1,
                'url' => 'https://offers.api-cdt.maersk.com/',
                'credentials' => '{"api_key": "cargofive-dyRBk9yDhrH5VQCHQn9wBuXe4eRqECDw"}',
                'created_at' => NULL,
                'updated_at' => NULL,
                'code' => 'maersk',
                'require_login' => 0,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'SEALAND SPOT',
                'description' => NULL,
                'image' => 'sealand.png',
                'status' => 1,
                'url' => NULL,
                'credentials' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
                'code' => 'sealand',
                'require_login' => 0,
            ),
        ));
    }
}
