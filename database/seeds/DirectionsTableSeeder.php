<?php

use Illuminate\Database\Seeder;

class DirectionsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('directions')->delete();

        \DB::table('directions')->insert([
            0 => [
                'id' => 1,
                'name' => 'Import',
                'created_at' => null,
                'updated_at' => null,
            ],
            1 => [
                'id' => 2,
                'name' => 'Export',
                'created_at' => null,
                'updated_at' => null,
            ],
            2 => [
                'id' => 3,
                'name' => 'Both',
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
