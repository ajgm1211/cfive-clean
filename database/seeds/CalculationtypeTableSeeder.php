<?php

use Illuminate\Database\Seeder;

class CalculationtypeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('calculationtype')->delete();
        
        \DB::table('calculationtype')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Per 40 "',
                'code' => '40',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Per 20 "',
                'code' => '20',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Per 40 HC',
                'code' => '40HC',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Per TEU',
                'code' => 'TEU',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Per Container',
                'code' => 'CONT',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Per Shipment',
                'code' => 'SHIP',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}