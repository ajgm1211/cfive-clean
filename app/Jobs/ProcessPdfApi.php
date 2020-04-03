<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\CompanyUser;
use App\Currency;
use App\QuoteV2;
use App\User;
use App\Http\Traits\QuoteV2Trait;

class ProcessPdfApi implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,QuoteV2Trait;
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
    $quote->addMedia(public_path().'/pdf/quote-'.$quote->quote_id.'.pdf')->toMediaCollection('document','pdfApiS3');
  }
}
