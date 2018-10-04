<?php

use Illuminate\Database\Seeder;

class CompanyUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $date = Carbon\Carbon::now();
        
        \DB::table('company_users')->delete();

        \DB::table('company_users')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => 'Cargofive',
                'address' => 'Lisbon, Portugal',
                'phone' => '+56972374655',
                'hash' => \Hash::make('Cargofive'),
                'currency_id' =>  149,
                'pdf_language' => 1,
                'type_pdf' => 2,
                'created_at' => $date,
                'updated_at' => $date,
            )
        ));
    }
}
