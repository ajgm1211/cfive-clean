<?php

use Illuminate\Database\Seeder;

class HarborsLocationSearchTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('harbors_location_search')->delete();
        
        \DB::table('harbors_location_search')->insert(array (
            0 => 
            array (
                'id' => 1,
                'location_id' => 3824,
                'harbors_id' => 949,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'location_id' => 3818,
                'harbors_id' => 949,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'location_id' => 3887,
                'harbors_id' => 949,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'location_id' => 3887,
                'harbors_id' => 962,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'location_id' => 3780,
                'harbors_id' => 946,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}