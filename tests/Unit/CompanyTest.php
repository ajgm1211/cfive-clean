<?php

// namespace Tests\Unit;

// use Tests\TestCase;
// use App\Company;
// use Illuminate\Foundation\Testing\WithFaker;
// use Illuminate\Foundation\Testing\RefreshDatabase;
// use Illuminate\Foundation\Testing\DatabaseTransactions;

// class CompanyTest extends TestCase
// {
//     use DatabaseTransactions;

//     /**
//      * A basic test example.
//      *
//      * @return void
//      */
//     public function test_if_client_company_is_stored()
//     {
//         $company = factory(Company::class)->create();

//         $this->seeInDatabase('companies', $company->toArray());
//     }

//     public function test_if_client_company_is_updated()
//     {

//         $model = factory(Company::class)->create();

//         $company = Company::find($model->id);

//         $company->business_name = 'tecnicarbonCA';

//         $company->update();

//         // $model

//         $this->seeInDatabase('companies', [
//             'business_name' => 'tecnicarbonCA'
//         ]);
//     }
// }
