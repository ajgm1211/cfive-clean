<?php

use Illuminate\Database\Seeder;

class QuoteSegmentTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name'=> 'freight',
            ],
            [
                'name'=> 'origin',
            ],
            [
                'name'=> 'destination',
            ],
            [
                'name'=> 'inlands'
            ]
        ];
        
        DB::table('quote_segment_types')->insert($data);
    }
}