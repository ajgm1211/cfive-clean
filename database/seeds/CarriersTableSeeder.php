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
                'image' => 'apl_logo.png',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'CCNI',
                'image' => 'noimage.png',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'CMA CGM',
                'image' => 'noimage.png',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'COSCO',
                'image' => 'cosco.jpeg',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'CSAV',
                'image' => 'noimage.png',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Evergreen',
                'image' => 'noimage.png',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Hamburg Sud',
                'image' => 'noimage.png',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Hanjin',
                'image' => 'noimage.png',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Hapag Lloyd',
                'image' => 'noimage.png',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'HMM',
                'image' => 'noimage.png',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'K Line',
                'image' => 'noimage.png',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Maersk',
                'image' => 'noimage.png',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'MOL',
                'image' => 'noimage.png',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'MSC',
                'image' => 'noimage.png',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'NYK Line',
                'image' => 'noimage.png',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'OOCL',
                'image' => 'noimage.png',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'PIL',
                'image' => 'noimage.png',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'Safmarine',
                'image' => 'noimage.png',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'UASC',
                'image' => 'noimage.png',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'Wan Hai Lines',
                'image' => 'noimage.png',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'YML',
                'image' => 'noimage.png',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'ZIM',
                'image' => 'noimage.png',
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'Otro',
                'image' => 'noimage.png',
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'Sealand',
                'image' => 'noimage.png',
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'ONE',
                'image' => 'noimage.png',
            ),
        ));
        
        
    }
}