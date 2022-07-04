<?php

namespace App\Observers;

use App\StatusQuote;

class StatusQuoteObserver
{
    /**
     * Handle the status quote "created" event.
     *
     * @param  \App\StatusQuote  $statusQuote
     * @return void
     */
    public function created(StatusQuote $statusQuote)
    {
        cache()->forget('quote_status');
        cache()->forget('data_status_options');
    }

    /**
     * Handle the status quote "updated" event.
     *
     * @param  \App\StatusQuote  $statusQuote
     * @return void
     */
    public function updated(StatusQuote $statusQuote)
    {
        cache()->forget('quote_status');
        cache()->forget('data_status_options');
    }

    /**
     * Handle the status quote "deleted" event.
     *
     * @param  \App\StatusQuote  $statusQuote
     * @return void
     */
    public function deleted(StatusQuote $statusQuote)
    {
        cache()->forget('quote_status');
        cache()->forget('data_status_options');
    }

    /**
     * Handle the status quote "restored" event.
     *
     * @param  \App\StatusQuote  $statusQuote
     * @return void
     */
    public function restored(StatusQuote $statusQuote)
    {
        //
    }

    /**
     * Handle the status quote "force deleted" event.
     *
     * @param  \App\StatusQuote  $statusQuote
     * @return void
     */
    public function forceDeleted(StatusQuote $statusQuote)
    {
        //
    }
}
