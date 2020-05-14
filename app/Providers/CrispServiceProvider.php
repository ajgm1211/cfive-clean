<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CrispServiceProvider extends ServiceProvider
{
  /**
     * Bootstrap services.
     *
     * @return void
     */
  public function boot()
  {
    //
  }

  /**
     * Register services.
     *
     * @return void
     */
  public function register()
  {
    require_once app_path() . '/Helpers/HelperCrisp.php';
  }
}
