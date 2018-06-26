<?php

namespace Tests\Feature;

use App\User;
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

        //$this->withoutExceptionHandling();

        //$user = User::find(1);
        //$this->actingAs($user);

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $this->visit('/users/add')
            ->post(route('users.store'), [
                'name' => 'francisco',
                'lastname' => 'vargas',
                'email' => 'fv@mail.com',
                'pass' => '123456'
            ]);

        $this->seeInDatabase('users', [
                'name' => 'francisco',
                'lastname' => 'vargas',
                'email' => 'fv@mail.com'
            ]);
    }
}
