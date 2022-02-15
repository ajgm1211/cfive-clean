<?php

use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('languages')->delete();

        \DB::table('languages')->insert([
            0 => [
                'id' => 1,
                'name' => 'English',
                'created_at' => null,
                'updated_at' => null,
            ],
            1 => [
                'id' => 2,
                'name' => 'Spanish',
                'created_at' => null,
                'updated_at' => null,
            ],
            2 => [
                'id' => 3,
                'name' => 'Portuguese',
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
