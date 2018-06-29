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

        $this->visit('/users/add')
            ->post(route('users.store'), $user->toArray());

        $this->seeInDatabase('users', $user->toArray());
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
        $this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $this->delete(route('users.destroy', $user->id), $user->toArray());

        $this->dontSeeInDatabase('users' ,$user->toArray());
    }

    public function testUserCanLogin()
    {
        $user = factory(User::class)->create(['verified' => 1]);

        $data = [
            '_token' => $user->remember_token,
            'email' => $user->email,
            'password' => $user->password,
        ];

        $this->post(route('login'), $data);
        $this->assertResponseStatus(302);
    }

    public function testUserCantLogin()
    {
        $user = factory(User::class)->create();

        $this->visit('/')
            ->see('login')
            ->type($user->email, 'email')
            ->type($user->password, 'password')
            ->press('Login')
            ->see('These credentials do not match our records.');

    }

    public function testActivateUser()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $this->put(route('users.activate', $user->id), $user->toArray());
        $this->notSeeInDatabase('users', $user->toArray());
    }
}
