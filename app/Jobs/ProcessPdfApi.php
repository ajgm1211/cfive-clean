<?php

namespace App\Jobs;

use App\CompanyUser;
use App\Currency;
use App\Http\Traits\QuoteV2Trait;
use App\QuoteV2;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPdfApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, QuoteV2Trait;
    protected $quote;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($quot)
    {
        $this->quote = $quot;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $quote = $this->quote;
        if (file_exists(public_path().'/pdf/quote-'.$quote->quote_id.'.pdf')) {
            $quote->addMedia(public_path().'/pdf/quote-'.$quote->quote_id.'.pdf')->toMediaCollection('document', 'pdfApiS3');
        }
    }
}
