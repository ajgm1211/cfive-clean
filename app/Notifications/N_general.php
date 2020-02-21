<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\User;
class N_general extends Notification
{
  use Queueable;

  protected $user;
  protected $message;


  public function __construct(User $user, $message)
  {
    $this->user = $user;
    $this->message = $message;    
  }
  /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
  public function via($notifiable)
  {
    return ['broadcast'];
  }


  public function toDatabase($notifiable)
  {
    return [
      'id' => $this->id,
      'read_at' => null,
      'id_company' => $this->user->company_user_id,
      'name_user' => $this->user->name,
      'message' => $this->message,
      'data' => [
        'id_company' => $this->user->company_user_id,
        'name_user' => $this->user->name,
        'message' => $this->message,
      ],
    ];
  }
    public function toArray($notifiable)
  {
    return [
      'id' => $this->id,
      'read_at' => null,
      'id_company' => $this->user->company_user_id,
      'name_user' => $this->user->name,
      'message' => $this->message,
      'data' => [
        'id_company' => $this->user->company_user_id,
        'name_user' => $this->user->name,
        'message' => $this->message,
      ],
    ];
  }
}
