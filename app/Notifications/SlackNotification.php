<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;

  class SlackNotification extends Notification{
  use Queueable;
  private $message;
  /**
     * Create a new notification instance.
     *
     * @return void
     */
  public function __construct($message)
  {
    $this->message = $message;
  }


  public function via($notifiable)
  {
    return ['slack'];
  }
  public function toSlack($notifiable)
  {

     return (new SlackMessage)
                ->from('CargoFive', ':extraterrestre:')
                ->to('#request-importation')
                ->content($this->message);
  }
  /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
  public function toArray($notifiable)
  {

  }

}
