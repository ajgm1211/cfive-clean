<?php

namespace App\Jobs;

use PrvHarbor;
use App\Region;
use App\User;
use App\Harbor;
use PrvCarrier;
use App\Country;
use App\Carrier;
use App\Currency;
use PrvValidation;
use App\Surcharge;
use Carbon\Carbon;
use App\CompanyUser;
use App\TypeDestiny;
use App\GlobalChargeLcl;
use App\GlobalCharPortLcl;
use App\CalculationTypeLcl;
use App\GlobalCharCarrierLcl;
use App\GlobalCharCountryLcl;
use App\FailedGlobalchargerLcl;
use App\Notifications\N_general;
use App\AccountImportationGlobalChargerLcl;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ReprocessGlobalChargersLclJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $id = $this->id;
        
        $failglobalchargers = FailedGlobalchargerLcl::where('account_imp_gclcl_id','=',$id)->get();
            //dd($failglobalchargers);
            $account_idVal = $id;
            foreach($failglobalchargers as $failglobalcharger){

                $company_user_id    = $failglobalcharger->company_user_id;
                $surchargerEX       = '';
                $origenEX           = '';
                $destinyEX          = '';
                $typedestinyEX      = '';
                $calculationtypeEX  = '';
                $ammountEX          = '';
                $minimumEX          = '';
                $currencyEX         = '';
                $carrierEX          = '';
                $validitytoEX       = '';
                $validityfromEX     = '';
                $originResul        = '';
                $originExits        = '';
                $originV            = '';
                $destinResul        = '';
                $destinationExits   = '';
                $destinationV       = '';
                $surchargerV        = '';
                $typedestunyV       = '';
                $calculationtypeV   = '';
                $amountV            = '';
                $currencyV          = '';
                $carrierV           = '';
                $validityfromV      = '';
                $validitytoV        = '';

                $carrierB           = false;
                $calculationtypeB   = false;
                $typedestinyB       = false;
                $originB            = false;
                $destinyB           = false;
                $surcharB           = false;
                $currencyB          = false;
                $validityfromBol    = false;
                $validitytoBol      = false;


                $surchargerEX       = explode('_',$failglobalcharger['surcharge']);
                $originEX           = explode('_',$failglobalcharger['origin']);
                $destinyEX          = explode('_',$failglobalcharger['destiny']);
                $typedestinyEX      = explode('_',$failglobalcharger['typedestiny']);
                $calculationtypeEX  = explode('_',$failglobalcharger['calculationtypelcl']);
                $ammountEX          = explode('_',$failglobalcharger['ammount']);
                $minimumEX          = explode('_',$failglobalcharger['minimum']);
                $currencyEX         = explode('_',$failglobalcharger['currency']);
                $carrierEX          = explode('_',$failglobalcharger['carrier']);
                $validityfromEX     = explode('_',$failglobalcharger['validity']);
                $validitytoEX       = explode('_',$failglobalcharger['expire']);

                if(count($surchargerEX) == 1     && count($typedestinyEX) == 1
                   && count($typedestinyEX) == 1 && count($calculationtypeEX) == 1
                   && count($ammountEX) == 1     && count($minimumEX) == 1  && count($currencyEX) == 1){

                    // Origen Y Destino ------------------------------------------------------------------------
                    if($failglobalcharger->differentiator  == 1){
                        $resultadoPortOri = PrvHarbor::get_harbor($originEX[0]);
                        $originV  = $resultadoPortOri['puerto'];
                    } else if($failglobalcharger->differentiator  == 2){
                        $resultadoPortOri = PrvHarbor::get_country($originEX[0]);
                        $originV  = $resultadoPortOri['country'];
                    }
                    if($resultadoPortOri['boolean']){
                        $originB = true;    
                    }

                    if($failglobalcharger->differentiator  == 1){
                        $resultadoPortDes = PrvHarbor::get_harbor($destinyEX[0]);
                        $destinationV  = $resultadoPortDes['puerto'];
                    } else if($failglobalcharger->differentiator  == 2){
                        $resultadoPortDes = PrvHarbor::get_country($destinyEX[0]);
                        $destinationV  = $resultadoPortDes['country'];
                    }
                    if($resultadoPortDes['boolean']){
                        $destinyB = true;    
                    }

                    //  Surcharge ------------------------------------------------------------------------------

                    $surchargerV = Surcharge::where('name','=',$surchargerEX[0])->first();
                    if(count($surchargerV) == 1){
                        $surcharB = true;
                        $surchargerV = $surchargerV['id'];
                    }

                    //  Type Destiny ---------------------------------------------------------------------------

                    $typedestunyV = TypeDestiny::where('description','=',$typedestinyEX[0])->first();
                    if(count($typedestunyV) == 1){
                        $typedestinyB = true;
                        $typedestunyV = $typedestunyV['id'];
                    }

                    //  Calculation Type -----------------------------------------------------------------------

                    $calculationtypeV = CalculationTypeLcl::where('code','=',$calculationtypeEX[0])->orWhere('name','=',$calculationtypeEX[0])->first();

                    if(count($calculationtypeV) == 1){
                        $calculationtypeB = true;
                        $calculationtypeV = $calculationtypeV['id'];
                    }

                    //  Amount ---------------------------------------------------------------------------------
                    $amountV    = floatval($ammountEX[0]);
                    
                    //  Minimum --------------------------------------------------------------------------------
                    $minimumV   = floatval($minimumEX[0]);

                    //  Currency -------------------------------------------------------------------------------

                    $currencyV = Currency::where('alphacode','=',$currencyEX[0])->first();
                    if(count($currencyV) == 1){
                        $currencyB = true;
                        $currencyV = $currencyV['id'];
                    }

                    //  Carrier -------------------------------------------------------------------------------

                    $carrierArr = PrvCarrier::get_carrier($carrierEX[0]);
                    $carrierV   = $carrierArr['carrier'];
                    $carrierB   = $carrierArr['boolean'];

                    //------------------ VALIDITY FROM ------------------------------------------------------

                    if(count($validityfromEX) <= 1){
                        try{
                            $validityfromV = Carbon::parse($validityfromEX[0])->format('Y-m-d');
                            $validityfromBol = true;
                        } catch (\Exception $err){

                        }
                    }

                    //------------------ VALIDITY TO --------------------------------------------------------				
                    if(count($validitytoEX) <= 1){
                        try{
                            $validitytoV = Carbon::parse($validitytoEX[0])->format('Y-m-d');
                            $validitytoBol = true;
                        } catch (\Exception $err){

                        }
                    }
                    /*
                    $colleccion = collect([]);
                    $colleccion = [
                        'origen'            =>  $originV,
                        'destiny'           =>  $destinationV,
                        'surcharge'         =>  $surchargerV,
                        'typedestuny'       =>  $typedestunyV,
                        'calculationtypeV'  =>  $calculationtypeV,
                        'amountV'           =>  $amountV,
                        '$minimumV'         =>  $minimumV,
                        'currencyV'         =>  $currencyV,
                        'carrierV'          =>  $carrierV,
                        'validityfromV'     =>  $validityfromV,
                        'validitytoV'       =>  $validitytoV,
                        'surcharB'          =>  $surcharB,
                        'originB'           =>  $originB,
                        'destinyB'          =>  $destinyB,
                        'calculationtypeB'  =>  $calculationtypeB,
                        'carrierB'          =>  $carrierB,
                        'currencyB'         =>  $currencyB,
                        'typedestinyB'      =>  $typedestinyB,
                        'validityfromBol'   =>  $validityfromBol,
                        'validitytoBol'     =>  $validitytoBol
                    ];

                    dd($colleccion);*/

                    if($originB             == true && $destinyB        == true 
                       && $surcharB         == true && $typedestinyB    == true
                       && $calculationtypeB == true && $currencyB       == true
                       && $validityfromBol  == true && $validitytoBol   == true
                       && $carrierB         == true){

                        if($failglobalcharger->differentiator  == 1){ //si es puerto verificamos si exite uno creado con country
                            $typeplace = 'globalcharportlcl';
                        } elseif($failglobalcharger->differentiator  == 2){  //si es country verificamos si exite uno creado con puerto
                            $typeplace = 'globalcharcountrylcl';
                        }

                        $globalChargeArreG = GlobalChargeLcl::where('surcharge_id',$surchargerV)
                            ->where('typedestiny_id',$typedestunyV)
                            ->where('company_user_id',$company_user_id)
                            ->where('calculationtypelcl_id',$calculationtypeV)
                            ->where('ammount',$amountV)
                            ->where('minimum',$minimumV)
                            ->where('validity',$validityfromV)
                            ->where('expire',$validitytoV)
                            ->where('currency_id',$currencyV)
                            ->has($typeplace)
                            ->first();

                        if(count($globalChargeArreG) == 0){

                            $globalChargeArreG = GlobalChargeLcl::create([ // tabla GlobalCharge
                                'surcharge_id'       						=> $surchargerV,
                                'typedestiny_id'     						=> $typedestunyV,
                                'account_imp_gclcl_id'                      => $account_idVal,
                                'company_user_id'    						=> $company_user_id,
                                'calculationtypelcl_id'						=> $calculationtypeV,
                                'ammount'            						=> $amountV,
                                'minimum'            						=> $minimumV,
                                'validity' 									=> $validityfromV,
                                'expire'					 				=> $validitytoV,
                                'currency_id'        						=> $currencyV
                            ]);
                        }

                        GlobalCharCarrierLcl::create([ // tabla GlobalCharCarrier
                            'carrier_id'            => $carrierV,
                            'globalchargelcl_id'    => $globalChargeArreG->id
                        ]);

                        if($failglobalcharger->differentiator  == 1){
                            GlobalCharPortLcl::create([ // tabla GlobalCharPort
                                'port_orig'      	  => $originV,
                                'port_dest'      	  => $destinationV,
                                'globalchargelcl_id'  => $globalChargeArreG->id
                            ]);
                        } else if($failglobalcharger->differentiator  == 2){
                            GlobalCharCountryLcl::create([ // tabla GlobalCharCountry
                                'country_orig'          => $originV,
                                'country_dest'          => $destinationV,
                                'globalchargelcl_id'    => $globalChargeArreG->id                                                   
                            ]);
                        }

                        $failglobalcharger->delete();
                    }
                }

            }

            $account = AccountImportationGlobalChargerLcl::find($id);
            $usersNotifiques = User::where('type','=','admin')->get();
            foreach($usersNotifiques as $userNotifique){
                $message = 'The Globalchargers LCL was Reprocessed. Account: ' . $account['id'].' '.$account['name'] ;
                $userNotifique->notify(new N_general($userNotifique,$message));
            }
    }
}
