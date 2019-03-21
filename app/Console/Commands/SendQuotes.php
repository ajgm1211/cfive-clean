<?php

namespace App\Console\Commands;

use App\Mail\SendQuotePdf;
use App\Quote;
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
        $notifications=SendQuote::where('status',0)->get();
        foreach ($notifications as $item) {
            $quote=Quote::findOrFail($item->quote_id);
            Mail::to($item->to)->bcc($item->from)->send(new SendQuotePdf($item->subject,$item->body,$quote,$item->from));
            $send_notification=SendQuote::find($item->id);
            $send_notification->status=1;
            $send_notification->update();
        }
        $this->info('Command Send Quotes executed successfully!');
    }
}