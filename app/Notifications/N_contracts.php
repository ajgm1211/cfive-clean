<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

use App\Contract;
use App\User;

class N_contracts extends Notification implements ShouldQueue
{
  use Queueable;

  protected $user;
  protected $contract;


  public function __construct(User $user, Contract $contract)
  {
    $this->user = $user;
    $this->contract = $contract;    
  }
  /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
  public function via($notifiable)
  {
    return ['database','broadcast'];
  }


  public function toArray($notifiable)
  {
    return [
      'id' => $this->id,
      'read_at' => null,
      'data' => [
        'id_company' => $this->user->company_user_id,
        'number_contract' => $this->contract->number,
        'name_user' => $this->user->name,
      ],
    ];
  }

}
