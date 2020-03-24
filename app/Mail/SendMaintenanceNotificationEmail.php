<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMaintenanceNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($day, $month, $date, $hour, $duration)
    {
        $this->day = $day;
        $this->month = $month;
        $this->date = $date;
        $this->hour = $hour;
        $this->duration = $duration;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@cargofive.com')
            ->view('emails.notifications.maintenance')
            ->subject('Cargofive scheduled maintenance')
            ->with(['day' => $this->day,'month'=> $this->month,'date'=> $this->date,'hour'=>$this->hour,'duration'=>$this->duration]);
    }
}
