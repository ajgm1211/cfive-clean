<?php

use Illuminate\Database\Seeder;

class CargoTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('cargo_types')->delete();
        
        \DB::table('cargo_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Pallets',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Packages',
                'created_at' => NULL,
                'updated_at' => NULL,
            )
        ));
    }
}
