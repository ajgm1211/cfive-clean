<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class QuoteAutomaticTest extends DuskTestCase
{
  /**
     * A Dusk test example.
     *
     * @return void
     */
  public function testQuoteAutomatic()
  {
    $this->browse(function (Browser $browser) {
      $this->assertTrue(true);
      $browser->visit('/login')
        ->type('email', 'admin@example.com')
        ->type('password', 'secret')
        ->press('Login')
        ->visit("quotes/automatic")
        ->type('twuenty', '1')
        ->type('forty', '2')
        ->type('fortyhc', '3')
        ->type('date', '2018-9-20')
        ->select('originport[]', 'Abu Dhabi, AEAUH')
        ->select('destinyport[]', 'Ajman, AEAJM')
        ->select('company_id_quote', 'Pepsi')
        ->select('contact_id', 'Pedro')
        ->press('Create Quote');

    });
  }
}
