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
    $user = $post->user;
    foreach ($user->followers as $follower) {
      $follower->notify(new NewPost($user, $post));
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
