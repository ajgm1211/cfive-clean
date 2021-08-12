<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateUsersWhitelabelTest extends TestCase
{
    /** @test */
    public function CreateUsersWhitelabelTest()
    {

        $this->withoutExceptionHandling();

        $response = $this->post('/api/whitelabel/users', [
            'name' => 'Jose',
            'lastname' => 'Suarez',
            'password' => 'prueba',
            'email' => 'josesuarez17@mail.com',
            'phone' => '+584142610454',
            'type' => 'company',
            'company_user_id' => 1,
            'position' => 'developer',
            'whitelabel' => true
        ]);

        $response->assertOk();

        // $this->assertCount(1421, User::all());

        // $this->assertEquals($response->email, 'josesuarez13@mail.com') ;

    }


}
