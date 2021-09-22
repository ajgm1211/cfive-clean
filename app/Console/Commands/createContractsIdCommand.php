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

        foreach($contracts as $contract){
            $lastContract = Contract::where('company_user_id',$contract->company_user_id)
            ->orderBy('contract_code', 'desc')->first();

            $company = strtoupper(substr($contract->companyUser->name, 0, 3));

            $code = 'FCL-'.$company.'-0001';
    
            if($lastContract->contract_code){
                $lastContractId = (int)substr($lastContract->contract_code, -3);
                $lastContractId = str_pad($lastContractId+1, 4, '0', STR_PAD_LEFT);
                $code = 'FCL-'.$company.'-'.$lastContractId;
            }
    
            $contract->contract_code = $code;
            $contract->save();
        }

        foreach($contracts_lcl as $contract){
            $lastContract = ContractLcl::where('company_user_id',$contract->company_user_id)->
            orderBy('contract_code', 'desc')->first();
    
            $company = strtoupper(substr($contract->companyUser->name, 0, 3));
    
            $code = 'LCL-'.$company.'-0001';
            
            if($lastContract->contract_code){
                $lastContractId = (int)substr($lastContract->contract_code, -3);
                $lastContractId = str_pad($lastContractId+1, 4, '0', STR_PAD_LEFT);
                $code = 'LCL-'.$company.'-'.$lastContractId;
            }
    
            $contract->contract_code = $code;
            $contract->save();
        }
    }
}
