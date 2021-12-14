<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListContactsWhitelabelTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

         /** @test */
    public function ListContactsWhitelabel()
    {

        $contact = factory('App\Contact')->create();


        $response = $this->get('/api/whitelabel/contacts');


        $response->assertSee($contact->email);

    }

}
