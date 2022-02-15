<?php

use Illuminate\Database\Seeder;

class PriceTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = Carbon\Carbon::now();

        \DB::table('price_types')->delete();

        \DB::table('price_types')->insert([
            0 => [
                    'id' => 1,
                    'name' => 'Sea Freights FCL',
                    'created_at' => $date,
                    'updated_at' => $date,
                ],
            1 => [
                    'id' => 2,
                    'name' => 'Sea Freights LCL',
                    'created_at' => $date,
                    'updated_at' => $date,
                ],
            2 => [
                    'id' => 3,
                    'name' => 'Air Freights',
                    'created_at' => $date,
                    'updated_at' => $date,
                ],
        ]);
    }
}
