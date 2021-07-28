<?php

namespace App\Jobs;

use App\AccountImportationGlobalcharge;
use App\GlobalCharge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChangeEquimentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;
    protected $module;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $module)
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
        if (strnatcasecmp($this->module, 'gcfcl') == 0) {
            $id = $this->data['id'];
            $equiment = $this->data['equiment'];
            $account = AccountImportationGlobalcharge::find($id);
            $account->data = json_encode(['change_equiment' => true]);
            $account->update();
            $relation = [
                'DryToRF' => [2=>12, 1=>13, 3=>14, 5=>19, 8=>34,4=>22,35=>36,9=>30],
                'DryToOT' => [2=>16, 1=>18, 5=>20, 4=>23 ,35=>37,9=>31,3=>39],
                'DryToFR' => [2=>25, 1=>26, 5=>21, 4=>24,9=>32,35=>38],

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

                'exclusions' => [6,10, 11, 15, 17],
            ];
            $select_relation = null;
            if ($equiment == 2) {
                $select_relation = 'DryToRF';
            } elseif ($equiment == 3) {
                $select_relation = 'DryToOT';
            } elseif ($equiment == 4) {
                $select_relation = 'DryToFR';
            }

            $globals = GlobalCharge::where('account_importation_globalcharge_id', $id)->get();
            //  dd($relation[$select_relation],$globals,in_array(6,$relation['exclusions']));
            foreach ($globals as $global) {
                if (! in_array($global->calculationtype_id, $relation['exclusions'])) {
                    if (array_key_exists($global->calculationtype_id, $relation[$select_relation])) {
                        $global->calculationtype_id = $relation[$select_relation][$global->calculationtype_id];
                        $global->update();
                    } else {
                        $global->forceDelete();
                    }
                }
            }
        }
    }
}

/*

2 - Per 20 DV           | 12 - Per 20 RF        | 16 - Per 20 OT        | 25 - Per 20 FR            |
1 - Per 40 DV           | 13 - Per 40 RF        | 18 - Per 40 OT        | 26 - Per 40 FR            |
3 - Per 40 HC           | 14 - Per 40 HCRF      | 39 - PER 40HC OT      |                           |
8 - Per 45 HC           | 34 - Per 45 RF        |                       |                           |
4 - Per TEU Dry         | 22 - Per TEU RF       | 23 - Per TEU OT       | 24 - Per TEU FR           |
5 - Per Container DRY   | 19 - Per Container RF | 20 - Per Container OT | 21 - Per Container FR     |
35 - Per TON Dry        | 36 - Per TON Reefer   | 37 - Per TON Open Top | 38 - Per TON Flat Rack    |
9 - Per BL              | 30 - Per BL Reefer    | 31 - Per BL Open Top  | 32 - Per BL Flat Rack     |

*/