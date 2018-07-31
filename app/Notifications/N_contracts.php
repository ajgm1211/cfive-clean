<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Contract;
use App\User;

class N_contracts extends Notification implements ShouldQueue
{
  use Queueable;

  protected $user;
  protected $contract;
  public $thread;

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

  /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
  public function toMail($notifiable)
  {
    return (new MailMessage)
      ->line('The introduction to the notification.')
      ->action('Notification Action', url('/'))
      ->line('Thank you for using our application!');
  }


  public function toDatabase($notifiable)
  {
    return [

      'id_user' => $this->user->id,
      'name_user' => $this->user->name,
      'id_company' => $this->user->company_user_id,
      'number_contract' => $this->contract->number,
    ];
  }
  public function toBroadcast($notifiable)
  {
    return new BroadcastMessage([

      'id_user' => $this->user->id,
      'name_user' => $this->user->name,
      'id_company' => $this->user->company_user_id,
      'number_contract' => $this->contract->number,
    ]);
  }


}
