<?php

use Illuminate\Database\Seeder;

class IncotermTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('incoterms')->delete();

        \DB::table('incoterms')->insert([
            0 => [
                'id' => 1,
                'name' => 'EWX',
            ],
            1 => [
                'id' => 2,
                'name' => 'FAS',
            ],
            2 => [
                'id' => 3,
                'name' => 'FCA',
            ],
            3 => [
                'id' => 4,
                'name' => 'FOB',
            ],
            4 => [
                'id' => 5,
                'name' => 'CFR',
            ],
            5 => [
                'id' => 6,
                'name' => 'CIF',
            ],
            6 => [
                'id' => 7,
                'name' => 'CIP',
            ],
            7 => [
                'id' => 8,
                'name' => 'DAT',
            ],
            8 => [
                'id' => 9,
                'name' => 'DAT',
            ],
            9 => [
                'id' => 10,
                'name' => 'DAP',
            ],
            10 => [
                'id' => 11,
                'name' => 'DDP',
            ],
        ]);
    }
}
