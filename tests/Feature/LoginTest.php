<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{   
    /**
     * test_if_user_can_login
     *
     * @return void
     */
    public function test_if_user_can_login()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/home');
    }
}
