<?php

namespace App\Observers;

use App\Incoterm;

class IncotermObserver
{
    /**
     * Handle the incoterm "created" event.
     *
     * @param  \App\Incoterm  $incoterm
     * @return void
     */
    public function created(Incoterm $incoterm)
    {
        cache()->forget('data_incoterms');
    }

    /**
     * Handle the incoterm "updated" event.
     *
     * @param  \App\Incoterm  $incoterm
     * @return void
     */
    public function updated(Incoterm $incoterm)
    {
        cache()->forget('data_incoterms');
    }

    /**
     * Handle the incoterm "deleted" event.
     *
     * @param  \App\Incoterm  $incoterm
     * @return void
     */
    public function deleted(Incoterm $incoterm)
    {
        cache()->forget('data_incoterms');
    }

    /**
     * Handle the incoterm "restored" event.
     *
     * @param  \App\Incoterm  $incoterm
     * @return void
     */
    public function restored(Incoterm $incoterm)
    {
        //
    }

    /**
     * Handle the incoterm "force deleted" event.
     *
     * @param  \App\Incoterm  $incoterm
     * @return void
     */
    public function forceDeleted(Incoterm $incoterm)
    {
        //
    }
}
