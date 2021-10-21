<?php

use Illuminate\Database\Seeder;

class DistancesKmLocationTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('distances_km_location')->delete();
        
        \DB::table('distances_km_location')->insert(array (
            0 => 
            array (
                'id' => 1,
                'distance'=> 5,
                'location_id' => 3824,
                'harbors_id' => 949,
                'created_at' => '2021-08-11 02:32:01',
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'distance'=> 35,
                'location_id' => 3818,
                'harbors_id' => 949,
                'created_at' => '2021-08-11 02:32:02',
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'distance'=> 75,
                'location_id' => 3887,
                'harbors_id' => 949,
                'created_at' => '2021-08-11 02:32:03',
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'distance'=> 432,
                'location_id' => 3887,
                'harbors_id' => 962,
                'created_at' => '2021-08-11 02:32:04',
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'distance'=> 457,
                'location_id' => 3780,
                'harbors_id' => 946,
                'created_at' => '2021-08-11 02:32:05',
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}