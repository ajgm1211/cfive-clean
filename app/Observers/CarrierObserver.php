<?php

namespace App\Observers;

use App\Carrier;

class CarrierObserver
{

    public function created(Carrier $carrier)
    {
        cache()->forget('data_carriers');
    }

    /**
     * Handle the carrier "updated" event.
     *
     * @param  \App\Carrier  $carrier
     * @return void
     */
    public function updated(Carrier $carrier)
    {
        cache()->forget('data_carriers');
    }

    /**
     * Handle the carrier "deleted" event.
     *
     * @param  \App\Carrier  $carrier
     * @return void
     */
    public function deleted(Carrier $carrier)
    {
        cache()->forget('data_carriers');
    }

    /**
     * Handle the carrier "restored" event.
     *
     * @param  \App\Carrier  $carrier
     * @return void
     */
    public function restored(Carrier $carrier)
    {
        //
    }

    /**
     * Handle the carrier "force deleted" event.
     *
     * @param  \App\Carrier  $carrier
     * @return void
     */
    public function forceDeleted(Carrier $carrier)
    {
        //
    }
}
