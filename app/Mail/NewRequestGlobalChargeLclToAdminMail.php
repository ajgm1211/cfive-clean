<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewRequestGlobalChargeLclToAdminMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user,$admin,$contract;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($admin,$user,$contract)
    {
        $this->admin    = $admin;
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
        return $this->markdown('emails.RequestGlobalchargersLcl.ToAdminMailabel')->with(['admin' => $this->admin,
                                                                                         'user' => $this->user,
                                                                                         'contract' => $this->contract])->from('info@cargofive.com');
    }
}
