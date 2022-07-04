<?php

namespace App\Observers;

use App\Language;

class LanguageObserver
{
    /**
     * Handle the language "created" event.
     *
     * @param  \App\Language  $language
     * @return void
     */
    public function created(Language $language)
    {
        cache()->forget('data_languages');
    }

    /**
     * Handle the language "updated" event.
     *
     * @param  \App\Language  $language
     * @return void
     */
    public function updated(Language $language)
    {
        cache()->forget('data_languages');
    }

    /**
     * Handle the language "deleted" event.
     *
     * @param  \App\Language  $language
     * @return void
     */
    public function deleted(Language $language)
    {
        cache()->forget('data_languages');
    }

    /**
     * Handle the language "restored" event.
     *
     * @param  \App\Language  $language
     * @return void
     */
    public function restored(Language $language)
    {
        //
    }

    /**
     * Handle the language "force deleted" event.
     *
     * @param  \App\Language  $language
     * @return void
     */
    public function forceDeleted(Language $language)
    {
        //
    }
}
