<?php

namespace App\Observers;

use App\Harbor;

class HarborObserver
{
    /**
     * Handle the harbor "created" event.
     *
     * @param  \App\Harbor  $harbor
     * @return void
     */
    public function created(Harbor $harbor)
    {
        cache()->forget('habors_all');
        cache()->forget('data_harbors');
    }

    /**
     * Handle the harbor "updated" event.
     *
     * @param  \App\Harbor  $harbor
     * @return void
     */
    public function updated(Harbor $harbor)
    {
        cache()->forget('habors_all');
        cache()->forget('data_harbors');
    }

    /**
     * Handle the harbor "deleted" event.
     *
     * @param  \App\Harbor  $harbor
     * @return void
     */
    public function deleted(Harbor $harbor)
    {
        cache()->forget('habors_all');
        cache()->forget('data_harbors');
    }

    /**
     * Handle the harbor "restored" event.
     *
     * @param  \App\Harbor  $harbor
     * @return void
     */
    public function restored(Harbor $harbor)
    {
        //
    }

    /**
     * Handle the harbor "force deleted" event.
     *
     * @param  \App\Harbor  $harbor
     * @return void
     */
    public function forceDeleted(Harbor $harbor)
    {
        //
    }
}
