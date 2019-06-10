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
                'phone' => '+56972374655',
                'password' =>  bcrypt('secret'),
                'type' => 'admin',
                'company_user_id' => 1,
                'verified' => 1,
                'state' => 1,
                'created_at' => $date,
                'updated_at' => $date,
            )
        ));
    }
}
