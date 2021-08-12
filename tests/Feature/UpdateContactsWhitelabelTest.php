<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateContactsWhitelabelTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
        /** @test */
    public function UpdateContactsWhitelabel()
    {
        $contact = factory('App\Contact')->create();
        $contact->email = "chirino@mail.com";
        $this->put('/api/whitelabel/contacts/'.$contact->id, $contact->toArray());
        $this->assertDatabaseHas('contacts',['id'=> $contact->id , 'email' => 'chirino@mail.com']);
    }
}
