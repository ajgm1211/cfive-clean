<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ExportContractMail extends Mailable
{
    use Queueable, SerializesModels;
    public $namefile;
    public $contract;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($namefile, $contract)
    {
        $this->namefile = $namefile;
        $this->contract = $contract;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.export.contract')
         ->with(['contract' => $this->contract])
         ->attachFromStorageDisk('RequestFiles', $this->namefile)
         ->from('info@cargofive.com')
         ->subject('Exportation Finished');
    }
}
