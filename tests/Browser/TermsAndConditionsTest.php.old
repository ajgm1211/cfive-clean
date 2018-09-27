<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TermsAndConditionsTest extends DuskTestCase
{

    //use DatabaseMigrations;

    public function testAddTerms()
    {

        /**
     * @group terms
     */
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'admin@example.com')
                ->type('password', 'secret')
                ->press('Login')
                ->visit("/terms/add")
                ->type('name', 'Test 1')
                ->select('ports[]', 1);
            $browser->driver->executeScript('tinyMCE.get(\'import\').setContent(\'<h1>Test Import</h1>\')');
            $browser->driver->executeScript('tinyMCE.get(\'export\').setContent(\'<h1>Test Export</h1>\')');
            $browser->press('Save')->assertPathIs('/terms/list');
        });
    }

    /* public function testEditContact(){
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
    }*/
}