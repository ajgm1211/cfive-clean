<?php

use Illuminate\Database\Seeder;

class ContainersTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('containers')->delete();

        \DB::table('containers')->insert([
            0 => [
                'id' => 1,
                'name' => '20 DV',
                'code' => '20DV',
                'gp_container_id' => 1,
                'options' => '{"column": true, "optional": false, "field_rate": "twuenty", "column_name": "twuenty", "field_inland": "km_20"}',
                'created_at' => null,
                'updated_at' => null,
            ],
            1 => [
                'id' => 2,
                'name' => '40 DV',
                'code' => '40DV',
                'gp_container_id' => 1,
                'options' => '{"column": true, "optional": false, "field_rate": "forty", "column_name": "forty", "field_inland": "km_40"}',
                'created_at' => null,
                'updated_at' => null,
            ],
            2 => [
                'id' => 3,
                'name' => '40 HC',
                'code' => '40HC',
                'gp_container_id' => 1,
                'options' => '{"column": true, "optional": false, "field_rate": "fortyhc", "column_name": "fortyhc", "field_inland": "km_40hc"}',
                'created_at' => null,
                'updated_at' => null,
            ],
            3 => [
                'id' => 4,
                'name' => '45 HC',
                'code' => '45HC',
                'gp_container_id' => 1,
                'options' => '{"column": true, "optional": true, "field_rate": "fortyfive", "column_name": "fortyfive"}',
                'created_at' => null,
                'updated_at' => null,
            ],
            4 => [
                'id' => 5,
                'name' => '40 NOR',
                'code' => '40NOR',
                'gp_container_id' => 1,
                'options' => '{"column": true, "optional": true, "field_rate": "fortynor", "column_name": "fortynor"}',
                'created_at' => null,
                'updated_at' => null,
            ],
            5 => [
                'id' => 6,
                'name' => '20 RF',
                'code' => '20RF',
                'gp_container_id' => 2,
                'options' => '{"column": false, "optional": false, "field_rate": "containers"}',
                'created_at' => null,
                'updated_at' => null,
            ],
            6 => [
                'id' => 7,
                'name' => '40 RF',
                'code' => '40RF',
                'gp_container_id' => 2,
                'options' => '{"column": false, "optional": false, "field_rate": "containers"}',
                'created_at' => null,
                'updated_at' => null,
            ],
            7 => [
                'id' => 8,
                'name' => '40 HCRF',
                'code' => '40HCRF',
                'gp_container_id' => 2,
                'options' => '{"column": false, "optional": false, "field_rate": "containers"}',
                'created_at' => null,
                'updated_at' => null,
            ],
            8 => [
                'id' => 9,
                'name' => '20 OT',
                'code' => '20OT',
                'gp_container_id' => 3,
                'options' => '{"column": false, "optional": false, "field_rate": "containers"}',
                'created_at' => null,
                'updated_at' => null,
            ],
            9 => [
                'id' => 10,
                'name' => '40 OT',
                'code' => '40OT',
                'gp_container_id' => 3,
                'options' => '{"column": false, "optional": false, "field_rate": "containers"}',
                'created_at' => null,
                'updated_at' => null,
            ],
            10 => [
                'id' => 11,
                'name' => '20 FR',
                'code' => '20FR',
                'gp_container_id' => 4,
                'options' => '{"column": false, "optional": false, "field_rate": "containers"}',
                'created_at' => null,
                'updated_at' => null,
            ],
            11 => [
                'id' => 12,
                'name' => '40 FR',
                'code' => '40FR',
                'gp_container_id' => 4,
                'options' => '{"column": false, "optional": false, "field_rate": "containers"}',
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
