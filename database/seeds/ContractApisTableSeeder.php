<?php

use Illuminate\Database\Seeder;

class ContractApisTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('contractApis')->delete();
        
        \DB::table('contractApis')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Prueba Api',
                'number' => '12',
                'validity' => '2019-09-01',
                'expire' => '2019-09-30',
                'status' => 'publish',
                'remarks' => NULL,
                'company_user_id' => 1,
                'account_id' => NULL,
                'direction_id' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}