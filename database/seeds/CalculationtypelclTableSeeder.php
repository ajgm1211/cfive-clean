<?php

use Illuminate\Database\Seeder;

class CalculationtypelclTableSeeder extends Seeder
{
  /**
     * Run the database seeds.
     *
     * @return void
     */
  public function run()
  {
    \DB::table('calculationtypelcl')->delete();

    \DB::table('calculationtypelcl')->insert(array (
      0 => 
      array (
        'id' => 1,
        'name' => 'W/M',
        'code' => 'W/M',
        'created_at' => NULL,
        'updated_at' => NULL,
      ),
      1 => 
      array (
        'id' => 2,
        'name' => 'Per Shipment',
        'code' => 'SHIP',
        'created_at' => NULL,
        'updated_at' => NULL,
      ),
      2 => 
      array (
        'id' => 3,
        'name' => 'Per BL',
        'code' => 'BL',
        'created_at' => NULL,
        'updated_at' => NULL,
      ),
    ));
  }
}