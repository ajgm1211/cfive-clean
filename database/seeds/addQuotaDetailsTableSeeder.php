<?php

use App\CompanyUser;
use App\QuotaRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class addQuotaDetailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = CompanyUser::select('id')->get();

        foreach ($clients as $client) {
            QuotaRequest::create([
                'company_user_id' => $client->id,
                'quota' => 0,
                'status' => 1,
                'type' => 'unlimited',
                'issued_date' => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
