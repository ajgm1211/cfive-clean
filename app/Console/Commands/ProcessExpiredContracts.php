<?php

namespace App\Console\Commands;

use App\Contract;
use App\ContractLcl;
use Illuminate\Console\Command;

class ProcessExpiredContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:processExpiredContracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status in expired contracts';

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
			$contracts=Contract::where('status','publish')->where('expire','<=',date('Y-m-d'))->get();
			foreach ($contracts as $contract){
				Contract::where('id',$contract->id)->update(['status' => 'expired']);
			}
			$contracts_lcl=ContractLcl::where('status','publish')->where('expire','<=',date('Y-m-d'))->get();
			foreach ($contracts_lcl as $contract){
				ContractLcl::where('id',$contract->id)->update(['status' => 'expired']);
			}
		} catch(\Exception $e){
            return $this->info($e->getMessage());
        }
        $this->info('Command Process Expired Contracts executed successfully!');
    }
}