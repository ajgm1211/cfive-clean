<?php

namespace App\Jobs;

use App\FclPdf;
use App\LclPdf;
use App\PdfQuoteStatus;
use App\QuoteV2;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GeneratePdfToApiJob implements ShouldQueue
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
        try {
            $quotes = QuoteV2::whereHas('pdf_quote_status', function ($query) {
                $query->where('status', 0);
            })->get();

            $upload = true;

            foreach ($quotes as $quote) {
                switch ($quote->type) {
                    case "FCL":
                        $pdf = new FclPdf($upload);
                        $pdf->generate($quote);
                        $quote->pdf_quote_status()->update(['status'=>1]);
                        break;
                    case "LCL":
                        $pdf = new LclPdf($upload);
                        $pdf->generate($quote);
                        $quote->pdf_quote_status()->update(['status'=>1]);
                        break;
                }
            }
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }
}
