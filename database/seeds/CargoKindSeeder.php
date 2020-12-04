<?php

use Illuminate\Database\Seeder;

class CargoKindSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cargo_kinds')->insert([
            0 => [
                'id' => 1,
                'name' => 'General'
            ],
            1 => [
                'id' => 2,
                'name' => 'Perishable'
            ],
            2 => [
                'id' => 3,
                'name' => 'Dangerous'
            ],
            3 => [
                'id' => 4,
                'name' => 'Valuable Cargo'
            ],
            4 => [
                'id' => 5,
                'name' => 'All Live Animals'
            ],
            5 => [
                'id' => 6,
                'name' => 'Human Remains'
            ],
            6 => [
                'id' => 7,
                'name' => 'Pharma'
            ]
            
        ]);
    }
}
