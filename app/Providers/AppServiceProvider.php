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

  public function boot(UrlGenerator $url){
    Schema::defaultStringLength(191);
    Contract::observe(ContractObserver::class);
    Quote::observe(QuoteObserver::class);

    Queue::after(function (JobProcessed $event) {
        switch($event->job->resolveName()){
            case "App\Jobs\SyncCompaniesJob":
                $userLogin  = auth()->user();
                $setting = ApiIntegrationSetting::where('company_user_id', $userLogin->company_user_id)->first();
                $setting->status=0;
                $setting->save();
            break;
        }
    });

    //$url->forceScheme('https');

  }

  public function register(){
    // Dusk, if env is appropiate
    /*if ($this->app->environment('local', 'testing')) {
        $this->app->register(DuskServiceProvider::class);
      }*/
  }
}
