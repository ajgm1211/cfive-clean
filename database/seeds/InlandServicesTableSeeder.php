<?php

use Illuminate\Database\Seeder;

class InlandServicesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('inland_services')->delete();
        
        \DB::table('inland_services')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'RAMP',
                'created_at' => '2021-06-23 15:55:29',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'MOTOR',
                'created_at' => '2021-06-23 15:55:29',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'MOTOR ALL',
                'created_at' => '2021-06-23 15:55:29',
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}