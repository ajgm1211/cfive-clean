<?php

namespace App\Jobs;

use App\Surcharge;
use App\GlobalCharge;
use App\GlobalCharPort;
use App\GlobalChargeLcl;
use App\GlobalCharPortLcl;
use App\GlobalCharCarrier;
use App\GlobalCharCountry;
use App\GlobalCharCarrierLcl;
use App\GlobalCharCountryLcl;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GlobalchargerDuplicateFclLclJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $arreglo,$selector;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($array,$selector)
    {
        $this->arreglo    = $array;
        $this->selector = $selector;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $request    = $this->arreglo;
        $selector   = $this->selector;
        if(strnatcasecmp($selector,'fcl') == 0){
            $company_user = $request['company_user_id'];
            foreach($request['idArray'] as $gb){

                $globalOfAr = GlobalCharge::find($gb);
                $globalOfAr->load('surcharge','globalcharcarrier','globalcharport.portOrig','globalcharport.portDest','globalcharcountry.countryOrig','globalcharcountry.countryDest');
                $surchName  =  $globalOfAr->surcharge->name;
                $surcharger = Surcharge::where('name',$surchName)->where('company_user_id',$company_user)->first();
                if(count($surcharger) >= 1){
                    $surcharger = $surcharger->id;
                } else {
                    $surcharger =  Surcharge::create([
                        'name'              => $surchName, 
                        'description'       => $surchName,
                        'company_user_id'   => $company_user
                    ]);
                    $surcharger = $surcharger->id;
                }

                $place = null;
                if(count($globalOfAr->globalcharport) >= 1){
                    $place = 'globalcharport';
                } elseif(count($globalOfAr->globalcharcountry) >= 1){
                    $place = 'globalcharcountry';                    
                }
                $global = GlobalCharge::where('validity',$globalOfAr->validity)
                    ->where('expire',$globalOfAr->expire)
                    ->where('surcharge_id',$surcharger)
                    ->where('calculationtype_id',$globalOfAr->calculationtype_id)
                    ->where('typedestiny_id',$globalOfAr->typedestiny_id)
                    ->where('currency_id',$globalOfAr->currency_id)
                    ->where('ammount',$globalOfAr->ammount)
                    ->where('company_user_id',$company_user)
                    ->has($place)
                    ->first();
                if(empty($global)){
                    $global = GlobalCharge::create([
                        'validity'          => $globalOfAr->validity, 
                        'expire'            => $globalOfAr->expire, 
                        'surcharge_id'      => $surcharger, 
                        'calculationtype_id'=> $globalOfAr->calculationtype_id, 
                        'typedestiny_id'    => $globalOfAr->typedestiny_id, 
                        'ammount'           => $globalOfAr->ammount, 
                        'currency_id'       => $globalOfAr->currency_id, 
                        'company_user_id'   => $company_user, 
                    ]);           
                }
                $global = $global->id;

                foreach($globalOfAr->globalcharcarrier->pluck('carrier_id') as $c )
                {
                    $countgbcarri = GlobalCharCarrier::where('carrier_id',$c)
                        ->where('globalcharge_id',$global)
                        ->get();
                    if(count($countgbcarri) == 0){
                        $detailcarrier = new GlobalCharCarrier();
                        $detailcarrier->carrier_id      = $c;
                        $detailcarrier->globalcharge_id = $global;
                        $detailcarrier->save();
                    }
                }
                if(count($globalOfAr->globalcharport) >= 1){
                    $detailport     = $globalOfAr->globalcharport->pluck('portOrig')->pluck('id');
                    $detailportDest = $globalOfAr->globalcharport->pluck('portDest')->pluck('id');
                    foreach($detailport as $p => $value)
                    {
                        foreach($detailportDest as $dest => $valuedest)
                        {
                            $countgbport = GlobalCharPort::where('port_orig',$value)
                                ->where('port_dest',$valuedest)
                                ->where('typedestiny_id',$globalOfAr->typedestiny_id)
                                ->where('globalcharge_id',$global)
                                ->get();
                            if(count($countgbport) == 0){
                                $ports                  = new GlobalCharPort();
                                $ports->port_orig       = $value;
                                $ports->port_dest       = $valuedest;
                                $ports->typedestiny_id  = $globalOfAr->typedestiny_id;
                                $ports->globalcharge_id = $global;
                                $ports->save();
                            }
                        }
                    }
                }elseif(count($globalOfAr->globalcharcountry) >= 1){
                    $detailCountrytOrig = $globalOfAr->globalcharcountry->pluck('countryOrig')->pluck('id');
                    $detailCountryDest  = $globalOfAr->globalcharcountry->pluck('countryDest')->pluck('id');
                    foreach($detailCountrytOrig as $p => $valueC)
                    {
                        foreach($detailCountryDest as $dest => $valuedestC)
                        {
                            $countgbcont = GlobalCharCountry::where('country_orig',$valueC)
                                ->where('country_dest',$valuedestC)
                                ->where('globalcharge_id',$global)
                                ->get();
                            if(count($countgbcont) == 0){
                                $detailcountry = new GlobalCharCountry();
                                $detailcountry->country_orig    = $valueC;
                                $detailcountry->country_dest    = $valuedestC;
                                $detailcountry->globalcharge_id = $global;
                                $detailcountry->save();
                            }
                        }
                    }
                }
            }
        } elseif(strnatcasecmp($selector,'lcl') == 0){
            $company_user = $request['company_user_id'];
            foreach($request['idArray'] as $gb){

                $globalOfAr = GlobalChargeLcl::find($gb);
                $globalOfAr->load('surcharge','globalcharcarrierslcl','globalcharportlcl.portOrig','globalcharportlcl.portDest','globalcharcountrylcl.countryOrig','globalcharcountrylcl.countryDest');
                $surchName  =  $globalOfAr->surcharge->name;
                $surcharger = Surcharge::where('name',$surchName)->where('company_user_id',$company_user)->first();
                if(count($surcharger) >= 1){
                    $surcharger = $surcharger->id;
                } else {
                    $surcharger =  Surcharge::create([
                        'name'              => $surchName, 
                        'description'       => $surchName,
                        'company_user_id'   => $company_user
                    ]);
                    $surcharger = $surcharger->id;
                }

                $place = null;
                if(count($globalOfAr->globalcharportlcl) >= 1){
                    $place = 'globalcharportlcl';
                } elseif(count($globalOfAr->globalcharcountrylcl) >= 1){
                    $place = 'globalcharcountrylcl';                    
                }

                $global = GlobalChargeLcl::where('validity',$globalOfAr->validity)
                    ->where('expire',$globalOfAr->expire)
                    ->where('surcharge_id',$surcharger)
                    ->where('calculationtypelcl_id',$globalOfAr->calculationtypelcl_id)
                    ->where('typedestiny_id',$globalOfAr->typedestiny_id)
                    ->where('currency_id',$globalOfAr->currency_id)
                    ->where('ammount',$globalOfAr->ammount)
                    ->where('minimum',$globalOfAr->minimum)
                    ->where('company_user_id',$company_user)
                    ->has($place)
                    ->first();
                if(empty($global)){
                    $global = GlobalChargeLcl::create([
                        'validity'              => $globalOfAr->validity, 
                        'expire'                => $globalOfAr->expire, 
                        'surcharge_id'          => $surcharger, 
                        'calculationtypelcl_id' => $globalOfAr->calculationtypelcl_id, 
                        'typedestiny_id'        => $globalOfAr->typedestiny_id, 
                        'ammount'               => $globalOfAr->ammount, 
                        'minimum'               => $globalOfAr->minimum, 
                        'currency_id'           => $globalOfAr->currency_id, 
                        'company_user_id'       => $company_user 
                    ]);            
                }
                $global = $global->id;

                foreach($globalOfAr->globalcharcarrierslcl->pluck('carrier_id') as $c ){
                    $countgbcarri = GlobalCharCarrierLcl::where('carrier_id',$c)
                        ->where('globalchargelcl_id',$global)
                        ->get();
                    if(count($countgbcarri) == 0){
                        $detailcarrier = new GlobalCharCarrierLcl();
                        $detailcarrier->carrier_id          = $c;
                        $detailcarrier->globalchargelcl_id  = $global;
                        $detailcarrier->save();
                    }
                }
                if(count($globalOfAr->globalcharportlcl) >= 1){
                    $detailport     = $globalOfAr->globalcharportlcl->pluck('portOrig')->pluck('id');
                    $detailportDest = $globalOfAr->globalcharportlcl->pluck('portDest')->pluck('id');
                    foreach($detailport as $p => $value){
                        foreach($detailportDest as $dest => $valuedest){
                            $countgbport = GlobalCharPortLcl::where('port_orig',$value)
                                ->where('port_dest',$valuedest)
                                ->where('globalchargelcl_id',$global)
                                ->get();
                            if(count($countgbport) == 0){
                                $ports                      = new GlobalCharPortLcl();
                                $ports->port_orig           = $value;
                                $ports->port_dest           = $valuedest;
                                $ports->globalchargelcl_id  = $global;
                                $ports->save();
                            }
                        }
                    }
                }elseif(count($globalOfAr->globalcharcountrylcl) >= 1){
                    $detailCountrytOrig = $globalOfAr->globalcharcountrylcl->pluck('countryOrig')->pluck('id');
                    $detailCountryDest  = $globalOfAr->globalcharcountrylcl->pluck('countryDest')->pluck('id');
                    foreach($detailCountrytOrig as $p => $valueC){
                        foreach($detailCountryDest as $dest => $valuedestC){
                            $countgbcont = GlobalCharCountryLcl::where('country_orig',$valueC)
                                ->where('country_dest',$valuedestC)
                                ->where('globalchargelcl_id',$global)
                                ->get();
                                if(count($countgbcont) == 0){
                                    $detailcountry = new GlobalCharCountryLcl();
                                    $detailcountry->country_orig        = $valueC;
                                    $detailcountry->country_dest        = $valuedestC;
                                    $detailcountry->globalchargelcl_id  = $global;
                                    $detailcountry->save();
                                }
                        }
                    }
                }
            }
        }
    }
}
