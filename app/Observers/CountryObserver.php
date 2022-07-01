<?php

namespace App\Observers;

use App\Country;

class CountryObserver
{
    /**
     * Handle the country "created" event.
     *
     * @param  \App\Country  $country
     * @return void
     */
    public function created(Country $country)
    {
        cache()->forget('data_countries');
    }

    /**
     * Handle the country "updated" event.
     *
     * @param  \App\Country  $country
     * @return void
     */
    public function updated(Country $country)
    {
        cache()->forget('data_countries');
    }

    /**
     * Handle the country "deleted" event.
     *
     * @param  \App\Country  $country
     * @return void
     */
    public function deleted(Country $country)
    {
        cache()->forget('data_countries');
    }

    /**
     * Handle the country "restored" event.
     *
     * @param  \App\Country  $country
     * @return void
     */
    public function restored(Country $country)
    {
        //
    }

    /**
     * Handle the country "force deleted" event.
     *
     * @param  \App\Country  $country
     * @return void
     */
    public function forceDeleted(Country $country)
    {
        //
    }
}
