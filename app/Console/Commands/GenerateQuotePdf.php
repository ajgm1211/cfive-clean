<?php

namespace App\Console\Commands;

use App\FclPdf;
use App\LclPdf;
use App\QuoteV2;
use Illuminate\Console\Command;

class GenerateQuotePdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:generateQuotePdf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        $this->info('Command Generate Quote PDF executed successfully!');
    }
}
