<?php

use Illuminate\Database\Seeder;

class ContainersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('containers')->delete();
        
        \DB::table('containers')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => '20 DV',
                'code' => '20DV',
                'gp_container_id' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => '40 DV',
                'code' => '40DV',
                'gp_container_id' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => '40 HC',
                'code' => '40HC',
                'gp_container_id' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => '45 HC',
                'code' => '45HC',
                'gp_container_id' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => '40 NOR',
                'code' => '40NOR',
                'gp_container_id' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => '20 RF',
                'code' => '20RF',
                'gp_container_id' => 2,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'name' => '40 RF',
                'code' => '40RF',
                'gp_container_id' => 2,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => '40 HCRF',
                'code' => '40HCRF',
                'gp_container_id' => 2,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => '20 OT',
                'code' => '20OT',
                'gp_container_id' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'name' => '40 OT',
                'code' => '40OT',
                'gp_container_id' => 3,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'name' => '20 FR',
                'code' => '20FR',
                'gp_container_id' => 4,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'name' => '40 FR',
                'code' => '40FR',
                'gp_container_id' => 4,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}