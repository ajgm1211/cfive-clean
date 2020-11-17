<?php

namespace App\Jobs;

use App\Mail\SendQuotePdf;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendQuotes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $subject;
    protected $body;
    protected $to;
    protected $quote;
    protected $email;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subject,$body,$to,$quote,$email)
    {
        $this->subject  = $subject;
        $this->body = $body;
        $this->to = $to;
        $this->quote = $quote;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            if($this->to!=''){
                $explode=explode(';',$this->to);
                foreach($explode as $item) {
                    \Mail::to(trim($item))->bcc(\Auth::user()->email,\Auth::user()->name)->send(new SendQuotePdf($this->subject,$this->body,$this->quote));
                }
            }else{
                \Mail::to($this->email)->bcc(\Auth::user()->email,\Auth::user()->name)->send(new SendQuotePdf($this->subject,$this->body,$this->quote));
            }
        } catch(\Exception $e){
            $e->getMessage();
        }
    }
}
