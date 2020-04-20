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
    public function __construct($subject,$body,$quote,$sender,$sign,$sign_type)
    {
        $this->subject = $subject;
        $this->text = $body;
        $this->sender = $sender;
        $this->quote_id = $quote->id;
        $this->created = $quote->created_at;
        $this->sign = $sign;
        $this->sign_type = $sign_type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@cargofive.com', $this->sender)
            ->view('emails.quote_pdf')
            ->subject($this->subject)
            ->with(['text' => $this->text,'sign'=> $this->sign,'sign_type'=>$this->sign_type])
            ->attach(public_path().'/pdf/temp_' . $this->quote_id . '.pdf', [
                'as' => 'Quote_'.$this->quote_id.'_'.$this->created.'.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
