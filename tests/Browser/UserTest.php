<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends DuskTestCase
{
  /**
     * A Dusk test example.
     *
     * @return void
     */

  public function testUserCompanies()
  {
    $this->browse(function (Browser $browser) {
      $this->assertTrue(true);
      $browser->visit('/login')
        ->type('email', 'admin@example.com')
        ->type('password', 'secret')
        ->press('Login')
        ->visit("users/add")
        ->type('name', 'Nombre comp')
        ->type('lastname', '45123456789')
        ->type('email', 'mail@mail.com')
        ->type('password', '1234')
        ->select('type', 'company')
        ->press('Save');
    });
  }


}
