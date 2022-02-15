<?php

use Illuminate\Database\Seeder;

class InlandsProvincesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('inlands_provinces')->delete();
        
        \DB::table('inlands_provinces')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Distrito Capital',
                'region' => '',
                'country_id' => 234,
                'created_at' => '2021-06-01 19:31:01',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'carabobo',
                'region' => '',
                'country_id' => 234,
                'created_at' => '2021-06-01 19:32:32',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'zulia',
                'region' => '',
                'country_id' => 234,
                'created_at' => '2021-06-01 19:32:32',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'cartagena',
                'region' => '',
                'country_id' => 47,
                'created_at' => '2021-06-01 19:33:23',
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}