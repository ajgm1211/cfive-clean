<?php

use Illuminate\Database\Seeder;

class ScheduleTypeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('schedule_type')->delete();
        
        \DB::table('schedule_type')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Direct',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Transfer',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}