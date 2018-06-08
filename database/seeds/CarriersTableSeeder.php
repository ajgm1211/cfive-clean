<?php

use Illuminate\Database\Seeder;

class CarriersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('carriers')->delete();
        
        \DB::table('carriers')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'APL',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'CCNI',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'CMA CGM',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'COSCO',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'CSAV',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Evergreen',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Hamburg Sud',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Hanjin',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Hapag Lloyd',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'HMM',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'K Line',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Maersk',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'MOL',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'MSC',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'NYK Line',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'OOCL',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'PIL',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'Safmarine',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'UASC',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'Wan Hai Lines',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'YML',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'ZIM',
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'Otro',
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'Sealand',
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'ONE',
            ),
        ));
        
        
    }
}