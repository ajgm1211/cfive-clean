<?php

namespace App\Providers;

use App\ApiIntegrationSetting;
use App\Contract;
use App\Observers\ContractObserver;
use App\Observers\QuoteObserver;
use App\Observers\CompanyObserver;
use App\Observers\CarrierObserver;
use App\Observers\ContactObserver;
use App\Company;
use App\Quote;
use App\Carrier;
use App\Contact;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        Company::observe(CompanyObserver::class);
        Carrier::observe(CarrierObserver::class);
        Contact::observe(ContactObserver::class);

        /*if (env('APP_ENV') === 'prod' || env('APP_ENV') === 'production') {
            $url->forceScheme('https');
        }*/
    }

    /**
     * register.
     *
     * @return void
     */
    public function register()
    {
        // Dusk, if env is appropiate
        /*if ($this->app->environment('local', 'testing')) {
            $this->app->register(\Laravel\Dusk\DuskServiceProvider::class);
        }*/

        $this->app->bind(
            'App\Repositories\CompanyRepositoryInterface',
            'App\Repositories\CompanyRepository'
        );
    }
}
