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
    public function __construct($day, $month, $day_spanish, $month_spanish, $date, $hour, $duration)
    {
        $this->day = $day;
        $this->day_spanish = $day_spanish;
        $this->month = $month;
        $this->month_spanish = $month_spanish;
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
        return $this->from('info@cargofive.com', 'Cargofive')
            ->view('emails.notifications.maintenance')
            ->subject('Cargofive scheduled maintenance notice | Aviso de mantenimiento')
            ->with(['day' => $this->day,'day_spanish' => $this->day_spanish,'month'=> $this->month,'month_spanish'=> $this->month_spanish,'date'=> $this->date,'hour'=>$this->hour,'duration'=>$this->duration]);
    }
}
