<?php

use Illuminate\Database\Seeder;

class GroupContainersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('group_containers')->delete();
        
        \DB::table('group_containers')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'DRY',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'REEFER',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'OPEN TOP',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'FLAT RACK',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}