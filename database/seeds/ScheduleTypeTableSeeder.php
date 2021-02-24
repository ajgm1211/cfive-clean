<?php

use Illuminate\Database\Seeder;

class ScheduleTypeTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('schedule_type')->delete();

        \DB::table('schedule_type')->insert([
            0 => [
                'id' => 1,
                'name' => 'Direct',
                'created_at' => null,
                'updated_at' => null,
            ],
            1 => [
                'id' => 2,
                'name' => 'Transfer',
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
