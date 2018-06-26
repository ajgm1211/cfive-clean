<?php

use Illuminate\Database\Seeder;

class StatusQuotesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = Carbon\Carbon::now();

        \DB::table('status_quotes')->delete();

        \DB::table('status_quotes')->insert(array (
            0 =>
                array (
                    'id' => 1,
                    'name' => 'Draft',
                    'created_at' => $date,
                    'updated_at' => $date,
                ),
            1 =>
                array (
                    'id' => 2,
                    'name' => 'Sent',
                    'created_at' => $date,
                    'updated_at' => $date,
                ),
            2 =>
                array (
                    'id' => 3,
                    'name' => 'Negotiated',
                    'created_at' => $date,
                    'updated_at' => $date,
                ),
            3 =>
                array (
                    'id' => 4,
                    'name' => 'Lost',
                    'created_at' => $date,
                    'updated_at' => $date,
                ),
            4 =>
                array (
                    'id' => 5,
                    'name' => 'Win',
                    'created_at' => $date,
                    'updated_at' => $date,
                ),
        ));
    }
}