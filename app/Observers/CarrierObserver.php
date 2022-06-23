<?php

namespace App\Observers;

use App\Carrier;

class CarrierObserver
{
    /**
     * Handle the carrier "created" event.
     *
     * @param  \App\Carrier  $carrier
     * @return void
     */
    public function created(Carrier $carrier)
    {
        cache()->forget('quotations_form_required_data');
    }

    /**
     * Handle the carrier "updated" event.
     *
     * @param  \App\Carrier  $carrier
     * @return void
     */
    public function updated(Carrier $carrier)
    {
        cache()->forget('quotations_form_required_data');
    }

    /**
     * Handle the carrier "deleted" event.
     *
     * @param  \App\Carrier  $carrier
     * @return void
     */
    public function deleted(Carrier $carrier)
    {
        cache()->forget('quotations_form_required_data');
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
