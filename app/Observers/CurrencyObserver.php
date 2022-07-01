<?php

namespace App\Observers;

use App\Currency;

class CurrencyObserver
{
    /**
     * Handle the currency "created" event.
     *
     * @param  \App\Currency  $currency
     * @return void
     */
    public function created(Currency $currency)
    {
        cache()->forget('data_currency');
    }

    /**
     * Handle the currency "updated" event.
     *
     * @param  \App\Currency  $currency
     * @return void
     */
    public function updated(Currency $currency)
    {
        cache()->forget('data_currency');
    }

    /**
     * Handle the currency "deleted" event.
     *
     * @param  \App\Currency  $currency
     * @return void
     */
    public function deleted(Currency $currency)
    {
        cache()->forget('data_currency');
    }

    /**
     * Handle the currency "restored" event.
     *
     * @param  \App\Currency  $currency
     * @return void
     */
    public function restored(Currency $currency)
    {
        //
    }

    /**
     * Handle the currency "force deleted" event.
     *
     * @param  \App\Currency  $currency
     * @return void
     */
    public function forceDeleted(Currency $currency)
    {
        //
    }
}
