<?php

use Illuminate\Database\Seeder;

class GroupContainersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('group_containers')->delete();

        \DB::table('group_containers')->insert([
            0 => [
                'id' => 1,
                'name' => 'DRY',
                'data' => '{"color": "#012586"}',
                'created_at' => null,
                'updated_at' => null,
                'code' => 'dry',
            ],
            1 => [
                'id' => 2,
                'name' => 'REEFER',
                'data' => '{"color": "#ad43ba"}',
                'created_at' => null,
                'updated_at' => null,
                'code' => 'reefer',
            ],
            2 => [
                'id' => 3,
                'name' => 'OPEN TOP',
                'data' => '{"color": "#9f9b45"}',
                'created_at' => null,
                'updated_at' => null,
                'code' => 'opentop',
            ],
            3 => [
                'id' => 4,
                'name' => 'FLAT RACK',
                'data' => '{"color": "#058b0a"}',
                'created_at' => null,
                'updated_at' => null,
                'code' => 'flatrack',
            ],
        ]);
    }
}
