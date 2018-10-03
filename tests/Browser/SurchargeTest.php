<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SurchargeTest extends DuskTestCase
{
  /**
     * A Dusk test example.
     *
     * @return void
     */
  public function testAddSurcharges()
  {
    $this->browse(function (Browser $browser) {

      $browser->visit('/login')
        ->type('email', 'admin@example.com')
        ->type('password', 'secret')
        ->press('Login')
        ->visit("/surcharges/add")

        ->type('name', 'OSS')
        ->type('description', 'OSS al consumidor')
        ->press('Save')
        ->assertPathIs('/surcharges');
    });
  }



  public function testEditSurcharge(){
    $this->browse(function (Browser $browser) {
      $this->assertTrue(true);
      $browser->visit("/surcharges/1/edit")
        ->type('name', 'LUIS')
        ->type('description', 'IvA al consumidor y vendedor')
        ->press('Update');

    });
  }
  
}
