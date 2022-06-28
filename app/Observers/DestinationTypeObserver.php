<?php

namespace App\Observers;

use App\DestinationType;

class DestinationTypeObserver
{
    /**
     * Handle the destination type "created" event.
     *
     * @param  \App\DestinationType  $destinationType
     * @return void
     */
    public function created(DestinationType $destinationType)
    {
        cache()->forget('data_destination_types');
    }

    /**
     * Handle the destination type "updated" event.
     *
     * @param  \App\DestinationType  $destinationType
     * @return void
     */
    public function updated(DestinationType $destinationType)
    {
        cache()->forget('data_destination_types');
    }

    /**
     * Handle the destination type "deleted" event.
     *
     * @param  \App\DestinationType  $destinationType
     * @return void
     */
    public function deleted(DestinationType $destinationType)
    {
        cache()->forget('data_destination_types');
    }

    /**
     * Handle the destination type "restored" event.
     *
     * @param  \App\DestinationType  $destinationType
     * @return void
     */
    public function restored(DestinationType $destinationType)
    {
        //
    }

    /**
     * Handle the destination type "force deleted" event.
     *
     * @param  \App\DestinationType  $destinationType
     * @return void
     */
    public function forceDeleted(DestinationType $destinationType)
    {
        //
    }
}
