<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Company;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClientCompanyTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_if_client_company_is_stored()
    {

        Company::create([
            'business_name' =>'Company 1',
            'phone' => '972374655',
            'address' => 'Santiago de Chile',
            'email' => 'company1@example.com',
            'associated_quotes' => 'NULL',
            'company_user_id' => 1,
            'owner' => 1
        ]);

        $this->assertDatabaseHas('companies', [
            'business_name' =>'Company 1',
            'phone' => '972374655',
            'address' => 'Santiago de Chile',
            'email' => 'company1@example.com',
            'associated_quotes' => 'NULL',
            'company_user_id' => 1,
            'owner' => 1
        ]);
    }
}
