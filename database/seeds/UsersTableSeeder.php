<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date = Carbon\Carbon::now();

        \DB::table('users')->delete();

        \DB::table('users')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'Admin',
                'lastname' => 'Admin',
                'email' => 'admin@example.com',
                'password' =>  bcrypt('secret'),
                'type' => 'admin',
                'verified' => 1,
                'emailverified' => 1,
                'created_at' => $date,
                'updated_at' => $date,
            )
        ));
    }
}
