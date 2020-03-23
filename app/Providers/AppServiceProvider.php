<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Routing\UrlGenerator;
use App\Observers\ContractObserver;
use App\Contract;
use App\Observers\QuoteObserver;
use App\Quote;

class AppServiceProvider extends ServiceProvider
{
  /**
     * Bootstrap any application services.
     *
     * @return void
     */

  public function boot(UrlGenerator $url){
    Schema::defaultStringLength(191);
    Contract::observe(ContractObserver::class);
    Quote::observe(QuoteObserver::class);

    /*if(env('APP_ENV') !== 'local') {
      $url->forceScheme('https');
    }*/
  }

  public function register(){
    // Dusk, if env is appropiate
    /*if ($this->app->environment('local', 'testing')) {
        $this->app->register(DuskServiceProvider::class);
      }*/
  }
}
