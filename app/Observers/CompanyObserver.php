<?php

namespace App\Observers;

use App\Company;

class CompanyObserver
{
    /**
     * Handle the company "created" event.
     *
     * @param  \App\Company  $company
     * @return void
     */
    public function created(Company $company)
    {
        cache()->forget('quotations_form_required_data');
    }

    /**
     * Handle the company "updated" event.
     *
     * @param  \App\Company  $company
     * @return void
     */
    public function updated(Company $company)
    {
        cache()->forget('quotations_form_required_data');
    }

    /**
     * Handle the company "deleted" event.
     *
     * @param  \App\Company  $company
     * @return void
     */
    public function deleted(Company $company)
    {
        cache()->forget('quotations_form_required_data');
    }

    /**
     * Handle the company "restored" event.
     *
     * @param  \App\Company  $company
     * @return void
     */
    public function restored(Company $company)
    {
        //
    }

    /**
     * Handle the company "force deleted" event.
     *
     * @param  \App\Company  $company
     * @return void
     */
    public function forceDeleted(Company $company)
    {
        //
    }
}
