<?php

namespace App\Observers;

use App\TermAndConditionV2;

class TermAndConditionV2Observer
{
    /**
     * Handle the term and condition v2 "created" event.
     *
     * @param  \App\TermAndConditionV2  $termAndConditionV2
     * @return void
     */
    public function created(TermAndConditionV2 $termAndConditionV2)
    {
        cache()->forget('data_terms_and_conditions');
    }

    /**
     * Handle the term and condition v2 "updated" event.
     *
     * @param  \App\TermAndConditionV2  $termAndConditionV2
     * @return void
     */
    public function updated(TermAndConditionV2 $termAndConditionV2)
    {
        cache()->forget('data_terms_and_conditions');
    }

    /**
     * Handle the term and condition v2 "deleted" event.
     *
     * @param  \App\TermAndConditionV2  $termAndConditionV2
     * @return void
     */
    public function deleted(TermAndConditionV2 $termAndConditionV2)
    {
        cache()->forget('data_terms_and_conditions');
    }

    /**
     * Handle the term and condition v2 "restored" event.
     *
     * @param  \App\TermAndConditionV2  $termAndConditionV2
     * @return void
     */
    public function restored(TermAndConditionV2 $termAndConditionV2)
    {
        //
    }

    /**
     * Handle the term and condition v2 "force deleted" event.
     *
     * @param  \App\TermAndConditionV2  $termAndConditionV2
     * @return void
     */
    public function forceDeleted(TermAndConditionV2 $termAndConditionV2)
    {
        //
    }
}
