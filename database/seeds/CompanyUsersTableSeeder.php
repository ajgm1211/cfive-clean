<?php

use Illuminate\Database\Seeder;

class CompanyUsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('company_users')->delete();
        
        \DB::table('company_users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Cargofive',
                'address' => 'Portugal',
                'phone' => '36586289542',
                'logo' => NULL,
                'hash' => '$2y$10$3.L5S4V1kCVJzukHKZ2DEOAJMl8MNTFd9vLFkj0YIKKd6i1J5LLSe',
                'currency_id' => 149,
                'pdf_language' => 2,
                'type_pdf' => 2,
                'pdf_ammounts' => 2,
                'created_at' => '2019-05-08 18:42:44',
                'updated_at' => '2019-05-08 18:42:44',
            ),
        ));
        
        
    }
}