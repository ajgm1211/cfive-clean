<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteUserWhitelabelTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    /** @test */
    public function DeleteUserWhitelabel()
    {

        $this->withoutExceptionHandling();

        $user = factory('App\User')->create();
        $this->delete('/api/whitelabel/users/'.$user->id);
        $this->assertDatabaseMissing('users',['id'=> $user->id]);
    }
}
