<?php

namespace App\Jobs;

// FCL
use App\Rate;
use PrvCarrier;
use App\FailRate;
use App\Currency;
use App\Contract;
use App\LocalCharge;
use App\ScheduleType;
use App\LocalCharPort;
use App\ContractAddons;
use App\ContractCarrier;
use App\LocalCharCountry;
use App\LocalCharCarrier;
use App\ContractUserRestriction;
use App\ContractCompanyRestriction;
// LCL
use App\RateLcl;
use App\ContractLcl;
use App\LocalChargeLcl;
use App\LocalCharPortLcl;
use App\ContractCarrierLcl;
use App\LocalCharCarrierLcl;
use App\LocalCharCountryLcl;
use App\ContractLclUserRestriction;
use App\ContractLclCompanyRestriction;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GeneralJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $accion,$data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($accion,$data)
    {
        $this->accion   = $accion;
        $this->data     = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $requestArrayD = $this->data;

        if(strnatcasecmp($this->accion,'duplicated_fcl') == 0){
            $id           = $requestArrayD['id'];
            $requestArray = $requestArrayD['data'];

            $contract               = Contract::find($id);
            $contract_original_id   = $contract->id;

            $dates      = explode(' / ',$requestArray['validation_expire']);
            $validity   = trim($dates[0]);
            $expire     = trim($dates[1]);

            $contract_new   = new Contract();
            $contract_new->name             = $requestArray['reference'];
            $contract_new->direction_id     = $requestArray['direction_id'];
            $contract_new->company_user_id  = $requestArray['company_user_id'];
            $contract_new->validity         = $validity;
            $contract_new->expire           = $expire;
            $contract_new->status           = 'publish';
            $contract_new->save();
            $contract_new_id                = $contract_new->id;

            foreach($requestArray['carrier_id'] as $carrier_id){
                $carrier_contract               = new ContractCarrier();
                $carrier_contract->carrier_id   = $carrier_id;
                $carrier_contract->contract_id  = $contract_new_id;
                $carrier_contract->save();
            }

            $rates = Rate::where('contract_id',$contract_original_id)->get();

            foreach($rates as $rate_original){
                $rate_new = new Rate();
                $rate_new->origin_port      = $rate_original->origin_port;
                $rate_new->destiny_port     = $rate_original->destiny_port;
                $rate_new->carrier_id       = $rate_original->carrier_id;
                $rate_new->contract_id      = $contract_new_id;
                $rate_new->twuenty          = $rate_original->twuenty;
                $rate_new->forty            = $rate_original->forty;
                $rate_new->fortyhc          = $rate_original->fortyhc;
                $rate_new->fortynor         = $rate_original->fortynor;
                $rate_new->fortyfive        = $rate_original->fortyfive;
                $rate_new->currency_id      = $rate_original->currency_id;
                $rate_new->schedule_type_id = $rate_original->schedule_type_id;
                $rate_new->transit_time     = $rate_original->transit_time;
                $rate_new->via              = $rate_original->via;
                $rate_new->save();

            }

            $addons_originals = ContractAddons::where('contract_id',$contract_original_id)->get();

            foreach($addons_originals as $addons_original){
                $addons_new = new ContractAddons();
                $addons_new->base_port          = $addons_original->base_port;
                $addons_new->port               = $addons_original->port;
                $addons_new->carrier_id         = $addons_original->carrier_id;
                $addons_new->contract_id        = $contract_new_id;
                $addons_new->twuenty_addons     = $addons_original->twuenty_addons;
                $addons_new->forty_addons       = $addons_original->forty_addons;
                $addons_new->fortyhc_addons     = $addons_original->fortyhc_addons;
                $addons_new->fortynor_addons    = $addons_original->fortynor_addons;
                $addons_new->fortyfive_addons   = $addons_original->fortyfive_addons;
                $addons_new->currency_id        = $addons_original->currency_id;
                $addons_new->save();
            }

            $companyRestrictions = ContractCompanyRestriction::where('contract_id',$contract_original_id)->get();

            foreach($companyRestrictions as $companyRestriction_original){
                $companyRestriction_new = new ContractLclCompanyRestriction();
                $companyRestriction_new->company_id     = $companyRestriction_original->company_id;
                $companyRestriction_new->contract_id    = $contract_new_id;
                $companyRestriction_new->save();
            }

            $userRestrictions = ContractUserRestriction::where('contract_id',$contract_original_id)->get();
            foreach($companyRestrictions as $companyRestriction_original){
                $companyRestriction_new  = new ContractUserRestriction();
                $companyRestriction_new->user_id        = $companyRestriction_original->user_id;
                $companyRestriction_new->contract_id    = $contract_new_id;
                $companyRestriction_new->save();
            }
            $localchargers = LocalCharge::where('contract_id',$contract_original_id)->with('localcharports','localcharcountries','localcharcarriers')->get();

            foreach($localchargers as $localcharger){

                $localcharger_new = new LocalCharge();
                $localcharger_new->surcharge_id         = $localcharger->surcharge_id;
                $localcharger_new->typedestiny_id       = $localcharger->typedestiny_id;
                $localcharger_new->contract_id          = $contract_new_id;
                $localcharger_new->calculationtype_id   = $localcharger->calculationtype_id;
                $localcharger_new->ammount              = $localcharger->ammount;
                $localcharger_new->currency_id          = $localcharger->currency_id;
                $localcharger_new->save();
                $localcharger_new_id                    = $localcharger_new->id;

                if(count($localcharger->localcharports) >= 1 ){
                    foreach($localcharger->localcharports as $localcharger_port_original){
                        $localcharger_port_new = new LocalCharPort();
                        $localcharger_port_new->port_orig       = $localcharger_port_original->port_orig;
                        $localcharger_port_new->port_dest       = $localcharger_port_original->port_dest;
                        $localcharger_port_new->localcharge_id  = $localcharger_new_id;
                        $localcharger_port_new->save();

                    }
                }

                if(count($localcharger->localcharcountries) >= 1){
                    foreach($localcharger->localcharcountries as $localcharger_country_original){
                        $localcharger_country_new = new LocalCharCountry();
                        $localcharger_country_new->country_orig     = $localcharger_country_original->country_orig;
                        $localcharger_country_new->country_dest     = $localcharger_country_original->country_dest;
                        $localcharger_country_new->localcharge_id   = $localcharger_new_id;
                        $localcharger_country_new->save();
                    }
                }

                if(count($localcharger->localcharcarriers) >= 1){
                    foreach($localcharger->localcharcarriers as $localcharger_carrier_original){
                        $localcharger_carrier_new = new LocalCharCarrier();
                        $localcharger_carrier_new->carrier_id       = $localcharger_carrier_original->carrier_id;
                        $localcharger_carrier_new->localcharge_id   = $localcharger_new_id;
                        $localcharger_carrier_new->save();
                    }
                }

            }

        } else if(strnatcasecmp($this->accion,'duplicated_lcl') == 0){
            $id           = $requestArrayD['id'];
            $requestArray = $requestArrayD['data'];

            $contract               = ContractLcl::find($id);
            $contract_original_id   = $contract->id;

            $dates      = explode(' / ',$requestArray['validation_expire']);
            $validity   = trim($dates[0]);
            $expire     = trim($dates[1]);

            $contract_new                   = new ContractLcl();
            $contract_new->name             = $requestArray['reference'];
            $contract_new->direction_id     = $requestArray['direction_id'];
            $contract_new->company_user_id  = $requestArray['company_user_id'];
            $contract_new->validity         = $validity;
            $contract_new->expire           = $expire;
            $contract_new->status           = 'publish';
            $contract_new->save();
            $contract_new_id                = $contract_new->id;

            foreach($requestArray['carrier_id'] as $carrier_id){
                $carrier_contract               = new ContractCarrierLcl();
                $carrier_contract->carrier_id   = $carrier_id;
                $carrier_contract->contract_id  = $contract_new_id;
                $carrier_contract->save();
            }

            $rates = RateLcl::where('contractlcl_id',$contract_original_id)->get();

            foreach($rates as $rate_original){
                $rate_new                   = new RateLcl();
                $rate_new->origin_port      = $rate_original->origin_port;
                $rate_new->destiny_port     = $rate_original->destiny_port;
                $rate_new->carrier_id       = $rate_original->carrier_id;
                $rate_new->contractlcl_id   = $contract_new_id;
                $rate_new->uom              = $rate_original->uom;
                $rate_new->minimum          = $rate_original->minimum;
                $rate_new->currency_id      = $rate_original->currency_id;
                $rate_new->schedule_type_id = $rate_original->schedule_type_id;
                $rate_new->transit_time     = $rate_original->transit_time;
                $rate_new->via              = $rate_original->via;
                $rate_new->save();
            }

            $companyRestrictions = ContractLclCompanyRestriction::where('contractlcl_id',$contract_original_id)->get();

            foreach($companyRestrictions as $companyRestriction_original){
                $companyRestriction_new                 = new ContractLclCompanyRestriction();
                $companyRestriction_new->company_id     = $companyRestriction_original->company_id;
                $companyRestriction_new->contractlcl_id = $contract_new_id;
                $companyRestriction_new->save();
            }

            $userRestrictions = ContractLclUserRestriction::where('contractlcl_id',$contract_original_id)->get();
            foreach($companyRestrictions as $companyRestriction_original){
                $companyRestriction_new                 = new ContractLclUserRestriction();
                $companyRestriction_new->user_id        = $companyRestriction_original->user_id;
                $companyRestriction_new->contractlcl_id = $contract_new_id;
                $companyRestriction_new->save();
            }

            $localchargers = LocalChargeLcl::where('contractlcl_id',$contract_original_id)->with('localcharportslcl','localcharcountrieslcl','localcharcarrierslcl')->get();

            foreach($localchargers as $localcharger){

                $localcharger_new                       = new LocalChargeLcl();
                $localcharger_new->surcharge_id         = $localcharger->surcharge_id;
                $localcharger_new->typedestiny_id       = $localcharger->typedestiny_id;
                $localcharger_new->contractlcl_id       = $contract_new_id;
                $localcharger_new->calculationtypelcl_id= $localcharger->calculationtypelcl_id;
                $localcharger_new->ammount              = $localcharger->ammount;
                $localcharger_new->minimum              = $localcharger->minimum;
                $localcharger_new->currency_id          = $localcharger->currency_id;
                $localcharger_new->save();
                $localcharger_new_id                    = $localcharger_new->id;

                if(count($localcharger->localcharportslcl) >= 1 ){
                    foreach($localcharger->localcharportslcl as $localcharger_port_original){
                        $localcharger_port_new                      = new LocalCharPortLcl();
                        $localcharger_port_new->port_orig           = $localcharger_port_original->port_orig;
                        $localcharger_port_new->port_dest           = $localcharger_port_original->port_dest;
                        $localcharger_port_new->localchargelcl_id   = $localcharger_new_id;
                        $localcharger_port_new->save();

                    }
                }

                if(count($localcharger->localcharcountrieslcl) >= 1){
                    foreach($localcharger->localcharcountrieslcl as $localcharger_country_original){
                        $localcharger_country_new                       = new LocalCharCountryLcl();
                        $localcharger_country_new->country_orig         = $localcharger_country_original->country_orig;
                        $localcharger_country_new->country_dest         = $localcharger_country_original->country_dest;
                        $localcharger_country_new->localchargelcl_id    = $localcharger_new_id;
                        $localcharger_country_new->save();
                    }
                }

                if(count($localcharger->localcharcarrierslcl) >= 1){
                    foreach($localcharger->localcharcarrierslcl as $localcharger_carrier_original){
                        $localcharger_carrier_new                       = new LocalCharCarrierLcl();
                        $localcharger_carrier_new->carrier_id           = $localcharger_carrier_original->carrier_id;
                        $localcharger_carrier_new->localchargelcl_id    = $localcharger_new_id;
                        $localcharger_carrier_new->save();
                    }
                }
            }
        } else if(strnatcasecmp($this->accion,'edit_mult_rates_fcl') == 0){

            $id           = $requestArrayD['id'];
            $requestArray = $requestArrayD['data'];
            foreach($requestArray['arreglo'] as $rateF){
                $failrate = FailRate::find($rateF);
                $carrierEX          = null;
                $twuentyEX          = null;
                $fortyEX            = null;
                $fortyhcEX          = null;
                $currencyEX         = null;
                $originResul        = null;
                $originExits        = null;
                $originV            = null;
                $destinResul        = null;
                $destinationExits   = null;
                $destinationV       = null;
                $originEX           = null;
                $destinyEX          = null;
                $twentyVal          = null;
                $fortyVal           = null;
                $fortyhcVal         = null;
                $carrierVal         = null;
                $carrierArr         = null;
                $twentyArr          = null;
                $fortyArr           = null;
                $fortyhcArr         = null;
                $currencyArr        = null;
                $currencyVal        = null;
                $currenct           = null;
                $fortynorVal        = null;
                $fortyfiveVal       = null;
                $scheduleTVal       = null;

                $curreExitBol       = false;
                $twentyExiBol       = false;
                $fortyExiBol        = false;
                $fortyhcExiBol      = false;
                $values             = true;
                $carriExitBol       = false;
                $fortynorExiBol     = false;
                $fortyfiveExiBol    = false;
                $scheduleTBol       = false;

                $carrierArr       = explode("_",$failrate['carrier_id']);
                $currencyArr      = explode("_",$failrate['currency_id']);
                $twuentyArr       = explode("_",$failrate['twuenty']);
                $fortyArr         = explode("_",$failrate['forty']);
                $fortyhcArr       = explode("_",$failrate['fortyhc']);
                $fortynorArr      = explode("_",$failrate['fortynor']);
                $fortyfiveArr     = explode("_",$failrate['fortyfive']);
                $scheduleTArr    = explode("_",$failrate['schedule_type']);
                //dd($failrate);

                $carrierEX     = count($carrierArr);
                $twuentyEX     = count($twentyArr);
                $fortyEX       = count($fortyArr);
                $fortyhcEX     = count($fortyhcArr);
                $currencyEX    = count($currencyArr);

                if( $twuentyEX  <= 1 &&
                   $fortyEX     <= 1 &&  
                   $fortyhcEX   <= 1 &&
                   $currencyEX  <= 1 ){

                    $originV        = $requestArray['origin_id'];
                    $destinationV   = $requestArray['destiny_id'];

                    //---------------- Carrier ------------------------------------------------------------------

                    $carrierArr      = PrvCarrier::get_carrier($carrierArr[0]);
                    $carriExitBol    = $carrierArr['boolean'];
                    $carrierVal      = $carrierArr['carrier'];

                    //---------------- 20' ------------------------------------------------------------------

                    if(empty($twentyArr[0]) != true || (int)$twentyArr[0] == 0){
                        $twentyExiBol = true;
                        $twentyVal   = floatval($twentyArr[0]);
                    }

                    //----------------- 40' -----------------------------------------------------------------

                    if(empty($fortyArr[0]) != true || (int)$fortyArr[0] == 0){
                        $fortyExiBol = true;
                        $fortyVal   = floatval($fortyArr[0]);
                    }

                    //----------------- 40'HC --------------------------------------------------------------

                    if(empty($fortyhcArr[0]) != true || (int)$fortyhcArr[0] == 0){
                        $fortyhcExiBol = true;
                        $fortyhcVal   = floatval($fortyhcArr[0]);
                    }

                    //----------------- 40'NOR -------------------------------------------------------------

                    if(empty($fortynorArr[0]) != true || (int)$fortynorArr[0] == 0){
                        $fortynorExiBol = true;
                        $fortynorVal   = floatval($fortynorArr[0]);
                    }

                    //----------------- 45' ----------------------------------------------------------------

                    if(empty($fortyfiveArr[0]) != true || (int)$fortyfiveArr[0] == 0){
                        $fortyfiveExiBol = true;
                        $fortyfiveVal   = floatval($fortyfiveArr[0]);
                    }

                    if($twentyVal == 0
                       && $fortyVal == 0
                       && $fortyhcVal == 0
                       && $fortynorVal == 0
                       && $fortyfiveVal == 0){
                        $values = false;
                    }
                    //----------------- Currency -----------------------------------------------------------

                    $currenct = Currency::where('alphacode','=',$currencyArr[0])->orWhere('id','=',$currencyArr[0])->first();

                    if(empty($currenct->id) != true){
                        $curreExitBol = true;
                        $currencyVal =  $currenct->id;
                    }

                    $scheduleT = ScheduleType::where('name','=',$scheduleTArr[0])->first();

                    if(empty($scheduleT->id) != true || $scheduleTArr[0] == null){
                        $scheduleTBol = true;
                        if($scheduleTArr[0] != null){
                            $scheduleTVal =  $scheduleT->id;
                        } else {
                            $scheduleTVal = null;
                        }
                    }

                    $array = [
                        '20'    => $twentyExiBol,
                        '40'    => $fortyExiBol,
                        '40h'   => $fortyhcExiBol,
                        '40n'   => $fortynorExiBol,
                        '45'    => $fortyfiveExiBol,
                        'val'   => $values,
                        'sch'   => $scheduleTBol,
                        'car'   => $carriExitBol,
                        'curr'  => $curreExitBol
                    ];
                    //dd($array);

                    // Validacion de los datos en buen estado ------------------------------------------------------------------------
                    if($twentyExiBol   == true && $fortyExiBol    == true &&
                       $fortyhcExiBol  == true && $fortynorExiBol == true &&
                       $fortyfiveExiBol == true && $values        == true &&
                       $scheduleTBol == true && $curreExitBol   == true && $carriExitBol == true){
                        $collecciont = '';
                        $exists = null;
                        $exists = Rate::where('origin_port',$originV)
                            ->where('destiny_port',$destinationV)
                            ->where('carrier_id',$carrierVal)
                            ->where('contract_id',$id)
                            ->where('twuenty',$twentyVal)
                            ->where('forty',$fortyVal)
                            ->where('fortyhc',$fortyhcVal)
                            ->where('fortynor',$fortynorVal)
                            ->where('fortyfive',$fortyfiveVal)
                            ->where('currency_id',$currencyVal)
                            ->where('schedule_type_id',$scheduleTVal)
                            ->where('transit_time',(int)$failrate['transit_time'])
                            ->where('via',$failrate['via'])
                            ->first();
                        if(count($exists) == 0){
                            $collecciont = Rate::create([
                                'origin_port'       => $originV,
                                'destiny_port'      => $destinationV,
                                'carrier_id'        => $carrierVal,                            
                                'contract_id'       => $id,
                                'twuenty'           => $twentyVal,
                                'forty'             => $fortyVal,
                                'fortyhc'           => $fortyhcVal,
                                'fortynor'          => $fortynorVal,
                                'fortyfive'         => $fortyfiveVal,
                                'currency_id'       => $currencyVal,
                                'schedule_type_id'  => $scheduleTVal,
                                'transit_time'      => (int)$failrate['transit_time'],
                                'via'               => $failrate['via']
                            ]);
                        }
                        $failrate->forceDelete();

                    } 

                }
            }
        }
    }
}
