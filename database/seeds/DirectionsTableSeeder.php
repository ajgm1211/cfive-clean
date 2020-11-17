<?php

use Illuminate\Database\Seeder;

class DirectionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('directions')->delete();
        
        \DB::table('directions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Import',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Export',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Both',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}