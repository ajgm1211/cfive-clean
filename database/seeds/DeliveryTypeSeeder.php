<?php

use Illuminate\Database\Seeder;

class DeliveryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('delivery_types')->insert([
            0 => [
                'id' => 1,
                'name' => 'Port to Port'
            ],
            1 => [
                'id' => 2,
                'name' => 'Port to Door'
            ],
            2 => [
                'id' => 3,
                'name' => 'Door to Port'
            ],
            3 => [
                'id' => 4,
                'name' => 'Door to Door'
            ]
            
        ]);
    }
}
