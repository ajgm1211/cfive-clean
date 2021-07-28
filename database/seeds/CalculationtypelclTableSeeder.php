<?php

use Illuminate\Database\Seeder;

class CalculationtypelclTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('calculationtypelcl')->delete();

        \DB::table('calculationtypelcl')->insert([
            0 => [
                'id' => 1,
                'name' => 'Per HBL',
                'code' => 'HBL',
                'created_at' => null,
                'updated_at' => null,
            ],
            1 => [
                'id' => 2,
                'name' => 'Per Shipment',
                'code' => 'SHIP',
                'created_at' => null,
                'updated_at' => null,
            ],
            2 => [
                'id' => 3,
                'name' => 'Per BL',
                'code' => 'BL',
                'created_at' => null,
                'updated_at' => null,
            ],
            3 => [
                'id' => 4,
                'name' => 'TON/M3',
                'code' => 'TON/M3',
                'created_at' => null,
                'updated_at' => null,
            ],
            4 => [
                'id' => 5,
                'name' => 'PER TON',
                'code' => 'TON',
                'created_at' => null,
                'updated_at' => null,
            ],
            5 => [
                'id' => 6,
            'name' => 'PER TON (TON o M3)',
                'code' => 'TON-M3',
                'created_at' => null,
                'updated_at' => null,
            ],
            6 => [
                'id' => 7,
            'name' => 'PER M3 (TON o M3)',
                'code' => 'M3-TON',
                'created_at' => null,
                'updated_at' => null,
            ],
            7 => [
                'id' => 8,
                'name' => 'Per Invoice',
                'code' => 'INV',
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
