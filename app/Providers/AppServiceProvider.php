<?php

namespace App\Providers;

use App\ApiIntegrationSetting;
use App\Contract;
use App\Company;
use App\Quote;
use App\Carrier;
use App\Contact;
use App\QuoteV2;
use App\Incoterm;
use App\Harbor;
use App\PaymentCondition;
use App\TermAndConditionV2;
use App\DeliveryType;
use App\StatusQuote;
use App\CargoKind;
use App\Language;
use App\Currency;
use App\Container;
use App\CalculationType;
use App\ScheduleType;
use App\Country;
use App\CargoType;
use App\CalculationTypeLcl;
use App\DestinationType;
use App\Observers\ContractObserver;
use App\Observers\CompanyObserver;
use App\Observers\QuoteObserver;
use App\Observers\CarrierObserver;
use App\Observers\ContactObserver;
use App\Observers\QuoteV2Observer;
use App\Observers\IncotermObserver;
use App\Observers\HarborObserver;
use App\Observers\PaymentConditionObserver;
use App\Observers\TermAndConditionV2Observer;
use App\Observers\DeliveryTypeObserver;
use App\Observers\StatusQuoteObserver;
use App\Observers\CargoKindObserver;
use App\Observers\LanguageObserver;
use App\Observers\CurrencyObserver;
use App\Observers\ContainerObserver;
use App\Observers\CalculationTypeObserver;
use App\Observers\ScheduleTypeObserver;
use App\Observers\CountryObserver;
use App\Observers\CargoTypeObserver;
use App\Observers\CalculationTypeLclObserver;
use App\Observers\DestinationTypeObserver;
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
        Company::observe(CompanyObserver::class);
        Quote::observe(QuoteObserver::class);
        Carrier::observe(CarrierObserver::class);
        Contact::observe(ContactObserver::class);
        QuoteV2::observe(QuoteV2Observer::class);
        Incoterm::observe(IncotermObserver::class);
        Harbor::observe(HarborObserver::class);
        PaymentCondition::observe(PaymentConditionObserver::class);
        TermAndConditionV2::observe(TermAndConditionV2Observer::class);
        DeliveryType::observe(DeliveryTypeObserver::class);
        StatusQuote::observe(StatusQuoteObserver::class);
        CargoKind::observe(CargoKindObserver::class);
        Language::observe(LanguageObserver::class);
        Currency::observe(CurrencyObserver::class);
        Container::observe(ContainerObserver::class);
        CalculationType::observe(CalculationTypeObserver::class);
        ScheduleType::observe(ScheduleTypeObserver::class);
        Country::observe(CountryObserver::class);
        CargoType::observe(CargoTypeObserver::class);
        CalculationTypeLcl::observe(CalculationTypeLclObserver::class);
        DestinationType::observe(DestinationTypeObserver::class);

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
