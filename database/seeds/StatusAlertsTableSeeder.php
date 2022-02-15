<?php

use Illuminate\Database\Seeder;

class StatusAlertsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('status_alerts')->delete();

        \DB::table('status_alerts')->insert([
            0 => [
                'id' => 1,
                'name' => 'pending',
                'created_at' => null,
                'updated_at' => null,
            ],
            1 => [
                'id' => 2,
                'name' => 'false',
                'created_at' => null,
                'updated_at' => null,
            ],
            2 => [
                'id' => 3,
                'name' => 'solved',
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
