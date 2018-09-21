<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ClientContactTest extends DuskTestCase
{

    public function testAddContacts()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@example.com')
                ->type('password', 'secret')
                ->press('Login')
                ->visit("/contacts/add")
                ->type('first_name', 'Julio')
                ->type('last_name', 'Avila')
                ->type('email', 'javila3090@gmail.com')
                ->type('phone', '972374655')                
                ->select('company_id', 1)
                ->press('Save')
                ->assertPathIs('/contacts/add');
        });
    }

    public function testEditContact(){
        $this->browse(function (Browser $browser) {
            $this->assertTrue(true);
            $browser->visit("/contacts/1/edit")
                ->type('first_name', 'Cesar')
                ->type('last_name', 'Loreto')
                ->type('email', 'kaiser3090@gmail.com')
                ->type('phone', '972374607')                
                ->select('company_id', 1)
                ->press('Update');

        });
    }
}
