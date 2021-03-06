<?php

namespace App\Jobs;

use App\CalculationType;
use App\Carrier;
use App\Contract;
use App\Currency;
use App\FailSurCharge;
use App\Harbor;
use App\LocalCharCarrier;
use App\LocalCharCountry;
use App\LocalCharge;
use App\LocalCharPort;
use App\Notifications\N_general;
use App\Surcharge;
use App\TypeDestiny;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PrvCarrier;
use PrvHarbor;

class ReprocessSurchargersJob implements ShouldQueue
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
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $id = $this->id;
        $failsurchargers = FailSurCharge::where('contract_id', '=', $id)->get();
        foreach ($failsurchargers as $FailSurchager) {
            $surchargerEX = '';
            $origenEX = '';
            $destinyEX = '';
            $typedestinyEX = '';
            $calculationtypeEX = '';
            $ammountEX = '';
            $currencyEX = '';
            $carrierEX = '';
            $originResul = '';
            $originExits = '';
            $originV = '';
            $destinResul = '';
            $destinationExits = '';
            $destinationV = '';
            $surchargerV = '';
            $typedestunyV = '';
            $calculationtypeV = '';
            $amountV = '';
            $currencyV = '';
            $carrierV = '';

            $carrierB = false;
            $calculationtypeB = false;
            $typedestinyB = false;
            $originB = false;
            $destinyB = false;
            $surcharB = false;
            $currencyB = false;

            $surchargerEX = explode('_', trim($FailSurchager['surcharge_id']));
            $originEX = explode('_', trim($FailSurchager['port_orig']));
            $destinyEX = explode('_', trim($FailSurchager['port_dest']));
            $typedestinyEX = explode('_', trim($FailSurchager['typedestiny_id']));
            $calculationtypeEX = explode('_', trim($FailSurchager['calculationtype_id']));
            $ammountEX = explode('_', trim($FailSurchager['ammount']));
            $currencyEX = explode('_', trim($FailSurchager['currency_id']));
            $carrierEX = explode('_', trim($FailSurchager['carrier_id']));

            if (count($surchargerEX) <= 1 && count($typedestinyEX) <= 1
                   && count($typedestinyEX) <= 1 && count($calculationtypeEX) <= 1
                   && count($ammountEX) <= 1 && count($currencyEX) <= 1) {

                    // Origen Y Destino ------------------------------------------------------------------------

                if ($FailSurchager->differentiator == 1) {
                    $resultadoPortOri = PrvHarbor::get_harbor($originEX[0]);
                    $originV = $resultadoPortOri['puerto'];
                } elseif ($FailSurchager->differentiator == 2) {
                    $resultadoPortOri = PrvHarbor::get_country($originEX[0]);
                    $originV = $resultadoPortOri['country'];
                }
                if ($resultadoPortOri['boolean']) {
                    $originB = true;
                }

                if ($FailSurchager->differentiator == 1) {
                    $resultadoPortDes = PrvHarbor::get_harbor($destinyEX[0]);
                    $destinationV = $resultadoPortDes['puerto'];
                } elseif ($FailSurchager->differentiator == 2) {
                    $resultadoPortDes = PrvHarbor::get_country($destinyEX[0]);
                    $destinationV = $resultadoPortDes['country'];
                }
                if ($resultadoPortDes['boolean']) {
                    $destinyB = true;
                }

                //  Surcharge ------------------------------------------------------------------------------

                $surchargerV = Surcharge::where('name', '=', $surchargerEX[0])->first();
                if (count((array)$surchargerV) == 1) {
                    $surcharB = true;
                    $surchargerV = $surchargerV['id'];
                }

                //  Type Destiny ---------------------------------------------------------------------------

                $typedestunyV = TypeDestiny::where('description', '=', $typedestinyEX[0])->first();
                if (count((array)$typedestunyV) == 1) {
                    $typedestinyB = true;
                    $typedestunyV = $typedestunyV['id'];
                }

                //  Calculation Type -----------------------------------------------------------------------

                $calculationtypeV = CalculationType::where('code', '=', $calculationtypeEX[0])->orWhere('name', '=', $calculationtypeEX[0])->first();

                if (count((array)$calculationtypeV) == 1) {
                    $calculationtypeV = $calculationtypeV['id'];
                }

                $calculationtypeB = true;
                //  Amount ---------------------------------------------------------------------------------

                $amountV = floatval($ammountEX[0]);

                //  Currency -------------------------------------------------------------------------------

                $currencyV = Currency::where('alphacode', '=', $currencyEX[0])->first();
                if (count((array)$currencyV) == 1) {
                    $currencyB = true;
                    $currencyV = $currencyV['id'];
                }

                //  Carrier -------------------------------------------------------------------------------
                $carrierArr = PrvCarrier::get_carrier($carrierEX[0]);
                $carrierB = $carrierArr['boolean'];
                $carrierV = $carrierArr['carrier'];

                /*$colleccion = collect([]);
                $colleccion = [
                    'origen'            =>  $originV,
                    'destiny'           =>  $destinationV,
                    'surcharge'         =>  $surchargerV,
                    'typedestuny'       =>  $typedestunyV,
                    'calculationtypeV'  =>  $calculationtypeV,
                    'amountV'           =>  $amountV,
                    'currencyV'         =>  $currencyV,
                    'carrierV'          =>  $carrierV,
                    'relation'          =>  $carrierArr['relation'],
                ];

                dd($colleccion);*/

                if ($originB == true && $destinyB == true
                       && $surcharB == true && $typedestinyB == true
                       && $calculationtypeB == true && $currencyB == true
                       && $carrierB == true) {
                    $LocalchargeId = null;
                    $LocalchargeId = LocalCharge::where('surcharge_id', $surchargerV)
                            ->where('typedestiny_id', $typedestunyV)
                            ->where('contract_id', $id)
                            ->where('calculationtype_id', $calculationtypeV)
                            ->where('ammount', $amountV)
                            ->where('currency_id', $currencyV)
                            ->first();

                    if (count((array)$LocalchargeId) == 0) {
                        $LocalchargeId = LocalCharge::create([
                                'surcharge_id'          => $surchargerV,
                                'typedestiny_id'        => $typedestunyV,
                                'contract_id'           => $id,
                                'calculationtype_id'    => $calculationtypeV,
                                'ammount'               => $amountV,
                                'currency_id'           => $currencyV,
                            ]);
                    }

                    $LocalchargeId = $LocalchargeId->id;

                    $existCa = null;
                    $existCa = LocalCharCarrier::where('carrier_id', $carrierV)
                            ->where('localcharge_id', $LocalchargeId)->first();
                    if (count((array)$existCa) == 0) {
                        LocalCharCarrier::create([
                                'carrier_id'     => $carrierV,
                                'localcharge_id' => $LocalchargeId,
                            ]);
                    }

                    if ($FailSurchager->differentiator == 1) {
                        $existsP = null;
                        $existsP = LocalCharPort::where('port_orig', $originV)
                                ->where('port_dest', $destinationV)
                                ->where('localcharge_id', $LocalchargeId)
                                ->first();
                        if (count((array)$existsP) == 0) {
                            LocalCharPort::create([
                                    'port_orig'         => $originV,
                                    'port_dest'         => $destinationV,
                                    'localcharge_id'    => $LocalchargeId,
                                ]);
                        }
                    } elseif ($FailSurchager->differentiator == 2) {
                        $existsC = null;
                        $existsC = LocalCharCountry::where('country_orig', $originV)
                                ->where('country_dest', $destinationV)
                                ->where('localcharge_id', $LocalchargeId)
                                ->first();
                        if (count((array)$existsC) == 0) {
                            LocalCharCountry::create([
                                    'country_orig'      => $originV,
                                    'country_dest'      => $destinationV,
                                    'localcharge_id'    => $LocalchargeId,
                                ]);
                        }
                    }

                    $FailSurchager->forceDelete();
                }
            }
        }

        $contractData = Contract::find($id);
        $usersNotifiques = User::where('type', '=', 'admin')->get();
        foreach ($usersNotifiques as $userNotifique) {
            $message = 'The Surchargers was Reprocessed. Contract: '.$contractData->number;
            $userNotifique->notify(new N_general($userNotifique, $message));
        }
    }
}
