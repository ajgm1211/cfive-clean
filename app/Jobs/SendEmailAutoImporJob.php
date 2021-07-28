<?php

namespace App\Jobs;

use App\Mail\NotificationAutoImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEmailAutoImporJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $email;
    public $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email, $message)
    {
        $this->email = $email;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Mail::to($this->email)->send(new NotificationAutoImport($this->message));
    }
}
