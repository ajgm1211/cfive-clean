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
            6 => 
            array (
                'id' => 7,
                'name' => 'Per 40 NOR',
                'code' => '40NOR',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Per 45',
                'code' => '45',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Per BL',
                'code' => 'BL',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Per TON',
                'code' => 'TON',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Per Invoice',
                'code' => 'INV',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}