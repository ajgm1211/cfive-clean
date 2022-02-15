<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewRequestGlobalChargeToAdminMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $admin;
    public $contract;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($admin, $user, $contract)
    {
        $this->admin = $admin;
        $this->user = $user;
        $this->contract = $contract;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.RequestGlobalcharge.ToAdminMailabel')->with(['admin' => $this->admin,
                                                                                                             'user' => $this->user,
                                                                                                            'contract' => $this->contract, ])->from('info@cargofive.com');
    }
}
