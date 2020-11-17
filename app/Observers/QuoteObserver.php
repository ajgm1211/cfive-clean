<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Quote;
use App\Notifications\N_general;
use App\User;

class QuoteObserver
{
    /**
     * Handle the quote "created" event.
     *
     * @param  \App\Quote  $quote
     * @return void
     */
    public function created(Quote $quote)
    {
        $userLogin  = auth()->user();
        $idCompany = $userLogin->company_user_id;
        $users = User::where('company_user_id', '=', $idCompany)->where('type', 'company')->orWhere('id', '=', $userLogin->id)->get();
        $message = ' created the ' . $quote->company_quote;
        foreach ($users as $user) {
            $user->notify(new N_general($userLogin, $message));
        }
    }

    /**
     * Handle the quote "updated" event.
     *
     * @param  \App\Quote  $quote
     * @return void
     */
    public function updated(Quote $quote)
    {
        //
    }

    /**
     * Handle the quote "deleted" event.
     *
     * @param  \App\Quote  $quote
     * @return void
     */
    public function deleted(Quote $quote)
    {
        //
    }

    /**
     * Handle the quote "restored" event.
     *
     * @param  \App\Quote  $quote
     * @return void
     */
    public function restored(Quote $quote)
    {
        //
    }

    /**
     * Handle the quote "force deleted" event.
     *
     * @param  \App\Quote  $quote
     * @return void
     */
    public function forceDeleted(Quote $quote)
    {
        //
    }
}
