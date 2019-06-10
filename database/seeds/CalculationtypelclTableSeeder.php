<?php

use Illuminate\Database\Seeder;

class CalculationtypelclTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('calculationtypelcl')->delete();
        
        \DB::table('calculationtypelcl')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Per HBL',
                'code' => 'HBL',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Per Shipment',
                'code' => 'SHIP',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Per BL',
                'code' => 'BL',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'TON/M3',
                'code' => 'TON/M3',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'PER TON',
                'code' => 'TON',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
            'name' => 'PER TON (TON o M3)',
                'code' => 'TON-M3',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
            'name' => 'PER M3 (TON o M3)',
                'code' => 'M3-TON',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Per Invoice',
                'code' => 'INV',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}