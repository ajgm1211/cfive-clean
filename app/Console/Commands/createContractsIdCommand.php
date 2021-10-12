<?php

namespace App\Console\Commands;

use App\Contract;
use App\ContractLcl;
use Illuminate\Console\Command;

class createContractsIdCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:createContractsIdCommand';

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
        $contracts = Contract::all();
        $contracts_lcl = ContractLcl::all();

        foreach ($contracts as $contract) {
            $lastContract = Contract::where('company_user_id', $contract->company_user_id)
                ->whereNotNull('contract_code')->orderBy('id', 'desc')->first();

            $company = strtoupper(substr($contract->companyUser->name, 0, 3));

            $code = 'FCL-'.$company.'-1';

            if (!empty($lastContract)) {
                $lastContractId = intval(str_replace('FCL-' . $company . "-", "", $lastContract->contract_code));
                $code = 'FCL-' . $company . '-' . strval($lastContractId + 1);
            }

            $contract->contract_code = $code;
            $contract->update();
        }

        foreach ($contracts_lcl as $contract) {
            $lastContract = ContractLcl::where('company_user_id', $contract->company_user_id)
            ->whereNotNull('contract_code')->orderBy('id', 'desc')->first();

            $company = strtoupper(substr($contract->companyUser->name, 0, 3));

            $code = 'LCL-' . $company . '-1';

            if (!empty($lastContract)) {
                $lastContractId = intval(str_replace('LCL-' . $company . "-", "", $lastContract->contract_code));
                $code = 'LCL-' . $company . '-' . strval($lastContractId + 1);
            }

            $contract->contract_code = $code;
            $contract->update();
        }
    }
}
