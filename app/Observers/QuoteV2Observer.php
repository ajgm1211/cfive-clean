<?php

namespace App\Observers;

use App\QuoteV2;
use App\User;
use App\Jobs\AddDataToCacheForQuoteFilterOptionsJob;

class QuoteV2Observer
{

    public function created(QuoteV2 $quoteV2)
    {
        $user = auth()->user();
        $company_user_id = $user->company_user_id;

        
        // Encolar carga de data en caché, se puede condicionar solo para company_user crítico
        AddDataToCacheForQuoteFilterOptionsJob::dispatch($user);

    }

    public function updated(QuoteV2 $quoteV2)
    {
        //
    }

    public function deleted(QuoteV2 $quoteV2)
    {
        $user = auth()->user();
        $company_user_id = $user->company_user_id;

        

        // Encolar carga de data en caché, se puede condicionar solo para company_user crítico
        AddDataToCacheForQuoteFilterOptionsJob::dispatch($user);

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
