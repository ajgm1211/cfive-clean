<?php

use Illuminate\Database\Seeder;

class PriceSubtypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = Carbon\Carbon::now();

        \DB::table('price_subtypes')->delete();

        \DB::table('price_subtypes')->insert(array (
            0 =>
                array (
                    'id' => 1,
                    'name' => 'Import',
                    'created_at' => $date,
                    'updated_at' => $date,
                ),
            1 =>
                array (
                    'id' => 2,
                    'name' => 'Export',
                    'created_at' => $date,
                    'updated_at' => $date,
                ),
        ));
    }
}
