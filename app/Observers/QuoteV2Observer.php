<?php

namespace App\Observers;

use App\Http\Traits\QuoteV2Trait;
use App\QuoteV2;

class QuoteV2Observer
{
    use QuoteV2Trait;

    public function created(QuoteV2 $quoteV2)
    {
    
    }

    /**
     * Handle the quote v2 "updated" event.
     *
     * @param  \App\QuoteV2  $quoteV2
     * @return void
     */
    public function updated(QuoteV2 $quoteV2)
    {
        //
    }

    /**
     * Handle the quote v2 "deleted" event.
     *
     * @param  \App\QuoteV2  $quoteV2
     * @return void
     */
    public function deleted(QuoteV2 $quoteV2)
    {
        //
    }

    /**
     * Handle the quote v2 "restored" event.
     *
     * @param  \App\QuoteV2  $quoteV2
     * @return void
     */
    public function restored(QuoteV2 $quoteV2)
    {
        //
    }

    /**
     * Handle the quote v2 "force deleted" event.
     *
     * @param  \App\QuoteV2  $quoteV2
     * @return void
     */
    public function forceDeleted(QuoteV2 $quoteV2)
    {
        //
    }
}
