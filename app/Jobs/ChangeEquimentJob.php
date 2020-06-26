<?php

namespace App\Jobs;
use App\GlobalCharge;
use App\AccountImportationGlobalcharge;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ChangeEquimentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data,$module;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data,$module)
    {
        $this->data = $data;
        $this->module = $module;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(strnatcasecmp($this->module,'gcfcl')==0){
            $id         = $this->data['id'];
            $equiment   = $this->data['equiment'];
            $account    = AccountImportationGlobalcharge::find($id);
            $account->data = json_encode(['change_equiment' => true]);
            $account->update();
            $relation = [
                'DryToRF' => [2=>12,1=>13,3=>14,5=>19, 4=>22],
                'DryToOT' => [2=>16,1=>18,5=>20, 4=>23],
                'DryToFR' => [2=>25,1=>26,5=>21, 4=>24],

                //            'RFToDry' => [12=>2,13=>1,14=>3,19=>5, 22=>4]
                //            'RFToOT'  => [12=>16,13=>18,19=>20, 22=>23],
                //            'RFToFR'  => [12=>25,13=>26,19=>21, 22=>24],
                //            
                //            'OTToDry' => [16=>2,18=>1,20=>5, 23=>4],
                //            'OTToRF'  => [16=>12,18=>13,20=>19, 23=>22],
                //            'OTToFR'  => [16=>25,18=>26,20=>21, 23=>24],
                //            
                //            'FRToDry' => [25=>2,26=>1,21=>5, 24=>4],
                //            'FRToRF'  => [25=>12,26=>13,21=>19, 24=>22],
                //            'FRToOT'  => [25=>16,26=>18,21=>20, 24=>23],

                'exclusions' => [6,9,10,11,15,17]
            ];
            $select_relation = null;
            if($equiment == 2){
                $select_relation = 'DryToRF';
            } else if($equiment == 3){
                $select_relation = 'DryToOT';
            } else if($equiment == 4){
                $select_relation = 'DryToFR';
            }

            $globals = GlobalCharge::where('account_importation_globalcharge_id',$id)->get();
            //  dd($relation[$select_relation],$globals,in_array(6,$relation['exclusions']));
            foreach($globals as $global){
                if(!in_array($global->calculationtype_id,$relation['exclusions'])){
                    if(array_key_exists($global->calculationtype_id,$relation[$select_relation])){
                        $global->calculationtype_id = $relation[$select_relation][$global->calculationtype_id];                    
                        $global->update();
                    } else{
                        $global->forceDelete();
                    }
                } 

            }
        }
    }
}
