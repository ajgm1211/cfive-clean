<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CompanyTest extends DuskTestCase
{

  public function testAddCompanies()
    {
        $this->browse(function (Browser $browser) {
   
            $browser->visit('/login')
                ->type('email', 'admin@example.com')
                ->type('password', 'secret')
                ->press('Login')
                ->visit("/companies/add")
                ->type('business_name', 'cargofive name')
                ->type('phone', '45123456789')
                ->type('email', 'mail@mail.com')
                ->type('address', 'company address')
                ->press('Save')
                ->assertPathIs('/companies');
        });
    }
  
  

      public function testEditCompany(){
        $this->browse(function (Browser $browser) {
          $this->assertTrue(true);
            $browser->visit("/companies/1/edit")
                ->type('business_name', 'Juan name')
                ->type('phone', '45123456789')
                ->type('email', 'mail@mail.com')
                ->type('address', 'company address')
                ->press('Update');
            
        });
    }
    
}
