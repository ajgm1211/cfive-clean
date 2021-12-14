<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowUserWhitelabelTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    /** @test */
    public function ShowUsersWhitelabel()
    {
        $this->withoutExceptionHandling();

        $user = factory('App\User')->create();

        $response = $this->get('/api/whitelabel/users/'.$user->id);

        $response->assertSee($user->email);
    }
}
