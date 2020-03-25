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
                'options' => '{"group": true, "isteu": false}',
                'gp_pcontainer' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
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
                'name' => 'Per TEU',
                'code' => 'TEU',
                'options' => '{"group": true, "isteu": true}',
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Per Container',
                'code' => 'CONT',
                'options' => '{"group": false, "isteu": false}',
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Per Shipment',
                'code' => 'SHIP',
                'options' => '{"group": false, "isteu": false}',
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
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
                'options' => NULL,
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Per TON',
                'code' => 'TON',
                'options' => NULL,
                'gp_pcontainer' => 0,
                'created_at' => NULL,
                'updated_at' => NULL,
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
        ));
        
        
    }
}