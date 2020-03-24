<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendQuotePdf;
use App\QuoteV2;
use App\SendQuote;

class SendQuotesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $notifications=SendQuote::where('status',0)->get();
            foreach ($notifications as $item) {
                $quote=QuoteV2::findOrFail($item->quote_id);
                $send_notification=SendQuote::find($item->id);
                $send_notification->status=1;
                $send_notification->update();

                Mail::to($item->to)->bcc($item->from)->send(new SendQuotePdf($item->subject,$item->body,$quote,$item->from,$item->sign,$item->sign_type));
            }
        } catch(\Exception $e){
            $e->getMessage();
        }
    }
}
