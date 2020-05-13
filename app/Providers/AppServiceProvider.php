<?php

namespace App\Providers;

use App\ApiIntegrationSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Routing\UrlGenerator;
use App\Observers\ContractObserver;
use App\Contract;
use App\Observers\QuoteObserver;
use App\Quote;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    public function boot(UrlGenerator $url)
    {
        Schema::defaultStringLength(191);
        Contract::observe(ContractObserver::class);
        Quote::observe(QuoteObserver::class);
       // $url->forceScheme('https');
    }

    public function register()
    {
        // Dusk, if env is appropiate
        /*if ($this->app->environment('local', 'testing')) {
        $this->app->register(DuskServiceProvider::class);
      }*/
    }
}
