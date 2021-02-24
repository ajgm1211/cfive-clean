<?php

use Illuminate\Database\Seeder;

class CarriersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('carriers')->delete();

        \DB::table('carriers')->insert([
            0 => [
                'id' => 1,
                'name' => 'APL',
                'image' => 'apl.png',
            ],
            1 => [
                'id' => 2,
                'name' => 'CCNI',
                'image' => 'ccni.png',
            ],
            2 => [
                'id' => 3,
                'name' => 'CMA CGM',
                'image' => 'cma.png',
            ],
            3 => [
                'id' => 4,
                'name' => 'COSCO',
                'image' => 'cosco.png',
            ],
            4 => [
                'id' => 5,
                'name' => 'CSAV',
                'image' => 'csav.png',
            ],
            5 => [
                'id' => 6,
                'name' => 'Evergreen',
                'image' => 'evergreen.png',
            ],
            6 => [
                'id' => 7,
                'name' => 'Hamburg Sud',
                'image' => 'hamburgsud.png',
            ],
            7 => [
                'id' => 8,
                'name' => 'Hanjin',
                'image' => 'hanjin.png',
            ],
            8 => [
                'id' => 9,
                'name' => 'Hapag Lloyd',
                'image' => 'hapaglloyd.png',
            ],
            9 => [
                'id' => 10,
                'name' => 'HMM',
                'image' => 'hmm.png',
            ],
            10 => [
                'id' => 11,
                'name' => 'K Line',
                'image' => 'kline.png',
            ],
            11 => [
                'id' => 12,
                'name' => 'Maersk',
                'image' => 'maersk.png',
            ],
            12 => [
                'id' => 13,
                'name' => 'MOL',
                'image' => 'mol.png',
            ],
            13 => [
                'id' => 14,
                'name' => 'MSC',
                'image' => 'msc.png',
            ],
            14 => [
                'id' => 15,
                'name' => 'NYK Line',
                'image' => 'nyk.png',
            ],
            15 => [
                'id' => 16,
                'name' => 'OOCL',
                'image' => 'oocl.png',
            ],
            16 => [
                'id' => 17,
                'name' => 'PIL',
                'image' => 'pil.png',
            ],
            17 => [
                'id' => 18,
                'name' => 'Safmarine',
                'image' => 'safmarine.png',
            ],
            18 => [
                'id' => 19,
                'name' => 'UASC',
                'image' => 'uasc.png',
            ],
            19 => [
                'id' => 20,
                'name' => 'Wan Hai Lines',
                'image' => 'wanhai.png',
            ],
            20 => [
                'id' => 21,
                'name' => 'YML',
                'image' => 'yml.png',
            ],
            21 => [
                'id' => 22,
                'name' => 'ZIM',
                'image' => 'zim.png',
            ],
            22 => [
                'id' => 23,
                'name' => 'Otro',
                'image' => 'noimage.png',
            ],
            23 => [
                'id' => 24,
                'name' => 'Sealand',
                'image' => 'sealand.png',
            ],
            24 => [
                'id' => 25,
                'name' => 'ONE',
                'image' => 'one.png',
            ],
            25 => [
                'id' => 26,
                'name' => 'ALL',
                'image' => 'all.png',
            ],
        ]);
    }
}
