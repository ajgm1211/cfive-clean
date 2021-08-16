<?php

use Illuminate\Database\Seeder;

class LocationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('locations')->delete();
        
        \DB::table('locations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'CARACAS',
                'province_id' => 1,
                'identifier' => 2,
                'created_at' => NULL,
                'updated_at' => '2021-06-23 15:45:10',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'VALENCIA',
                'province_id' => 2,
                'identifier' => 2,
                'created_at' => '2021-06-23 15:45:10',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'MARACAIBO',
                'province_id' => 3,
                'identifier' => 2,
                'created_at' => '2021-06-23 15:45:10',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'CARTAGENA',
                'province_id' => 4,
                'identifier' => 2,
                'created_at' => '2021-06-23 15:45:10',
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}