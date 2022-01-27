<?php

namespace App\Policies;

use App\User;
use App\QuoteV2;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuoteV2Policy
{
    use HandlesAuthorization;

    public function author(User $user, QuoteV2 $quote){

        $options = \Auth::user()->options;
        $user_id = \Auth::user()->id;

        if($options === 'comercial') {
            return $user_id == $quote->user_id;        
        }

        return true;
    }
}
