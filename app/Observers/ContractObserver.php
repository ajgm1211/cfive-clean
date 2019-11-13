<?php

namespace App\Observers;
use Illuminate\Support\Facades\Auth;
use App\Notifications\N_general;
use App\Contract;
use App\User;

class ContractObserver
{
    /**
     * Handle to the contract "created" event.
     *
     * @param  \App\Contract  $contract
     * @return void
     */
    public function created(Contract $contract)
    {
        $userLogin  = \Auth::user();
        if(empty($userLogin) != true){
            $idCompany = $contract->company_user_id;

            $users = User::all()->where('company_user_id','=',$idCompany);
            $message = 'created the contract ' . $contract->name ;
            foreach ($users as $user) {
                $user->notify(new N_general($userLogin,$message));
            }
        }

    }

    /**
     * Handle the contract "updated" event.
     *
     * @param  \App\Contract  $contract
     * @return void
     */

    public function updated(Contract $contract)
    {

        if(auth()->user() != null ){
            $userLogin  = auth()->user();
            $idCompany = $contract->company_user_id;
            $users = User::all()->where('company_user_id','=',$idCompany);
            $message = ' updated the contract ' . $contract->name ;

            foreach ($users as $user) {
                $user->notify(new N_general($userLogin,$message));
            }

        }



    }

    /**
     * Handle the contract "deleted" event.
     *
     * @param  \App\Contract  $contract
     * @return void
     */
    public function deleted(Contract $contract)
    {
        $userLogin  = auth()->user();
        $idCompany = $contract->company_user_id;

        $users = User::all()->where('company_user_id','=',$idCompany);
        $message = 'deleted he contract ' . $contract->name ;
        foreach ($users as $user) {
            $user->notify(new N_general($userLogin,$message));
        }
    }
}
