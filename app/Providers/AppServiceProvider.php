<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
    public function boot()
    {
        Schema::defaultStringLength(191);
        Contract::observe(ContractObserver::class);
        Quote::observe(QuoteObserver::class);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
     
    }
}
