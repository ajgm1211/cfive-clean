<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class UserTest extends TestCase
{

    use RefreshDataBase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    public function testAddUser()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $userToAdd = factory(User::class)->create();

        $this->visit('/users/add')
            ->post(route('users.store'), $userToAdd->toArray());

        $this->seeInDatabase('users', $userToAdd->toArray());
    }

    public function testEditUser()
    {
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $this->put(route('users.update', $user), $user->toArray());

        $this->seeInDatabase('users', $user->toArray());
    }

    public function testDeleteUser()
    {

    }
}
