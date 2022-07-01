<?php

namespace App\Observers;

use App\CalculationType;

class CalculationTypeObserver
{
    /**
     * Handle the calculation type "created" event.
     *
     * @param  \App\CalculationType  $calculationType
     * @return void
     */
    public function created(CalculationType $calculationType)
    {
        cache()->forget('data_calculationtypes');
    }

    /**
     * Handle the calculation type "updated" event.
     *
     * @param  \App\CalculationType  $calculationType
     * @return void
     */
    public function updated(CalculationType $calculationType)
    {
        cache()->forget('data_calculationtypes');
    }

    /**
     * Handle the calculation type "deleted" event.
     *
     * @param  \App\CalculationType  $calculationType
     * @return void
     */
    public function deleted(CalculationType $calculationType)
    {
        cache()->forget('data_calculationtypes');
    }

    /**
     * Handle the calculation type "restored" event.
     *
     * @param  \App\CalculationType  $calculationType
     * @return void
     */
    public function restored(CalculationType $calculationType)
    {
        //
    }

    /**
     * Handle the calculation type "force deleted" event.
     *
     * @param  \App\CalculationType  $calculationType
     * @return void
     */
    public function forceDeleted(CalculationType $calculationType)
    {
        //
    }
}
