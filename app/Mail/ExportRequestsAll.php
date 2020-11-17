<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportRequestsAll extends Mailable
{
    use Queueable, SerializesModels;
    public $namefile,$selector;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($namefile,$selector)
    {
        $this->namefile = $namefile;
        $this->selector = $selector;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.export.requests_all')
         ->with(['selector' => $this->selector])
         ->attachFromStorageDisk('RequestFiles',$this->namefile)
         ->from('info@cargofive.com')
         ->subject('Exportation Finished');

    }
}
