<?php

use Illuminate\Database\Seeder;

class CalculationtypeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('calculationtype')->delete();
        
        \DB::table('calculationtype')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Per 40 "',
                'code' => '40',
                'options' => '{"group": true, "isteu": false}',
                'gp_pcontainer' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Per 20 "',
                'code' => '20',
                'options' => '{"name": "N\\\\A", "group": true, "isteu": false}',
                'gp_pcontainer' => 1,
                'created_at' => NULL,
                'updated_at' => '2020-03-27 15:18:20',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Per 40 HC',
                'code' => '40HC',
                'options' => '{"group": true, "isteu": false}',
                'gp_pcontainer' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Per TEU Dry',
                'code' => 'TEU',
                'options' => '{"name": "PER_TEU", "group": true, "isteu": true}',
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => '2020-03-27 15:38:26',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Per Container DRY',
                'code' => 'CONT',
                'options' => '{"name": "PER_CONTAINER", "group": false, "isteu": false}',
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => '2020-03-27 15:57:10',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Per Shipment',
                'code' => 'SHIP',
                'options' => '{"name": "PER_SHIPMENT", "group": false, "isteu": false}',
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => '2020-03-27 15:39:43',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Per 40 NOR',
                'code' => '40NOR',
                'options' => '{"group": true, "isteu": false}',
                'gp_pcontainer' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Per 45',
                'code' => '45',
                'options' => '{"group": true, "isteu": false}',
                'gp_pcontainer' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Per BL',
                'code' => 'BL',
                'options' => '{"name": "PER_BL", "group": false, "isteu": false}',
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => '2020-03-27 15:40:41',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Per TON',
                'code' => 'TON',
                'options' => '{"name": "PER_TON", "group": false, "isteu": false}',
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => '2020-03-27 15:40:12',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Per Invoice',
                'code' => 'INV',
                'options' => '{"group": false, "isteu": false}',
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Per 20Refeer',
                'code' => '20R',
                'options' => '{"group": true, "isteu": false}',
                'gp_pcontainer' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Per 40Refeer',
                'code' => '40RF',
                'options' => '{"group": true, "isteu": false}',
                'gp_pcontainer' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'Per 40HCRef',
                'code' => '40HCRF',
                'options' => NULL,
                'gp_pcontainer' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'ModidicacionBL',
                'code' => 'MBL',
                'options' => NULL,
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'Per 20OT',
                'code' => '20OT',
                'options' => '{"group": true, "isteu": false}',
                'gp_pcontainer' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'Per Tracking',
                'code' => 'TRCK',
                'options' => '{"group": false, "isteu": false}',
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'Per 40OT',
                'code' => '40OT',
                'options' => '{"group": true, "isteu": false}',
                'gp_pcontainer' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'Per container Reefer',
                'code' => 'CONT RF',
                'options' => '{"name": "PER_CONTAINER", "group": false, "isteu": false}',
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => '2020-03-27 15:20:55',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'Per container OP',
                'code' => 'CONT OP',
                'options' => '{"name": "PER_CONTAINER", "group": false, "isteu": false}',
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => '2020-03-27 15:19:57',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'Per container FR',
                'code' => 'CONT FR',
                'options' => '{"name": "PER_CONTAINER", "group": false, "isteu": false}',
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => '2020-03-27 15:19:45',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'Per TEU Reefer',
                'code' => 'TEU RF',
                'options' => '{"name": "PER_TEU", "group": false, "isteu": false}',
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => '2020-03-27 15:38:59',
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'Per TEU OP',
                'code' => 'TEU OP',
                'options' => '{"name": "PER_TEU", "group": false, "isteu": false}',
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => '2020-03-27 15:38:50',
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'Per TEU FR',
                'code' => 'TEU FR',
                'options' => '{"name": "PER_TEU", "group": false, "isteu": false}',
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => '2020-03-27 15:38:37',
            ),
        ));
        
        
    }
}