<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('roles')->delete();

        \DB::table('roles')->insert([
            0 => [
                'id' => 1,
                'name' => 'administrator',
                'guard_name' => 'web',
                'created_at' => null,
                'updated_at' => null,
            ],
            1 => [
                'id' => 2,
                'name' => 'subuser',
                'guard_name' => 'web',
                'created_at' => null,
                'updated_at' => null,
            ],
            2 => [
                'id' => 3,
                'name' => 'company',
                'guard_name' => 'web',
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
