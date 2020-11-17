<?php

use Illuminate\Database\Seeder;

class IncotermTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('incoterms')->delete();
        
        \DB::table('incoterms')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'EWX',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'FAS',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'FCA',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'FOB',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'CFR',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'CIF',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'CIP',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'DAT',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'DAT',
            ),
            9 =>
            array (
                'id' => 10,
                'name' => 'DAP',
            ),
            10 =>
            array (
                'id' => 11,
                'name' => 'DDP',
            ),
        ));
    }
}