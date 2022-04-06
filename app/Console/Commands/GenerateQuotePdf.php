<?php

namespace App\Console\Commands;

use App\FclPdf;
use App\LclPdf;
use App\QuoteV2;
use Log;
use Exception;
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
        $quotes = QuoteV2::whereHas('pdf_quote_status', function ($query) {
            $query->where('status', false);
        })->get();

        foreach ($quotes as $quote) {
            try {
                $upload = true;

                switch ($quote->type) {
                    case "FCL":
                        $pdf = new FclPdf($upload);
                        $pdf->generate($quote);
                        $quote->pdf_quote_status()->update(['status' => true]);

                        break;
                    case "LCL":
                        $pdf = new LclPdf($upload);
                        $pdf->generate($quote);
                        $quote->pdf_quote_status()->update(['status' => true]);

                        break;
                }
            } catch (Exception $e) {
                Log::error("Error creating API PDF: " . $e->getMessage() . " for quote: " . $quote->quote_id . " in line : " . $e->getLine());
                continue;
            }
        }

        $this->info('Command Generate Quote PDF executed successfully!');
    }
}
