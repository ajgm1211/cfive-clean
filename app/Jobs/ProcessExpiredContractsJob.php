<?php

namespace App\Jobs;

use App\Contract;
use App\ContractLcl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessExpiredContractsJob implements ShouldQueue
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
            $contracts = Contract::where('status', 'publish')->where('expire', '<=', date('Y-m-d'))->get();
            foreach ($contracts as $contract) {
                Contract::where('id', $contract->id)->update(['status' => 'expired']);
            }
            $contracts_lcl = ContractLcl::where('status', 'publish')->where('expire', '<=', date('Y-m-d'))->get();
            foreach ($contracts_lcl as $contract) {
                ContractLcl::where('id', $contract->id)->update(['status' => 'expired']);
            }
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }
}
