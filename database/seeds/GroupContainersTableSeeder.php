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
                'data' => '{"color": "#012586"}',
                'created_at' => NULL,
                'updated_at' => NULL,
                'code' => 'dry',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'REEFER',
                'data' => '{"color": "#ad43ba"}',
                'created_at' => NULL,
                'updated_at' => NULL,
                'code' => 'reefer',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'OPEN TOP',
                'data' => '{"color": "#9f9b45"}',
                'created_at' => NULL,
                'updated_at' => NULL,
                'code' => 'opentop',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'FLAT RACK',
                'data' => '{"color": "#058b0a"}',
                'created_at' => NULL,
                'updated_at' => NULL,
                'code' => 'flatrack',
            ),
        ));
        
        
    }
}