<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PriceLevelTest extends DuskTestCase
{

    public function testAddPriceLevel()
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('/login')
                ->type('email', 'admin@example.com')
                ->type('password', 'secret')
                ->press('Login')
                ->visit("/prices/add")
                ->type('name', 'Price Level 1')
                ->type('description', 'Testing')
                ->type('freight_percent_markup[]', '10')
                ->type('local_percent_markup_import[]', '10')
                ->type('local_percent_markup_export[]', '10')
                ->type('inland_percent_markup_import[]', '10')
                ->type('inland_percent_markup_export[]', '10')
                ->press('Submit')
                ->assertPathIs("/prices");
        });
    }

}
