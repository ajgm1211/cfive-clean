<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RequestToUserMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user,$contract;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $contract)
    {
        $this->user     = $user;
        $this->contract = $contract;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.Requests.ToUserMailabel')->with(['user'=>$this->user,
                                                                       'contract' => $this->contract]);
    }
}
