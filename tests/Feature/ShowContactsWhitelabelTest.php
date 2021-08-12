<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowContactsWhitelabelTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
    public function ShowContactsWhitelabel()
    {
        
    $this->withoutExceptionHandling();
        
    $contact = factory('App\Contact')->create();
    
    $response = $this->get('/api/whitelabel/contacts/'.$contact->id);
    
    $response->assertSee($contact->email);

    }
}
