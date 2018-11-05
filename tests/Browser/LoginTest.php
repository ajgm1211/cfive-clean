<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
class LoginTest extends DuskTestCase
{


  /**
     * A Dusk test example.
     *
     * @return void
     */
  public function testLogin()
  {
    $this->browse(function ($first, $second) {
      $this->assertTrue(true);
      $first->loginAs(User::find(1))
        ->visit('/home');
    });
  }
}
