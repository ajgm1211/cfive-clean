<?php

use Illuminate\Database\Seeder;

class TypedestinyTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('typedestiny')->delete();

        \DB::table('typedestiny')->insert([
            0 => [
                'id' => 1,
                'description' => 'origin',
            ],
            1 => [
                'id' => 2,
                'description' => 'destiny',
            ],
            2 => [
                'id' => 3,
                'description' => 'freight',
            ],
        ]);
    }
}
