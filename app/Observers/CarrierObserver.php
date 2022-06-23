<?php

namespace App\Observers;

use App\Http\Traits\QuoteV2Trait;
use App\Carrier;

class CarrierObserver
{
    use QuoteV2Trait;

    public function created(Carrier $carrier)
    {
        $this->forgetKeyCacheQuotationsFormRequiredDataOnAllUsers();
    }

    /**
     * Handle the carrier "updated" event.
     *
     * @param  \App\Carrier  $carrier
     * @return void
     */
    public function updated(Carrier $carrier)
    {
        $this->forgetKeyCacheQuotationsFormRequiredDataOnAllUsers();
    }

    /**
     * Handle the carrier "deleted" event.
     *
     * @param  \App\Carrier  $carrier
     * @return void
     */
    public function deleted(Carrier $carrier)
    {
        $this->forgetKeyCacheQuotationsFormRequiredDataOnAllUsers();
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
