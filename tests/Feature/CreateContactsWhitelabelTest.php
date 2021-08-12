<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateContactsWhitelabelTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
    public function CreateContactsWhitelabel()
    {
        $contact = factory('App\Contact')->create();
        $contact->email = "joselito@mail.com";
        $this->post('/api/whitelabel/contacts/',$contact->toArray());
        $this->assertDatabaseHas('contacts',['email' => 'joselito@mail.com']);

    }
}
