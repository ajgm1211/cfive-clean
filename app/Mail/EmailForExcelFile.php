<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailForExcelFile extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    public $text;
    public $path;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $body, $path)
    {
        $this->subject = $subject;
        $this->text = $body;
        $this->path = $path;
   

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.excel_csv')
        ->with(['text' => $this->text])
        ->attachFromStorageDisk('ExcelFiles',$this->path)
        ->from('info@cargofive.com')
        ->subject('Exportation Finished');
  
    }
}
