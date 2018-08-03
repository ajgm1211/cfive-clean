<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendQuotePdf extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject,$body,$quote)
    {
        $this->subject = $subject;
        $this->text = $body;
        $this->quote_id = $quote->id;
        $this->created = $quote->created_at;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(\Auth::user()->email)
        ->view('emails.quote_pdf')
        ->subject($this->subject)
        ->with(['text' => $this->text])
        ->attach('pdf/temp_' . $this->quote_id . '.pdf', [
                    'as' => 'Quote_'.$this->quote_id.'_'.$this->created.'.pdf',
                    'mime' => 'application/pdf',
                ]);
    }
}
