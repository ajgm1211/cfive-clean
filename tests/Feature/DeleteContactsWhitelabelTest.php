<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteContactsWhitelabelTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /** @test */
    public function DeleteContactWhitelabel()
    {
        $this->withoutExceptionHandling();
        $contact = factory('App\Contact')->create();
        $this->delete('/api/whitelabel/contacts/'.$contact->id);
        $this->assertDatabaseMissing('contacts',['id'=> $contact->id]);
    }
}
