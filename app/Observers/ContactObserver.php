<?php

namespace App\Observers;

use App\Http\Traits\QuoteV2Trait;
use App\Contact;

class ContactObserver
{
    use QuoteV2Trait;
    
    public function created(Contact $contact)
    {
        $this->forgetKeyCacheQuotationsFormRequiredDataOnAllUsers();
    }

    /**
     * Handle the contact "updated" event.
     *
     * @param  \App\Contact  $contact
     * @return void
     */
    public function updated(Contact $contact)
    {
        $this->forgetKeyCacheQuotationsFormRequiredDataOnAllUsers();
    }

    /**
     * Handle the contact "deleted" event.
     *
     * @param  \App\Contact  $contact
     * @return void
     */
    public function deleted(Contact $contact)
    {
        $this->forgetKeyCacheQuotationsFormRequiredDataOnAllUsers();
    }

    /**
     * Handle the contact "restored" event.
     *
     * @param  \App\Contact  $contact
     * @return void
     */
    public function restored(Contact $contact)
    {
        //
    }

    /**
     * Handle the contact "force deleted" event.
     *
     * @param  \App\Contact  $contact
     * @return void
     */
    public function forceDeleted(Contact $contact)
    {
        //
    }
}
