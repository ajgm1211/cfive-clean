<?php

namespace App\Console\Commands;

use App\Mail\SendQuotePdf;
use App\QuoteV2;
use App\SendQuote;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendQuotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sendQuotes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications with status 0';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
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
        $this->info('Command Send Quotes executed successfully!');
    }
}