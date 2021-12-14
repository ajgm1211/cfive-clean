<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateUserWhitelabelTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    /** @test */
    public function UpdateUsersWhitelabel()
    {
        $user = factory('App\User')->create();
        $user->email = "chirix@mail.com";
        $this->put('/api/whitelabel/users/'.$user->id, $user->toArray());
        $this->assertDatabaseHas('users',['id'=> $user->id , 'email' => 'chirix@mail.com']);

    }
}
