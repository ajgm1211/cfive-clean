<?php

namespace App\Observers;

use App\Contract;

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
        //
    }

    /**
     * Handle the contract "updated" event.
     *
     * @param  \App\Contract  $contract
     * @return void
     */
    public function updated(Contract $contract)
    {
        //
    }

    /**
     * Handle the contract "deleted" event.
     *
     * @param  \App\Contract  $contract
     * @return void
     */
    public function deleted(Contract $contract)
    {
        //
    }
}
