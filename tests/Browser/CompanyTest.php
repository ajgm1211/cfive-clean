<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CompanyTest extends DuskTestCase
{
  /**
     * A Dusk test example.
     *
     * @return void
     */
  public function testExample()
  {

    $this->browse(function ($browser) {
      $this->assertTrue(true);
      $browser->visit('/companies') //Go to the homepage
        ->click('addCompany') //Click the Register link

        ->type('address', 'valparaiso chile')
        ->value('business_name','test')
        ->value('phone', '555-55-55')
        ->value('email', 'secret@example.com')
        ->click('button[type="submit"]')
        ->assertPathIs('/companies');
    });
  }
}
