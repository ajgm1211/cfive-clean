<?php

use Illuminate\Database\Seeder;

class ContractApisTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('contractApis')->delete();

        \DB::table('contractApis')->insert([
            0 => [
                'id' => 1,
                'name' => 'Prueba Api',
                'number' => '12',
                'validity' => '2019-09-01',
                'expire' => '2019-09-30',
                'status' => 'publish',
                'remarks' => null,
                'company_user_id' => 1,
                'account_id' => null,
                'direction_id' => null,
                'created_at' => null,
                'updated_at' => null,
            ],
        ]);
    }
}
