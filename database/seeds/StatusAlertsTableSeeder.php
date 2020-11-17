<?php

use Illuminate\Database\Seeder;

class StatusAlertsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('status_alerts')->delete();
        
        \DB::table('status_alerts')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'pending',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'false',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'solved',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}