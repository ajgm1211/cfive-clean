<?php

namespace App\Http\Controllers;

use App\AccountImportationContractFcl as AccountFcl;
use App\CalculationType;
use App\CalculationTypeContent;
use App\Carrier;
use App\Company;
use App\CompanyUser;
use App\Contact;
use App\OverweightRange;
use App\FailOverweightRange;
use App\Container;
use App\EndpointTable;
use App\ContainerCalculation;
use App\Contract;
use App\ContractCarrier;
use App\Country;
use App\Currency;
use App\Direction;
use App\Failcompany;
use App\Failedcontact;
use App\FailRate;
use App\FailSurCharge;
use App\GroupContainer;
use App\Harbor;
use App\Jobs\GeneralJob;
use App\Jobs\ImportationRatesSurchargerJob;
use App\Jobs\ReprocessRatesJob;
use App\Jobs\ReprocessSurchargersJob;
use App\Jobs\ValidateTemplateJob;
use App\Jobs\ValidatorSurchargeJob;
use App\LocalCharCarrier;
use App\LocalCharCountry;
use App\LocalCharge;
use App\LocalCharPort;
use App\MasterSurcharge;
use App\MyClass\Excell\MyReadFilter;
use App\NewContractRequest;
use App\BehaviourPerContainer;
use App\Helpers\HelperAll as HelpersHelperAll;
use App\NewContractRequest as RequestFcl;
use App\Notifications\N_general;
use App\Rate;
use App\Region;
use App\ScheduleType;
use App\Surcharge;
use App\TypeDestiny;
use App\User;
use Carbon\Carbon;
use Excel;
use HelperAll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PrvCarrier;
use PrvHarbor;
use PrvRates;
use PrvSurchargers;
use PrvValidation;
use Yajra\Datatables\Datatables;
use App\MyClass\Excell\ChunkReadFilter;

class ImportationController extends Controller
{
    public function ReprocesarRates(Request $request, $id)
    {
        $countfailrates = FailRate::where('contract_id', '=', $id)->count();
        if ($countfailrates <= 150) {
            $failrates = FailRate::where('contract_id', '=', $id)->get();
            foreach ($failrates as $failrate) {
                $carrierEX = '';
                $twuentyEX = '';
                $fortyEX = '';
                $fortyhcEX = '';
                $currencyEX = '';
                $originResul = '';
                $originExits = '';
                $originV = '';
                $destinResul = '';
                $destinationExits = '';
                $destinationV = '';
                $originEX = '';
                $destinyEX = '';
                $twentyVal = '';
                $fortyVal = '';
                $fortyhcVal = '';
                $carrierVal = '';
                $carrierArr = '';
                $twentyArr = '';
                $fortyArr = '';
                $fortyhcArr = '';
                $currencyArr = '';
                $currencyVal = '';
                $currenct = '';
                $fortynorVal = '';
                $fortyfiveVal = '';
                $scheduleTVal = null;
                $containers = null;

                $curreExitBol = false;
                $originB = false;
                $destinyB = false;
                $carriExitBol = false;
                $scheduleTBol = false;
                $containersBol = false;

                $originEX = explode('_', trim($failrate->origin_port));
                $destinyEX = explode('_', trim($failrate->destiny_port));
                $carrierArr = explode('_', trim($failrate->carrier_id));
                $twentyArr = explode('_', trim($failrate->twuenty));
                $fortyArr = explode('_', trim($failrate->forty));
                $fortyhcArr = explode('_', trim($failrate->fortyhc));
                $fortynorArr = explode('_', trim($failrate->fortynor));
                $fortyfiveArr = explode('_', trim($failrate->fortyfive));
                $currencyArr = explode('_', trim($failrate->currency_id));
                $scheduleTArr = explode('_', trim($failrate->schedule_type));
                $containers = json_decode($failrate->containers, true);
                if (!empty($containers)) {
                    foreach ($containers as $containerEq) {
                        if (count(explode('_', $containerEq)) > 1) {
                            $containersBol = true;
                            break;
                        }
                    }
                }

                $carrierEX = count($carrierArr);
                $twuentyEX = count($twentyArr);
                $fortyEX = count($fortyArr);
                $fortyhcEX = count($fortyhcArr);
                $currencyEX = count($currencyArr);

                $caracteres = ['*', '/', '.', '?', '"', 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, '{', '}', '[', ']', '+', '_', '|', '°', '!', '$', '%', '&', '(', ')', '=', '¿', '¡', ';', '>', '<', '^', '`', '¨', '~', ':'];
                if (
                    $twuentyEX <= 1 &&
                    $fortyEX <= 1 && $fortyhcEX <= 1 &&
                    $currencyEX <= 1 && $containersBol == false
                ) {
                    $resultadoPortOri = PrvHarbor::get_harbor($originEX[0]);
                    if ($resultadoPortOri['boolean']) {
                        $originB = true;
                    }
                    $originV = $resultadoPortOri['puerto'];

                    $resultadoPortDes = PrvHarbor::get_harbor($destinyEX[0]);
                    if ($resultadoPortDes['boolean']) {
                        $destinyB = true;
                    }
                    $destinationV = $resultadoPortDes['puerto'];

                    //---------------- Carrier ------------------------------------------------------------------

                    $carrierArr = PrvCarrier::get_carrier($carrierArr[0]);
                    $carriExitBol = $carrierArr['boolean'];
                    $carrierVal = $carrierArr['carrier'];

                    //---------------- Containers -----------------------------------------------------------
                    $colec = [];
                    if (!empty($containers)) {
                        foreach ($containers as $key => $containerEq) {
                            $colec[$key] = '' . floatval($containerEq);
                        }
                    }
                    $containers = json_encode($colec);
                    //---------------- 20' ------------------------------------------------------------------

                    $twentyVal = floatval($twentyArr[0]);

                    //----------------- 40' -----------------------------------------------------------------

                    $fortyVal = floatval($fortyArr[0]);

                    //----------------- 40'HC --------------------------------------------------------------

                    $fortyhcVal = floatval($fortyhcArr[0]);

                    //----------------- 40'NOR -------------------------------------------------------------

                    $fortynorVal = floatval($fortynorArr[0]);

                    //----------------- 45' ----------------------------------------------------------------

                    $fortyfiveVal = floatval($fortyfiveArr[0]);

                    //----------------- Currency -----------------------------------------------------------

                    $currenct = Currency::where('alphacode', '=', $currencyArr[0])->orWhere('id', '=', $currencyArr[0])->first();

                    if (empty($currenct->id) != true) {
                        $curreExitBol = true;
                        $currencyVal = $currenct->id;
                    }

                    $scheduleT = ScheduleType::where('name', '=', $scheduleTArr[0])->first();

                    if (empty($scheduleT->id) != true || $scheduleTArr[0] == null) {
                        $scheduleTBol = true;
                        if ($scheduleTArr[0] != null) {
                            $scheduleTVal = $scheduleT->id;
                        } else {
                            $scheduleTVal = null;
                        }
                    }

                    $array = [
                        'ori' => $originB,
                        'des' => $destinyB,
                        'containers' => $containers,
                        'sch' => $scheduleTBol,
                        'car' => $carriExitBol,
                        'curr' => $curreExitBol,
                    ];
                    //dd($array);

                    // Validacion de los datos en buen estado ------------------------------------------------------------------------
                    if (
                        $originB == true && $destinyB == true &&
                        $scheduleTBol == true && $curreExitBol == true && $carriExitBol == true
                    ) {
                        $collecciont = '';
                        $exists = [];
                        $exists = Rate::where('origin_port', $originV)
                            ->where('destiny_port', $destinationV)
                            ->where('carrier_id', $carrierVal)
                            ->where('contract_id', $id)
                            ->where('twuenty', $twentyVal)
                            ->where('forty', $fortyVal)
                            ->where('fortyhc', $fortyhcVal)
                            ->where('fortynor', $fortynorVal)
                            ->where('fortyfive', $fortyfiveVal)
                            ->where('containers', $containers)
                            ->where('currency_id', $currencyVal)
                            ->where('schedule_type_id', $scheduleTVal)
                            ->where('transit_time', (int) $failrate['transit_time'])
                            ->where('via', $failrate['via'])
                            ->first();
                        if (count((array) $exists) == 0) {
                            $collecciont = Rate::create([
                                'origin_port' => $originV,
                                'destiny_port' => $destinationV,
                                'carrier_id' => $carrierVal,
                                'contract_id' => $id,
                                'twuenty' => $twentyVal,
                                'forty' => $fortyVal,
                                'fortyhc' => $fortyhcVal,
                                'fortynor' => $fortynorVal,
                                'fortyfive' => $fortyfiveVal,
                                'containers' => $containers,
                                'currency_id' => $currencyVal,
                                'schedule_type_id' => $scheduleTVal,
                                'transit_time' => (int) $failrate['transit_time'],
                                'via' => $failrate['via'],
                            ]);
                        }
                        $failrate->forceDelete();
                    }
                }
            }
            $contractData = Contract::find($id);
            $usersNotifiques = User::where('type', '=', 'admin')->get();
            foreach ($usersNotifiques as $userNotifique) {
                $message = 'The Rates was Reprocessed. Contract: ' . $contractData->name;
                $userNotifique->notify(new N_general($userNotifique, $message));
            }
        } else {
            if (env('APP_VIEW') == 'operaciones') {
                ReprocessRatesJob::dispatch($id)->onQueue('operaciones');
            } else {
                ReprocessRatesJob::dispatch($id);
            }
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.content', 'The rates are reprocessing in the background');

            return redirect()->route('Failed.Developer.For.Contracts', [$id, 0]);
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'The rates are being reprocessed');

        return redirect()->route('Failed.Developer.For.Contracts', [$id, 0]);
    }

    public function ReprocesarSurchargers(Request $request, $id)
    {
        $countfailsurchargers = FailSurCharge::where('contract_id', '=', $id)->count();
        if ($countfailsurchargers <= 150) {
            $failsurchargers = FailSurCharge::with('fail_overweight_ranges')->where('contract_id', '=', $id)->get();
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
                $lowerlimitB = false;
                $upperlimitB = false;
                $lower_limit = 0;
                $upper_limit = 0;

                $surchargerEX = explode('_', trim($FailSurchager['surcharge_id']));
                $originEX = explode('_', trim($FailSurchager['port_orig']));
                $destinyEX = explode('_', trim($FailSurchager['port_dest']));
                $typedestinyEX = explode('_', trim($FailSurchager['typedestiny_id']));
                $calculationtypeEX = explode('_', trim($FailSurchager['calculationtype_id']));
                $ammountEX = explode('_', trim($FailSurchager['ammount']));
                $currencyEX = explode('_', trim($FailSurchager['currency_id']));
                $carrierEX = explode('_', trim($FailSurchager['carrier_id']));
                $is_ow_range = !$FailSurchager->fail_overweight_ranges->isEmpty();
                if ($is_ow_range) {
                    $limits = $FailSurchager->fail_overweight_ranges->first();
                    $upper_limitEx = explode('_', trim($limits['upper_limit']));
                    $lower_limitEx = explode('_', trim($limits['lower_limit']));
                    if (count($upper_limitEx) <= 1) {
                        $upperlimitB = true;
                        $upper_limit = (!empty($upper_limitEx[0])) ? $upper_limitEx[0] : null;
                    }
                    if (count($lower_limitEx) <= 1) {
                        $lowerlimitB = true;
                        $lower_limit = (!empty($lower_limitEx[0])) ? $lower_limitEx[0] : null;
                    }
                } else {
                    $lowerlimitB = true;
                    $upperlimitB = true;
                }
                //dd($lowerlimitB,$upperlimitB,$upper_limit,$lower_limit);
                if (
                    count($surchargerEX) <= 1 && count($typedestinyEX) <= 1
                    && count($typedestinyEX) <= 1 && count($calculationtypeEX) <= 1
                    && count($ammountEX) <= 1 && count($currencyEX) <= 1
                ) {

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

                    $surchargerV = Surcharge::where('name', '=', $surchargerEX[0])->get();
                    if (!$surchargerV->isEmpty()) {
                        $surcharB = true;
                        $surchargerV = $surchargerV[0]['id'];
                    }

                    //  Type Destiny ---------------------------------------------------------------------------

                    $typedestunyV = TypeDestiny::where('description', '=', $typedestinyEX[0])->get();
                    if (!$typedestunyV->isEmpty()) {
                        $typedestinyB = true;
                        $typedestunyV = $typedestunyV[0]['id'];
                    }

                    //  Calculation Type -----------------------------------------------------------------------

                    $calculationtypeV = CalculationType::where('code', '=', $calculationtypeEX[0])->orWhere('name', '=', $calculationtypeEX[0])->get();

                    if (!$calculationtypeV->isEmpty()) {
                        $calculationtypeV = $calculationtypeV[0]['id'];
                        $calculationtypeB = true;
                    }

                    //  Amount ---------------------------------------------------------------------------------

                    $amountV = floatval($ammountEX[0]);

                    //  Currency -------------------------------------------------------------------------------

                    $currencyV = Currency::where('alphacode', '=', $currencyEX[0])->get();
                    if (!$currencyV->isEmpty()) {
                        $currencyB = true;
                        $currencyV = $currencyV[0]['id'];
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
                    'lowerlimitB'       =>  $lowerlimitB,
                    'upperlimitB'       =>  $upperlimitB,
                    'upper_limit'       =>  $upper_limit,
                    'lower_limit'       =>  $lower_limit
                    ];*/

                    //dd($originB,$destinyB,$surcharB,$typedestinyB,$calculationtypeB,$currencyB,$lowerlimitB,$upperlimitB,$carrierB);

                    if (
                        $originB == true && $destinyB == true
                        && $surcharB == true && $typedestinyB == true
                        && $calculationtypeB == true && $currencyB == true
                        && $lowerlimitB == true && $upperlimitB == true
                        && $carrierB == true
                    ) {
                        $LocalchargeId = null;
                        $LocalchargeId = LocalCharge::where('surcharge_id', $surchargerV)
                            ->where('typedestiny_id', $typedestunyV)
                            ->where('contract_id', $id)
                            ->where('calculationtype_id', $calculationtypeV)
                            ->where('ammount', $amountV)
                            ->where('currency_id', $currencyV);

                        if ($is_ow_range) {
                            $LocalchargeId->whereHas('overweight_ranges', function ($query) use ($upper_limit, $lower_limit, $amountV) {
                                $query->where('lower_limit', $lower_limit)
                                    ->where('upper_limit', $upper_limit)
                                    ->where('amount', $amountV)
                                    ->where('model_type', 'App\\LocalCharge');
                            });
                        }
                        $LocalchargeId = $LocalchargeId->get();

                        if ($LocalchargeId->isEmpty()) {
                            $LocalchargeId = LocalCharge::create([
                                'surcharge_id'          => $surchargerV,
                                'typedestiny_id'        => $typedestunyV,
                                'contract_id'           => $id,
                                'calculationtype_id'    => $calculationtypeV,
                                'ammount'               => $amountV,
                                'currency_id'           => $currencyV,
                            ]);
                            OverweightRange::create([
                                'lower_limit' => $lower_limit,
                                'upper_limit' => $upper_limit,
                                'amount' => $amountV,
                                'model_id' => $LocalchargeId->id,
                                'model_type' => 'App\\LocalCharge',
                            ]);
                        } else {
                            $LocalchargeId = $LocalchargeId->first();
                        }

                        $LocalchargeId = $LocalchargeId->id;

                        $existCa = null;
                        $existCa = LocalCharCarrier::where('carrier_id', $carrierV)
                            ->where('localcharge_id', $LocalchargeId)->first();
                        if (count((array) $existCa) == 0) {
                            LocalCharCarrier::create([
                                'carrier_id' => $carrierV,
                                'localcharge_id' => $LocalchargeId,
                            ]);
                        }

                        if ($FailSurchager->differentiator == 1) {
                            $existsP = null;
                            $existsP = LocalCharPort::where('port_orig', $originV)
                                ->where('port_dest', $destinationV)
                                ->where('localcharge_id', $LocalchargeId)
                                ->first();
                            if (count((array) $existsP) == 0) {
                                LocalCharPort::create([
                                    'port_orig' => $originV,
                                    'port_dest' => $destinationV,
                                    'localcharge_id' => $LocalchargeId,
                                ]);
                            }
                        } elseif ($FailSurchager->differentiator == 2) {
                            $existsC = null;
                            $existsC = LocalCharCountry::where('country_orig', $originV)
                                ->where('country_dest', $destinationV)
                                ->where('localcharge_id', $LocalchargeId)
                                ->first();
                            if (count((array) $existsC) == 0) {
                                LocalCharCountry::create([
                                    'country_orig' => $originV,
                                    'country_dest' => $destinationV,
                                    'localcharge_id' => $LocalchargeId,
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
                $message = 'The Surchargers was Reprocessed. Contract: ' . $contractData->number;
                $userNotifique->notify(new N_general($userNotifique, $message));
            }
        } else {
            if (env('APP_VIEW') == 'operaciones') {
                ReprocessSurchargersJob::dispatch($id)->onQueue('operaciones');
            } else {
                ReprocessSurchargersJob::dispatch($id);
            }
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.content', 'The Surchargers are reprocessing in the background');

            return redirect()->route('Failed.Developer.For.Contracts', [$id, 'FailSurcharge']);
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'The Surchargers are being reprocessed');
        $countfailSurChargersNew = FailSurCharge::where('contract_id', '=', $id)->count();

        if ($countfailSurChargersNew > 0) {
            //1
            return redirect()->route('Failed.Developer.For.Contracts', [$id, 'FailSurcharge']);
        } else {
            return redirect()->route('Failed.Developer.For.Contracts', [$id, 'GoodSurcharge']);
        }
    }

    // precarga la vista para importar rates mas surchargers desde Request

    public function requestProccess($id, $selector, $request_id)
    {
        $load_carrier = false;
        $carrier_exec = Carrier::where('name', 'ALL')->first();
        $carrier_exec = $carrier_exec->id;
        $equiment = ['id' => null, 'name' => null, 'color' => null];
        $api_contract = [];
        $json_rq = null;

        if ($selector == 1) {
            $requestfcl = RequestFcl::find($id);
            @$requestfcl->load('Requestcarriers');
            if (json_decode($requestfcl->data, true) != null) {
                $json_rq = json_decode($requestfcl->data, true);
                if (!empty($json_rq['group_containers'])) {
                    $equiment['id'] = $json_rq['group_containers']['id'];
                    $equiment['name'] = $json_rq['group_containers']['name'];
                    $api_contract['code'] = $json_rq['contract']['code'] ?? null;
                    $api_contract['is_api'] = $json_rq['contract']['is_api'] ?? 0;
                    $api_contract['user_id'] = $json_rq['contract']['user_id'] ?? null;
                    $groupContainer = GroupContainer::find($equiment['id']);
                    $json_rq = json_decode($groupContainer->data, true);
                    $equiment['color'] = $json_rq['color'];
                }
            } else {
                $groupContainer = GroupContainer::find(1);
                $json_rq = json_decode($groupContainer->data, true);
                $equiment['id'] = $groupContainer->id;
                $equiment['name'] = $groupContainer->name;
                $equiment['color'] = $json_rq['color'];
            }
            //dd($requestfcl,$equiment);
            if (count($requestfcl->Requestcarriers) == 1) {
                foreach ($requestfcl->Requestcarriers as $carrier_uniq) {
                    if ($carrier_uniq->id != $carrier_exec) {
                        $load_carrier = true;
                    }
                }
            }
        } elseif ($selector == 2) {
            $contract = Contract::find($id);
            if (isset($contract)) {
                @$contract->load('carriers');
                if (!empty($contract->gp_container_id)) {
                    $groupContainer = GroupContainer::find($contract->gp_container_id);
                    $json_rq = json_decode($groupContainer->data, true);
                    $equiment['id'] = $groupContainer->id;
                    $equiment['name'] = $groupContainer->name;
                    $equiment['color'] = $json_rq['color'];
                } else {
                    $groupContainer = GroupContainer::find(1);
                    $json_rq = json_decode($groupContainer->data, true);
                    $equiment['id'] = $groupContainer->id;
                    $equiment['name'] = $groupContainer->name;
                    $equiment['color'] = $json_rq['color'];
                }
                //dd($contract,$equiment);
                if (count($contract->carriers) == 1) {
                    foreach ($contract->carriers as $carrier_uniq) {
                        if ($carrier_uniq->id != $carrier_exec) {
                            $load_carrier = true;
                        }
                    }
                }
                $api_contract['user_id'] = $contract->user_id;
            } else {
                return redirect()->route('RequestFcl.index');
            }
        }

        // dd($equiment);

        $harbor = harbor::pluck('display_name', 'id');
        $country = Country::pluck('name', 'id');
        $region = Region::pluck('name', 'id');
        $carrier = carrier::pluck('name', 'id');
        $coins = currency::pluck('alphacode', 'id');
        $currency = currency::where('alphacode', 'USD')->pluck('id');
        $direction = Direction::pluck('name', 'id');
        $companysUser = CompanyUser::all()->pluck('name', 'id');
        $typedestiny = TypeDestiny::all()->pluck('description', 'id');
        if ($selector == 1) {
            return view('importationV2.Fcl.newImport', compact('harbor', 'direction', 'country', 'region', 'carrier', 'companysUser', 'typedestiny', 'requestfcl', 'selector', 'load_carrier', 'coins', 'currency', 'equiment', 'api_contract'));

            //            return view('importation.ImportContractFCLRequest',compact('harbor','direction','country','region','carrier','companysUser','typedestiny','requestfcl','selector','load_carrier'));
        } elseif ($selector == 2) {
            return view('importationV2.Fcl.newImport', compact('harbor', 'direction', 'country', 'region', 'carrier', 'companysUser', 'typedestiny', 'contract', 'selector', 'request_id', 'load_carrier', 'coins', 'currency', 'equiment', 'api_contract'));

            //            return view('importation.ImportContractFCLRequest',compact('harbor','direction','country','region','carrier','companysUser','typedestiny','contract','selector','request_id','load_carrier'));
        }
    }

    // carga el archivo excel y verifica la cabecera para mostrar la vista con las columnas:
    public function UploadFileNewContract(Request $request)
    {
        //dd($request->all());

        $now = new \DateTime();
        $now2 = $now;
        $now = $now->format('dmY_His');
        $now2 = $now2->format('Y-m-d');
        $datTypeDes = false;
        $name = $request->name;
        $CompanyUserId = $request->CompanyUserId;
        $request_id = $request->request_id;
        $contract_id = $request->contract_id;
        $selector = $request->selector;
        $dataCarrier = $request->DatCar;
        $carrierVal = $request->carrier;
        $datTypeDes = $request->DatTypeDes;
        $typedestinyVal = $request->typedestiny;
        $chargeVal = $request->chargeVal;
        $gp_container_id = $request->gp_container_id;
        $contract_code = $request->contract_code;
        $contract_is_api = $request->contract_is_api;
        $contract_owner = $request->contract_owner;
        $validity = explode('/', $request->validation_expire);

        $statustypecurren = $request->valuesCurrency;
        $currency = $request->currency;
        $statusPortCountry = $request->valuesportcountry;
        $direction_id = $request->direction;
        $file = $request->input('document');

        $carrierBol = false;
        $PortCountryRegionBol = false;
        $typedestinyBol = false;
        $filebool = false;
        $data = collect([]);

        //$contract_id            = 45;

        if (!empty($file)) {
            $account = new AccountFcl();
            $account->name = $name;
            $account->date = $now2;
            $account->company_user_id = $CompanyUserId;
            $account->request_id = $request_id;
            $account->save();

            $account->addMedia(storage_path('tmp/importation/fcl/' . $file))->toMediaCollection('document', 'FclAccount');

            if ($selector == 2) {
                $contract = Contract::find($contract_id);
                $contract->account_id = $account->id;
                $contract->update();
            } else {

                $contract = new Contract();
                $contract->name = $request->name;

                $contract->validity = $validity[0];
                $contract->expire = $validity[1];
                $contract->direction_id = $direction_id;
                $contract->status = 'incomplete';
                $contract->company_user_id = $CompanyUserId;
                $contract->account_id = $account->id;
                $contract->gp_container_id = $gp_container_id;
                $contract->code = $contract_code;
                $contract->is_api = $contract_is_api;
                $contract->user_id = $contract_owner;
                $contract->save();

                //Adding custom code to contract
                $contract->createCustomCode();

                foreach ($request->carrierM as $carrierVal) {
                    ContractCarrier::create([
                        'carrier_id' => $carrierVal,
                        'contract_id' => $contract->id,
                    ]);
                }
            }
            $contract->load('carriers');
            $contract_id = $contract->id;

            if (!empty($request_id)) {
                $requestFile = NewContractRequest::find($request_id);
                if (!empty($requestFile->id)) {
                    if (empty($requestFile->contract_id)) {
                        $requestFile->contract_id = $contract_id;
                        $requestFile->update();
                    }
                }
            }
            //dd($account,$contract,$requestFile);
        } else {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'Error File!!');

            return back();
        }

        $requestCont = NewContractRequest::find($request_id);
        $data = json_decode($requestCont->data);
        $columnsSelected = collect(['ORIGIN', 'DESTINY', 'CHARGE', 'CALCULATION TYPE']);
        //dd($data);

        //$account    = AccountFcl::find(29);

        $valuesSelecteds = collect([
            'company_user_id' => $CompanyUserId,
            'request_id' => $request_id,
            'selector' => $selector,
            'chargeVal' => $chargeVal,
            'contract_id' => $contract_id,
            'acount_id' => $account->id,
        ]);

        $request_columns = [];
        foreach ($data->containers as $dataContainers) {
            $columnsSelected->push($dataContainers->code);
            array_push($request_columns, $dataContainers->code);
        }

        $valuesSelecteds->put('group_container_id', $data->group_containers->id);
        $valuesSelecteds->put('request_columns', $request_columns);

        // ------- TYPE DESTINY -------------------

        if ($datTypeDes) {
            $typedestinyBol = true;
            $valuesSelecteds->put('typeDestinyVal', $typedestinyVal);
            $valuesSelecteds->put('select_typeDestiny', $typedestinyBol);
        } else {
            $columnsSelected->push('TYPE DESTINY');
            $valuesSelecteds->put('select_typeDestiny', $typedestinyBol);
        }

        // ------- CURRENCY -----------------------
        if ($statustypecurren == 1) {
            $columnsSelected->push('CURRENCY');
            $valuesSelecteds->put('select_currency', 1);
        } elseif ($statustypecurren == 2) {
            $valuesSelecteds->put('select_currency', 2);
        } elseif ($statustypecurren == 3) {
            $valuesSelecteds->put('select_currency', 3);
            $valuesSelecteds->put('currencyVal', $currency);
        }

        // ------- CARRIER ------------------------
        if ($dataCarrier == false) {
            $columnsSelected->push('CARRIER');
            $valuesSelecteds->put('select_carrier', $carrierBol);
        } else {
            $carrierBol = true;
            $valuesSelecteds->put('carrierVal', $carrierVal);
            $valuesSelecteds->put('select_carrier', $carrierBol);
        }

        // ------- PUERTO/COUNTRY/REGION ----------

        if ($statusPortCountry == 2) {
            $PortCountryRegionBol = true;
            $columnsSelected->push('DIFFERENTIATOR');
            $valuesSelecteds->put('select_portCountryRegion', $PortCountryRegionBol);
        } else {
            $valuesSelecteds->put('select_portCountryRegion', $PortCountryRegionBol);
        }

        $columnsSelected->push('LIMITS');
        $mediaItem = $account->getFirstMedia('document');
        $excel = Storage::disk('FclAccount')->get($mediaItem->id . '/' . $mediaItem->file_name);
        Storage::disk('FclImport')->put($mediaItem->file_name, $excel);
        $excelF = Storage::disk('FclImport')->url($mediaItem->file_name);

        $extObj = new \SplFileInfo($mediaItem->file_name);
        $ext = $extObj->getExtension();
        if (strnatcasecmp($ext, 'xlsx') == 0) {
            $inputFileType = 'Xlsx';
        } elseif (strnatcasecmp($ext, 'xls') == 0) {
            $inputFileType = 'Xls';
        } else {
            $inputFileType = 'Csv';
        }

        $firstRow = new MyReadFilter(1, 1);
        $reader = IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $reader->setReadFilter($firstRow);
        $spreadsheet = $reader->load($excelF);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        //$sheetData = $spreadsheet->getActiveSheet()->toArray(null,true,true,true);
        //dd($sheetData);
        Storage::disk('FclImport')->Delete($mediaItem->file_name);
        $final_columns = collect([]);
        foreach ($columnsSelected as $columnSelect) {
            foreach ($sheetData as $rowD) {
                foreach ($rowD as $key => $cells) {
                    //dd($key,$cells);
                    if ($columnSelect == $cells) {
                        $final_columns->put($cells, $key);
                    }
                }
            }
        }

        // LOAD CALCULATIONS FOR COLUMN ------------------------
        $column_calculatioT_bol_rq = true;
        [$column_calculatioT_bol_rq, $contenedores_to_cal_rq] = HelperAll::calculationByContainers($valuesSelecteds['group_container_id']);

        if ($column_calculatioT_bol_rq) {
            // despacha el job
            $json_account = json_encode(['final_columns' => $final_columns->toArray(), 'valuesSelecteds' => $valuesSelecteds->toArray()]);
            $account->data = $json_account;
            $account->update();
            $json_account = json_decode($account->data, true);
            if (array_key_exists('final_columns', $json_account)) {
                // colocar contract_id al despachar para evitar el borrado mientras se importa el contracto
                if (env('APP_VIEW') == 'operaciones') {
                    ImportationRatesSurchargerJob::dispatch($account->id, $contract_id, \Auth::user()->id)->onQueue('operaciones'); //NO BORRAR!!
                } else {
                    ImportationRatesSurchargerJob::dispatch($account->id, $contract_id, \Auth::user()->id); //NO BORRAR!!
                }
                ///$this->handle($account->id);
                return redirect()->route('redirect.Processed.Information', $contract_id);
            } else {
                Log::error('Json-Account data load error. Reload the page and try again please. ' . Auth::user()->email);
                $request->session()->flash('message.nivel', 'error');
                $request->session()->flash('message.content', 'Json-Account data load error. Reload the page and try again please');

                return back();
            }
        } else {
            Log::error('Container calculation type relationship error. Check Relationship in the module "Containers Calculation Types"');
            $request->session()->flash('message.nivel', 'error');
            $request->session()->flash('message.content', 'Error in the relation Container-CalculationType');

            return back();
        }

        //dd($final_columns,$valuesSelecteds,$columnsSelected,$sheetData);
    }

    // Edicion de handle
    public function handle($account_id)
    {
        //$account_id = $this->account_id;
        $account = AccountFcl::find($account_id);
        $json_account_dc = json_decode($account->data, true);
        $valuesSelecteds = $json_account_dc['valuesSelecteds'];
        $final_columns = $json_account_dc['final_columns'];
        $ncontractRq = NewContractRequest::find($account->request_id);
        $ncontractRq->status = 'Processing';
        $ncontractRq->update();
        //dd($valuesSelecteds,$final_columns);

        $contract_id = $valuesSelecteds['contract_id'];
        $groupContainer_id = $valuesSelecteds['group_container_id'];
        $column_calculatioT_bol = true;
        $caracteres = ['*', '/', '.', '?', '"', 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, '{', '}', '[', ']', '+', '_', '|', '°', '!', '$', '%', '&', '(', ')', '=', '¿', '¡', ';', '>', '<', '^', '`', '¨', '~', ':', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];

        // LOAD CALCULATIONS FOR COLUMN ------------------------
        $conatiner_calculation_id = [];

        $behaviourContainers = BehaviourPerContainer::pluck('name')->all();
        [$column_calculatioT_bol, $conatiner_calculation_id] = HelperAll::calculationByContainers($valuesSelecteds['group_container_id']);
        //dd($conatiner_calculation_id);

        // --------------- AL FINALIZAR  CARGAR LA EXATRACCION DESDE S3 -----------------

        $mediaItem = $account->getFirstMedia('document');
        $excel = Storage::disk('FclAccount')->get($mediaItem->id . '/' . $mediaItem->file_name);
        Storage::disk('FclImport')->put($mediaItem->file_name, $excel);
        $excelF = Storage::disk('FclImport')->url($mediaItem->file_name);

        $extObj = new \SplFileInfo($mediaItem->file_name);
        $ext = $extObj->getExtension();

        if (strnatcasecmp($ext, 'xlsx') == 0) {
            $inputFileType = 'Xlsx';
        } elseif (strnatcasecmp($ext, 'xls') == 0) {
            $inputFileType = 'Xls';
        } else {
            $inputFileType = 'Csv';
        }
        if (strnatcasecmp($ext, 'csv') != 0) {
            $reader = IOFactory::createReader($inputFileType);
            $spreadsheet = $reader->load($excelF);
            $writer = IOFactory::createWriter($spreadsheet, 'Csv');
            $writer->setSheetIndex(0);
            $excelF = str_replace($ext, 'csv', $excelF);
            $inputFileType = 'Csv';
            $writer->save($excelF);
        }
        //dd($excelF,$extObj,$ext);
        // --------------- AL FINALIZAR  CARGAR LA EXATRACCION DESDE S3 -----------------

        if ($column_calculatioT_bol) {
            $chunkRow = new ChunkReadFilter();

            $readerJob = IOFactory::createReader($inputFileType);
            $readerJob->setReadDataOnly(true);
            //$readerJob->setReadFilter($chunkRow);

            $chunkSize = 2;

            $spreadsheetJob = $readerJob->load($excelF);
            $sheetData = $spreadsheetJob->getActiveSheet()->toArray();
            //dd($final_columns->toArray(),$valuesSelecteds->toArray(),$columnsSelected->toArray());

            $originExc = @$final_columns['ORIGIN']; // lectura de excel
            $destinyExc = @$final_columns['DESTINY']; // lectura de excel
            $chargeExc = @$final_columns['CHARGE']; // lectura de excel
            $calculationtypeExc = @$final_columns['CALCULATION TYPE']; // lectura de excel
            $chargeExc = @$final_columns['CHARGE']; // lectura de excel
            $limitsExc = @$final_columns['LIMITS']; // lectura de excel // para los limites de OW

            $company_user_id = $valuesSelecteds['company_user_id'];
            $statusPortCountry = $valuesSelecteds['select_portCountryRegion'];
            $statusTypeDestiny = $valuesSelecteds['select_typeDestiny'];
            $statusCarrier = $valuesSelecteds['select_carrier'];
            $chargeVal = $valuesSelecteds['chargeVal'];
            $request_columns = $valuesSelecteds['request_columns'];
            $statusCurrency = $valuesSelecteds['select_currency'];

            $currencyVal = '';

            // DIFERENCIADOR DE PUERTO CONTRY/REGION ---------------
            if ($statusPortCountry) {
                $differentiator = @$final_columns['DIFFERENTIATOR'];
            }

            // CURRENCY --------------------------------------------
            if ($statusCurrency == 3) {
                $currencyVal = $valuesSelecteds['currencyVal'];
            } elseif ($statusCurrency == 1) {
                $currencyExc = $final_columns['CURRENCY'];
            }

            // TYPE DESTINY ----------------------------------------
            if (!$statusTypeDestiny) {
                $typedestinyExc = $final_columns['TYPE DESTINY'];
            }

            if (!$statusCarrier) {
                $carrierExc = $final_columns['CARRIER'];
            }
            $columns_rt_ident = [];
            if ($groupContainer_id == 1) {
                $contenedores_rt = Container::where('gp_container_id', $groupContainer_id)->where('options->column', true)->get();
                foreach ($contenedores_rt as $conten_rt) {
                    $conten_rt->options = json_decode($conten_rt->options);
                    $columns_rt_ident[$conten_rt->code] = $conten_rt->options->column_name;
                }
            }
            $collection = collect([]);
            $countRow = 1;
            foreach ($sheetData as $row) {
                if ($countRow > 1) {

                    $calculationtypeVal = '';
                    $typedestinyVal = '';
                    $surchargeVal = '';
                    $carrierVal = '';

                    $typeExiBol = false;
                    $carriExitBol = false;
                    $typeChargeExiBol = false;
                    $calculationtypeExiBol = false;
                    $typedestinyExitBol = false;
                    $ct_converted_Bol = false;

                    $calculation_type_exc = null;
                    $chargeExc_val = null;

                    $chargeExc_val = $row[$chargeExc];
                    $calculation_type_exc = $row[$calculationtypeExc];
                    //dd($final_columns->toArray(),$valuesSelecteds->toArray(),$columnsSelected->toArray(),$row);

                    //------------------ COLUMNS SELECTEDS VALUES/CURRENCY/OPTIONS ----------------------------
                    $contenedores = Container::where('gp_container_id', $groupContainer_id)->get();
                    $columna_cont = [];
                    $currency_bol = [];
                    foreach ($contenedores as $contenedor) {
                        $options_cont = null;
                        $options_cont = json_decode($contenedor->options);
                        if (in_array($contenedor->code, $request_columns)) { // Asociamos en una matriz llaves Valores y moneda que exista en la seleccion
                            if ($statusCurrency == 3) { //currency seleccionado en el panel(select) no hay columna en el excel
                                $value_ = null;
                                $value_ = floatval($row[$final_columns[$contenedor->code]]);
                                $columna_cont[$contenedor->code] = [$value_, $currencyVal, $options_cont->optional, false, $options_cont->column];
                                $currency_bol[$contenedor->code] = true;
                            } elseif ($statusCurrency == 2) { // valor y currency en la misma columna del excel
                                $value_arr = [];
                                $value_arr = explode(' ', trim($row[$final_columns[$contenedor->code]]));
                                if (count($value_arr) == 1) {
                                    array_push($value_arr, '_E_E');
                                    array_push($value_arr, $options_cont->optional);
                                    array_push($value_arr, false);
                                    array_push($value_arr, $options_cont->column);
                                    $currency_bol[$contenedor->code] = false;
                                    $value_arr[0] = floatval($value_arr[0]);
                                    $columna_cont[$contenedor->code] = $value_arr;
                                } elseif (count($value_arr) > 1) {
                                    $curren_obj = Currency::where('alphacode', '=', $value_arr[1])->first();
                                    if (!empty($curren_obj->id)) {
                                        $value_arr[1] = $curren_obj->id;
                                        if (count($value_arr) == 2) {
                                            $currency_bol[$contenedor->code] = true;
                                        } else {
                                            $value_arr[1] = $curren_obj->alphacode . '_E_E';
                                            $currency_bol[$contenedor->code] = false;
                                        }

                                        if (count($value_arr) == 2) {
                                            array_push($value_arr, $options_cont->optional);
                                            array_push($value_arr, false);
                                            array_push($value_arr, $options_cont->column);
                                        } elseif (count($value_arr) == 3) {
                                            $value_arr[2] = $options_cont->optional;
                                            array_push($value_arr, false);
                                            array_push($value_arr, $options_cont->column);
                                        } elseif (count($value_arr) == 4) {
                                            $value_arr[2] = $options_cont->optional;
                                            $value_arr[3] = false;
                                            array_push($value_arr, $options_cont->column);
                                        }
                                        $value_arr[0] = floatval($value_arr[0]);
                                        $columna_cont[$contenedor->code] = $value_arr;
                                    } else {
                                        $value_arr[0] = floatval($value_arr[0]);
                                        $columna_cont[$contenedor->code] = [$value_arr[0], $value_arr[1] . '_E_E', $options_cont->optional, false, $options_cont->column];
                                        $currency_bol[$contenedor->code] = false;
                                    }
                                }
                            } elseif ($statusCurrency == 1) { // columna sola de currency en el excel
                                $value_cur = null;
                                $value_cur = trim($row[$currencyExc]);
                                $curren_obj = Currency::where('alphacode', '=', $value_cur)->first();
                                //                                try{
                                //                                    $curren_obj->id;
                                //                                } catch(\Exception $e){
                                //                                    dd($statusCurrency,$currencyExc,$value_cur,$curren_obj,empty($curren_obj->id));
                                //                                }
                                if (!empty($curren_obj->id)) {
                                    $value_cur = $curren_obj->id;
                                    $currency_bol[$contenedor->code] = true;
                                } else {
                                    $value_cur = $value_cur . '_E_E';
                                    $currency_bol[$contenedor->code] = false;
                                }
                                $columna_cont[$contenedor->code] = [floatval($row[$final_columns[$contenedor->code]]), $value_cur, $options_cont->optional, false, $options_cont->column];
                            }
                            //array_push($columna_cont[$contenedor->code],false);
                        } else { // Agregamos en una matriz llaves Valores y moneda que no existen en la seleccion pero si en el equipo Dry,RF,FR,OP....
                            $currency_bol[$contenedor->code] = true;
                            $columna_cont[$contenedor->code] = [0.00, 149, $options_cont->optional, true, $options_cont->column];
                        }
                    }

                    //  0 --->  valor.
                    //  1 --->  moneda.
                    //  2 --->  opcional en el comparador (nor y 45) (true si es opcional).
                    //  3 --->  la columna se agrego automaticamente(true) porque el usuario no la agrego, false no se agreo A.
                    //  5 --->  la columna pertenece a una columna(true) o a un json (false).

                    //dd($columna_cont,$currency_bol,$statusCurrency);
                    //--- PORT/CONTRY/REGION BOOL -------------------------------------
                    $differentiatorVal = '';
                    if ($statusPortCountry) {
                        $differentiatorVal = trim($row[$differentiator]);
                    } else {
                        $differentiatorVal = 'port';
                    }

                    //--- ORIGIN ------------------------------------------------------
                    $oricount = 0;
                    $originMultps = explode('|', $row[$originExc]);
                    foreach ($originMultps as $originMultCompact) {
                        if (strnatcasecmp($differentiatorVal, 'region') == 0) {
                            $originMultCompact = trim($originMultCompact);
                            $regionsOR = [];
                            $regionsOR = Region::where('name', 'like', '%' . $originMultCompact . '%')->with('CountriesRegions.country')->get();
                            if (count((array)$regionsOR) == 1) {
                                // region add
                                foreach ($regionsOR as $regionor) {
                                    if ($oricount == 0) {
                                        $originMultps = $regionor->CountriesRegions->pluck('country')->pluck('name')->toArray();
                                    } else {
                                        foreach ($regionor->CountriesRegions->pluck('country')->pluck('name')->toArray() as $oricountriesarray) {
                                            array_push($originMultps, $oricountriesarray);
                                        }
                                    }
                                }
                            } elseif (count((array)$regionsOR) == 0) {
                                // pais add
                                if ($oricount == 0) {
                                    $originMultps = [$originMultCompact];
                                } else {
                                    array_push($originMultps, $originMultCompact);
                                }
                            }
                        }
                        $oricount++;
                    }

                    //--- DESTINY -----------------------------------------------------
                    $descount = 0;
                    $destinyMultps = explode('|', $row[$destinyExc]);
                    foreach ($destinyMultps as $destinyMultCompact) {
                        if (strnatcasecmp($differentiatorVal, 'region') == 0) {
                            $destinyMultCompact = trim($destinyMultCompact);
                            $regionsDES = [];
                            $regionsDES = Region::where('name', 'like', '%' . $destinyMultCompact . '%')->with('CountriesRegions.country')->get();
                            if (count((array)$regionsDES) == 1) {
                                // region add
                                foreach ($regionsDES as $regiondes) {
                                    if ($descount == 0) {
                                        $destinyMultps = $regiondes->CountriesRegions->pluck('country')->pluck('name')->toArray();
                                    } else {
                                        foreach ($regiondes->CountriesRegions->pluck('country')->pluck('name')->toArray() as $descountriesarray) {
                                            array_push($destinyMultps, $descountriesarray);
                                        }
                                    }
                                }
                            } elseif (count((array)$regionsDES) == 0) {
                                // pais add
                                if ($descount == 0) {
                                    $destinyMultps = [$destinyMultCompact];
                                } else {
                                    array_push($destinyMultps, $destinyMultCompact);
                                }
                            }
                        }
                        $descount++;
                    }


                    //------------------ CALCULATION TYPE -----------------------------------------------------
                    $calculationtype = null;
                    $calculationtype = CalculationType::where('options->name', '=', $calculation_type_exc)
                        ->where('group_container_id', '=', $groupContainer_id)
                        ->get();
                    if ($calculationtype->isEmpty()) {
                        $calculationtype = CalculationType::where('options->name', '=', $calculation_type_exc)->get();
                    }

                    if (count($calculationtype) == 1) {
                        $calculationtypeExiBol = true;
                        $calculationtypeVal = $calculationtype[0]['id'];
                        $ct_options = $calculationtype[0]->options;
                        $ct_options = (!empty($ct_options)) ? json_decode($ct_options, true) : ["limits_ow" => false];
                        $ct_options = (array_key_exists('limits_ow', $ct_options)) ? $ct_options : $ct_options + ["limits_ow" => false];
                    } elseif (count($calculationtype) > 1) {
                        $calculationtypeVal = $calculation_type_exc . ' F.R + ' . count($calculationtype) . ' fila ' . $countRow . '_E_E';
                    } else {
                        $calculationtypeVal = $calculation_type_exc . ' fila ' . $countRow . '_E_E';
                    }

                    //--- LIMITS OW -----------------------------------------------------
                    $limits_val = [];
                    $limitsExiBol = true;
                    if (!empty($row[$limitsExc])) {
                        $limits_val = array_map('trim', explode('-', $row[$limitsExc]));
                        if (count($limits_val) == 1) {
                            array_push($limits_val, null);
                        }
                    } else {
                        $limitsExiBol = ($ct_options['limits_ow']) ? false : true;
                        $limits_val = ['_E_E', '_E_E'];
                    }
                    //--------------- Type Destiny ------------------------------------------------------------

                    if ($statusTypeDestiny) {
                        $typedestinyExitBol = true;
                        $typedestinyVal = $valuesSelecteds['typeDestinyVal'];
                    } else {
                        $typedestinyVal = $row[$typedestinyExc]; // cuando el carrier existe en el excel
                        $typedestinyResul = str_replace($caracteres, '', $typedestinyVal);
                        $typedestinyobj = TypeDestiny::where('description', '=', $typedestinyResul)->first();
                        if (empty($typedestinyobj->id) != true) {
                            $typedestinyExitBol = true;
                            $typedestinyVal = $typedestinyobj->id;
                        } else {
                            $typedestinyVal = $typedestinyVal . '_E_E';
                        }
                    }

                    //------------------ TYPE - CHARGE --------------------------------------------------------

                    if (!empty($chargeExc_val)) {
                        $typeChargeExiBol = true;
                        if ($chargeExc_val != $chargeVal) {
                            $surchargelist = Surcharge::where('name', '=', $chargeExc_val)
                                ->where('company_user_id', '=', $company_user_id)
                                ->first();
                            if (empty($surchargelist) != true) {
                                $surchargeVal = $surchargelist['id'];
                            } else {
                                $surchargelist = Surcharge::create([
                                    'name' => $chargeExc_val,
                                    'description' => $chargeExc_val,
                                    'company_user_id' => $company_user_id,
                                    'internal_options' => json_encode(['is_api' => false]),
                                ]);
                                $surchargeVal = $surchargelist->id;
                            }
                        }
                    } else {
                        $surchargeVal = $chargeExc_val . '_E_E';
                    }

                    //--------------- CARRIER -----------------------------------------------------------------
                    if ($statusCarrier) {
                        $carriExitBol = true;
                        $carrierVal = $valuesSelecteds['carrierVal']; // cuando se indica que no posee carrier
                    } else {
                        $carrierVal = $row[$carrierExc]; // cuando el carrier existe en el excel
                        $carrierArr = PrvCarrier::get_carrier($carrierVal);
                        $carriExitBol = $carrierArr['boolean'];
                        $carrierVal = $carrierArr['carrier'];
                    }

                    $values = true;
                    $values_uniq = [];
                    foreach ($columna_cont as $columnaRow) {
                        array_push($values_uniq, floatval($columnaRow[0]));
                    }
                    if (
                        count(array_unique($values_uniq)) == 1
                        && $values_uniq[0] == 0.00
                    ) {
                        $values = false;
                    }

                    //dd($columna_cont,$values);

                    //------------------ VALIDACION DE CURRENCY FALSE Ó TRUE 20 40 ...------------------------

                    $variant_currency = true;
                    $currency_uniq = [];
                    foreach ($currency_bol as $columnCurrenRow) {
                        if ($columnCurrenRow == true) {
                            array_push($currency_uniq, 1);
                        } else {
                            array_push($currency_uniq, 0);
                        }
                    }
                    if (count(array_unique($currency_uniq)) > 1) {
                        $variant_currency = false;
                    } elseif (
                        count(array_unique($currency_uniq)) == 1
                        && $currency_uniq[0] == 0
                    ) {
                        $variant_currency = false;
                    }
                    //--- INICION DE ERECORRIDO POR | ---------------------------------
                    foreach ($originMultps as $originMult) {
                        foreach ($destinyMultps as $destinyMult) {
                            $originVal = '';
                            $destinyVal = '';

                            $differentiatorBol = false;
                            $origExiBol = false;
                            $destiExitBol = false;


                            //--------------- DIFRENCIADOR HARBOR COUNTRY ---------------------------------------------
                            if ($statusPortCountry) {
                                if (strnatcasecmp($differentiatorVal, 'country') == 0 || strnatcasecmp($differentiatorVal, 'region') == 0) {
                                    $differentiatorBol = true;
                                }
                            }

                            //--------------- ORIGEN MULTIPLE O SIMPLE ------------------------------------------------
                            $originVal = trim($originMult); // hacer validacion de puerto en DB
                            if ($differentiatorBol == false) {
                                // El origen es  por puerto
                                $resultadoPortOri = PrvHarbor::get_harbor($originVal);
                                if ($resultadoPortOri['boolean']) {
                                    $origExiBol = true;
                                }
                                $originVal = $resultadoPortOri['puerto'];
                            } elseif ($differentiatorBol == true) {
                                // El origen es  por country
                                $resultadocountrytOri = PrvHarbor::get_country($originVal);
                                if ($resultadocountrytOri['boolean']) {
                                    $origExiBol = true;
                                }
                                $originVal = $resultadocountrytOri['country'];
                            }
                            //$collection->push([$countRow => [$originVal,$statusPortCountry,$differentiatorBol,$differentiatorVal]]);

                            //---------------- DESTINO MULTIPLE O SIMPLE -----------------------------------------------
                            $destinyVal = trim($destinyMult); // hacer validacion de puerto en DB
                            if ($differentiatorBol == false) {
                                // El origen es  por Harbors
                                $resultadoPortDes = PrvHarbor::get_harbor($destinyVal);
                                if ($resultadoPortDes['boolean']) {
                                    $destiExitBol = true;
                                }
                                $destinyVal = $resultadoPortDes['puerto'];
                            } elseif ($differentiatorBol == true) {
                                //El destino es por Country
                                $resultadocountryDes = PrvHarbor::get_country($destinyVal);
                                if ($resultadocountryDes['boolean']) {
                                    $destiExitBol = true;
                                }
                                $destinyVal = $resultadocountryDes['country'];
                            }

                            //------------------ VALIDACION DE CAMPOS VACIOS COLUMNAS 20 40 ...------------------------

                            // AYUDANTES -----------------------

                            //                            $currency_bol = [];
                            //              $statusCurrency           = 2;
                            //              $currency_bol['20DV']     = false;
                            //              $columna_cont['20DV'][1]  = 'USDD_E_E';
                            //              //                            $currency_bol['40DV'] = true;
                            //              //                            $currency_bol['40HC'] = true;
                            //              $calculation_type_exc     = 'PER_SHIPMENT';
                            //              $calculationtypeVal       = 6;
                            //              //$calculationtypeVal       = 'PER_SHIPMENTss F.R + 0 fila'.$countRow.'_E_E';
                            //              $calculationtypeExiBol    = true;
                            //
                            //              //$columna_cont = [];
                            //              $columna_cont['20DV'][0] = 1.0;
                            //              $columna_cont['40DV'][0] = 15.0;
                            //              $columna_cont['40HC'][0] = 7.00;

                            // FIN - AYUDANTES ------------------


                            //dd($currency_bol,$currency_uniq,array_unique($currency_uniq),$variant_currency);

                            $datos_finales = [
                                'originVal' => $originVal,
                                'destinyVal' => $destinyVal,
                                'typedestinyVal' => $typedestinyVal,
                                'carrierVal' => $carrierVal,
                                'surchargeVal' => $surchargeVal,
                                'calculationtypeVal' => $calculationtypeVal,
                                'contract_id' => $contract_id,
                                'chargeVal' => $chargeVal, // indica la diferencia entre "rate" o surcharge
                                'columnas_por_request' => $request_columns, // valores por columna, incluye el currency por columna
                                'valores_por_columna' => $columna_cont, // valores por columna, incluye el currency por columna:
                                //  0 --->  valor.
                                //  1 --->  moneda.
                                //  2 --->  opcional en el comparador (nor y 45) (true si es opcional).
                                //  3 --->  la columna se agrego automaticamente(true) porque el usuario no la agrego, false no se agreo A.
                                //  5 --->  la columna pertenece a una columna(true) o a un json (false).
                                'columns_rt_ident' => $columns_rt_ident, // contiene los nombres de las columnas de rates, DRY options->column = true
                                'currencyBol_por_colum' => $currency_bol, // Arreglo de  currency por columna
                                '$calculation_type_exc' => $calculation_type_exc, // Columna Calculation del excel
                                'origExiBol' => $origExiBol, // true si encontro el valor origen
                                'destiExitBol' => $destiExitBol, // true si encontro el valor destino
                                'typedestinyExitBol' => $typedestinyExitBol, // true si encontro el valor type destiny
                                'carriExitBol' => $carriExitBol, // true si encontro el valor carrier
                                'calculationtypeExiBol' => $calculationtypeExiBol, // true si encontro el valor calculation type
                                'limitsExiBol' => $limitsExiBol, // Booleano para verificar si existe valores limits_ow  para C.T. OW
                                'values' => $values, // true si si todos los valore son distintos de cero
                                'typeChargeExiBol' => $typeChargeExiBol, // true si el valor es distinto de vacio
                                'variant_currency' => $variant_currency, // true si el encontro todos los currency, false si alguno de sus contenedores no tiene currency
                                'differentiatorBol' => $differentiatorBol, // falso para port, true  para country o region
                                'statusPortCountry' => $statusPortCountry, // true status de activacion port contry region, false port
                                'statusTypeDestiny' => $statusTypeDestiny, // true para Seleccion desde panel, false para mapeo de excel
                                'statusCarrier' => $statusCarrier, // true para seleccion desde el panel, falso para mapear excel
                                'statusCurrency' => $statusCurrency, // 3. val. por SELECT,1. columna de  currency, 2. currency mas valor juntos
                                'conatiner_calculation_id' => $conatiner_calculation_id, // asocia los calculations con las columnas. relacion columna => calculation_id
                                'column_calculatioT_bol' => $column_calculatioT_bol, // False si falla la asociacion, true si esta asociado correctamente
                                'limits_val' => $limits_val, // Array que Indica los Limite para OW
                                'ct_options' => $ct_options, // Indica el valor Booleano para los OW
                            ];
                            if (strnatcasecmp($chargeExc_val, $chargeVal) == 0 && $typedestinyExitBol == false) {
                                $typedestinyExitBol = true;
                            }
                            //dd($datos_finales);

                            /////////////////////////////////

                            // INICIO IF PARA FALLIDOS O BUENOS
                            if (
                                $origExiBol == true
                                && $destiExitBol == true
                                && $typedestinyExitBol == true
                                && $carriExitBol == true
                                && $calculationtypeExiBol == true
                                && $values == true
                                && $variant_currency == true
                                && $typeChargeExiBol == true
                                && $limitsExiBol == true
                            ) {

                                ///////////////////////////////// GOOD

                                $container_json = null;
                                if (strnatcasecmp($chargeExc_val, $chargeVal) == 0) { // Rates
                                    if ($differentiatorBol == false) {
                                        $twuenty_val = 0;
                                        $forty_val = 0;
                                        $fortyhc_val = 0;
                                        $fortynor_val = 0;
                                        $fortyfive_val = 0;
                                        $currency_val = null;

                                        if ($groupContainer_id != 1) { //DISTINTO A DRY
                                            foreach ($columna_cont as $key => $conta_row) {
                                                if ($conta_row[4] == false) {
                                                    $container_json['C' . $key] = '' . $conta_row[0];
                                                }
                                                if ($conta_row[3] != true) {
                                                    $currency_val = $conta_row[1];
                                                }
                                            }
                                            $container_json = json_encode($container_json);
                                        } else { // DRY
                                            //dd($columna_cont);
                                            foreach ($columna_cont as $key => $conta_row) {
                                                if ($conta_row[4] == false) { // columna contenedores
                                                    $container_json['C' . $key] = '' . $conta_row[0];
                                                } else { // por columna específica
                                                    if (strnatcasecmp($columns_rt_ident[$key], 'twuenty') == 0) {
                                                        $twuenty_val = $conta_row[0];
                                                    } elseif (strnatcasecmp($columns_rt_ident[$key], 'forty') == 0) {
                                                        $forty_val = $conta_row[0];
                                                    } elseif (strnatcasecmp($columns_rt_ident[$key], 'fortyhc') == 0) {
                                                        $fortyhc_val = $conta_row[0];
                                                    } elseif (strnatcasecmp($columns_rt_ident[$key], 'fortynor') == 0) {
                                                        $fortynor_val = $conta_row[0];
                                                    } elseif (strnatcasecmp($columns_rt_ident[$key], 'fortyfive') == 0) {
                                                        $fortyfive_val = $conta_row[0];
                                                    }
                                                }
                                                if ($conta_row[3] != true) {
                                                    $currency_val = $conta_row[1];
                                                }
                                            }
                                            $container_json = json_encode($container_json);
                                        }
                                        $exists = [];
                                        $exists = Rate::where('origin_port', $originVal)
                                            ->where('destiny_port', $destinyVal)
                                            ->where('carrier_id', $carrierVal)
                                            ->where('contract_id', $contract_id)
                                            ->where('twuenty', $twuenty_val)
                                            ->where('forty', $forty_val)
                                            ->where('fortyhc', $fortyhc_val)
                                            ->where('fortynor', $fortynor_val)
                                            ->where('fortyfive', $fortyfive_val)
                                            ->where('containers', $container_json)
                                            ->where('currency_id', $currency_val)
                                            ->get();
                                        //dd($twuenty_val,$forty_val,$fortyhc_val,$fortynor_val,$fortyfive_val,$container_json,$currency_val,$exists);
                                        if (count($exists) == 0) {
                                            $ratesArre = Rate::create([
                                                'origin_port' => $originVal,
                                                'destiny_port' => $destinyVal,
                                                'carrier_id' => $carrierVal,
                                                'contract_id' => $contract_id,
                                                'twuenty' => $twuenty_val,
                                                'forty' => $forty_val,
                                                'fortyhc' => $fortyhc_val,
                                                'fortynor' => $fortynor_val,
                                                'fortyfive' => $fortyfive_val,
                                                'containers' => $container_json,
                                                'currency_id' => $currency_val,
                                            ]);
                                        }
                                    }
                                } else { //Surcharges

                                    if ($differentiatorBol == false) { //si es puerto verificamos si exite uno creado con puerto
                                        $typeplace = 'localcharports';
                                    } else { //si es country verificamos si exite uno creado con country
                                        $typeplace = 'localcharcountries';
                                    }
                                    // Es PER_CONTAINER - PER_CONTAINER_IMO ....
                                    if (in_array($calculation_type_exc, $behaviourContainers)) {

                                        // ESTOS ARREGLOS SON DE EJEMPLO PARA IGUALDAD DE VALORES EN PER_CONTAINER / Solo condicional -------
                                        //$columna_cont['20DV'][0]    = 1200;
                                        //$columna_cont['20DV'][3]    = false;
                                        //$columna_cont['40DV'][0]    = 1200;
                                        //$columna_cont['40HC'][0]    = 1200;
                                        //$columna_cont['40NOR'][0]   = 1200;
                                        //$columna_cont['45HC'][0]    = 1200;
                                        //$columna_cont['40NOR'][3]   = true;
                                        //$columna_cont['45HC'][3]    = true;

                                        // Comparamos si todos los valores son iguales (PER_CONTAINER) o si son distintos, dependiendo de equipo DRY,RF...
                                        $equals_values = [];
                                        $key = null;
                                        foreach ($columna_cont as $key => $conta_row) {
                                            if ($conta_row[3] == true && $conta_row[2] != true) {
                                                array_push($equals_values, $conta_row[0]);
                                            } elseif ($conta_row[3] == false) {
                                                array_push($equals_values, $conta_row[0]);
                                            }
                                        }
                                        //dd($columna_cont,$equals_values,array_unique($equals_values),count(array_unique($equals_values)));

                                        if (count(array_unique($equals_values)) == 1) { //Calculation PER_CONTAINER 1 solo registro
                                            $currency_val = null;
                                            $ammount = null;
                                            $key = null;
                                            foreach ($columna_cont as $key => $conta_row) {
                                                $ammount = $conta_row[0];
                                                $currency_val = $conta_row[1];
                                                break;
                                            }

                                            if ($ammount != 0 || $ammount != 0.00) {
                                                //Se verifica si existe un surcharge asociado con puerto o country dependiendo del diferenciador
                                                $surchargeObj = [];
                                                $surchargeObj = LocalCharge::where('surcharge_id', $surchargeVal)
                                                    ->where('typedestiny_id', $typedestinyVal)
                                                    ->where('contract_id', $contract_id)
                                                    ->where('calculationtype_id', $calculationtypeVal)
                                                    ->where('ammount', $ammount)
                                                    ->where('currency_id', $currency_val)
                                                    ->has($typeplace);

                                                if ($ct_options['limits_ow'] == true) {
                                                    $surchargeObj->whereHas('overweight_ranges', function ($query) use ($limits_val, $ammount) {
                                                        $query->where('lower_limit', $limits_val[0])
                                                            ->where('upper_limit', $limits_val[1])
                                                            ->where('amount', $ammount)
                                                            ->where('model_type', 'App\\LocalCharge');
                                                    });
                                                }

                                                $surchargeObj = $surchargeObj->get();

                                                if ($surchargeObj->isEmpty()) {
                                                    $surchargeObj = LocalCharge::create([ // tabla localcharges
                                                        'surcharge_id' => $surchargeVal,
                                                        'typedestiny_id' => $typedestinyVal,
                                                        'contract_id' => $contract_id,
                                                        'calculationtype_id' => $calculationtypeVal,
                                                        'ammount' => $ammount,
                                                        'currency_id' => $currency_val,
                                                    ]);
                                                    // ---------------------- Limits OW ------------------------------------------

                                                    if ($ct_options['limits_ow'] == true) {

                                                        OverweightRange::create([
                                                            'lower_limit' => $limits_val[0],
                                                            'upper_limit' => $limits_val[1],
                                                            'amount' => $ammount,
                                                            'model_id' => $surchargeObj->id,
                                                            'model_type' => 'App\\LocalCharge',
                                                        ]);
                                                    }
                                                } else {
                                                    $surchargeObj = $surchargeObj->first();
                                                }

                                                //----------------------- CARRIERS -------------------------------------------
                                                $existsCar = [];
                                                $existsCar = LocalCharCarrier::where('carrier_id', $carrierVal)
                                                    ->where('localcharge_id', $surchargeObj->id)->first();
                                                if (count((array)$existsCar) == 0) {
                                                    LocalCharCarrier::create([ // tabla localcharcarriers
                                                        'carrier_id' => $carrierVal,
                                                        'localcharge_id' => $surchargeObj->id,
                                                    ]);
                                                }

                                                //----------------------- ORIGEN DESTINO PUETO PAÍS --------------------------

                                                if ($differentiatorBol) { // country
                                                    $existCount = [];
                                                    $existCount = LocalCharCountry::where('country_orig', $originVal)
                                                        ->where('country_dest', $destinyVal)
                                                        ->where('localcharge_id', $surchargeObj->id)
                                                        ->first();
                                                    if (count((array)$existCount) == 0) {
                                                        $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                            'country_orig' => $originVal,
                                                            'country_dest' => $destinyVal,
                                                            'localcharge_id' => $surchargeObj->id,
                                                        ]);
                                                    }
                                                } else { // port
                                                    $existPort = [];
                                                    $existPort = LocalCharPort::where('port_orig', $originVal)
                                                        ->where('port_dest', $destinyVal)
                                                        ->where('localcharge_id', $surchargeObj->id)
                                                        ->first();
                                                    if (count((array)$existPort) == 0) {
                                                        $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                            'port_orig' => $originVal,
                                                            'port_dest' => $destinyVal,
                                                            'localcharge_id' => $surchargeObj->id,
                                                        ]);
                                                    }
                                                }
                                            }
                                        } elseif (count(array_unique($equals_values)) > 1) { //Calculation PER_ + "Contenedor o columna" registro por contenedor
                                            $key = null;
                                            $rows_calculations = [];
                                            foreach ($columna_cont as $key => $conta_row) { // Cargamos cada columna para despues insertarlas en la BD
                                                $rows_calculations[$key] = [
                                                    //'type'            => $key,
                                                    'calculationtype' => $conatiner_calculation_id[$calculation_type_exc][$key]['id'],
                                                    'limits_ow' => $conatiner_calculation_id[$calculation_type_exc][$key]['limits_ow'],
                                                    'ammount' => $conta_row[0],
                                                    'currency' => $conta_row[1],
                                                ];
                                            }
                                            //dd($rows_calculations);
                                            $key = null;
                                            foreach ($rows_calculations as $key => $row_calculation) {

                                                //dd($key,$row_calculation);
                                                if ($row_calculation['ammount'] != 0 || $row_calculation['ammount'] != 0.00) {
                                                    //Se verifica si existe un surcharge asociado con puerto o country dependiendo del diferenciador
                                                    $surchargeObj = [];
                                                    $surchargeObj = LocalCharge::where('surcharge_id', $surchargeVal)
                                                        ->where('typedestiny_id', $typedestinyVal)
                                                        ->where('contract_id', $contract_id)
                                                        ->where('calculationtype_id', $row_calculation['calculationtype'])
                                                        ->where('ammount', $row_calculation['ammount'])
                                                        ->where('currency_id', $row_calculation['currency'])
                                                        ->has($typeplace);

                                                    if ($row_calculation['limits_ow'] == true) {
                                                        $surchargeObj->whereHas('overweight_ranges', function ($query) use ($limits_val, $row_calculation) {
                                                            $query->where('lower_limit', $limits_val[0])
                                                                ->where('upper_limit', $limits_val[1])
                                                                ->where('amount', $row_calculation['ammount'])
                                                                ->where('model_type', 'App\\LocalCharge');
                                                        });
                                                    }
                                                    $surchargeObj = $surchargeObj->get();

                                                    if ($surchargeObj->isEmpty()) {
                                                        $surchargeObj = LocalCharge::create([ // tabla localcharges
                                                            'surcharge_id' => $surchargeVal,
                                                            'typedestiny_id' => $typedestinyVal,
                                                            'contract_id' => $contract_id,
                                                            'calculationtype_id' => $row_calculation['calculationtype'],
                                                            'ammount' => $row_calculation['ammount'],
                                                            'currency_id' => $row_calculation['currency'],
                                                        ]);
                                                        // ---------------------- Limits OW ------------------------------------------

                                                        if ($row_calculation['limits_ow'] == true) {
                                                            OverweightRange::create([
                                                                'lower_limit' => $limits_val[0],
                                                                'upper_limit' => $limits_val[1],
                                                                'amount' => $row_calculation['ammount'],
                                                                'model_id' => $surchargeObj->id,
                                                                'model_type' => 'App\\LocalCharge',
                                                            ]);
                                                        }
                                                    } else {
                                                        $surchargeObj = $surchargeObj->first();
                                                    }

                                                    //----------------------- CARRIERS -------------------------------------------
                                                    $existsCar = [];
                                                    $existsCar = LocalCharCarrier::where('carrier_id', $carrierVal)
                                                        ->where('localcharge_id', $surchargeObj->id)->first();
                                                    if (count((array)$existsCar) == 0) {
                                                        LocalCharCarrier::create([ // tabla localcharcarriers
                                                            'carrier_id' => $carrierVal,
                                                            'localcharge_id' => $surchargeObj->id,
                                                        ]);
                                                    }

                                                    //----------------------- ORIGEN DESTINO PUETO PAÍS --------------------------

                                                    if ($differentiatorBol) { // country
                                                        $existCount = [];
                                                        $existCount = LocalCharCountry::where('country_orig', $originVal)
                                                            ->where('country_dest', $destinyVal)
                                                            ->where('localcharge_id', $surchargeObj->id)
                                                            ->first();
                                                        if (count((array)$existCount) == 0) {
                                                            $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                                'country_orig' => $originVal,
                                                                'country_dest' => $destinyVal,
                                                                'localcharge_id' => $surchargeObj->id,
                                                            ]);
                                                        }
                                                    } else { // port
                                                        $existPort = [];
                                                        $existPort = LocalCharPort::where('port_orig', $originVal)
                                                            ->where('port_dest', $destinyVal)
                                                            ->where('localcharge_id', $surchargeObj->id)
                                                            ->first();
                                                        if (count((array)$existPort) == 0) {
                                                            $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                                'port_orig' => $originVal,
                                                                'port_dest' => $destinyVal,
                                                                'localcharge_id' => $surchargeObj->id,
                                                            ]);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        $currency_val = null;
                                        $ammount = null;
                                        $key = null;
                                        foreach ($columna_cont as $key => $conta_row) {
                                            if ($conta_row[3] != true) {
                                                $ammount = $conta_row[0];
                                            }
                                            $currency_val = $conta_row[1];
                                            if ($ammount != 0.00 || $ammount != null) {
                                                break;
                                            }
                                        }

                                        //Se verifica si existe un surcharge asociado con puerto o country dependiendo del diferenciador
                                        $surchargeObj = [];
                                        $surchargeObj = LocalCharge::where('surcharge_id', $surchargeVal)
                                            ->where('typedestiny_id', $typedestinyVal)
                                            ->where('contract_id', $contract_id)
                                            ->where('calculationtype_id', $calculationtypeVal)
                                            ->where('ammount', $ammount)
                                            ->where('currency_id', $currency_val)
                                            ->has($typeplace);
                                        if ($ct_options['limits_ow'] == true) {
                                            $surchargeObj->whereHas('overweight_ranges', function ($query) use ($limits_val, $ammount) {
                                                $query->where('lower_limit', $limits_val[0])
                                                    ->where('upper_limit', $limits_val[1])
                                                    ->where('amount', $ammount)
                                                    ->where('model_type', 'App\\LocalCharge');
                                            });
                                        }
                                        $surchargeObj = $surchargeObj->get();

                                        if ($surchargeObj->isEmpty()) {
                                            $surchargeObj = LocalCharge::create([ // tabla localcharges
                                                'surcharge_id' => $surchargeVal,
                                                'typedestiny_id' => $typedestinyVal,
                                                'contract_id' => $contract_id,
                                                'calculationtype_id' => $calculationtypeVal,
                                                'ammount' => $ammount,
                                                'currency_id' => $currency_val,
                                            ]);
                                            // ---------------------- Limits OW ------------------------------------------

                                            if ($ct_options['limits_ow'] == true) {
                                                OverweightRange::create([
                                                    'lower_limit' => $limits_val[0],
                                                    'upper_limit' => $limits_val[1],
                                                    'amount' => $ammount,
                                                    'model_id' => $surchargeObj->id,
                                                    'model_type' => 'App\\LocalCharge',
                                                ]);
                                            }
                                        } else {
                                            $surchargeObj = $surchargeObj->first();
                                        }


                                        //----------------------- CARRIERS -------------------------------------------
                                        $existsCar = [];
                                        $existsCar = LocalCharCarrier::where('carrier_id', $carrierVal)
                                            ->where('localcharge_id', $surchargeObj->id)->first();
                                        if (count((array)$existsCar) == 0) {
                                            LocalCharCarrier::create([ // tabla localcharcarriers
                                                'carrier_id' => $carrierVal,
                                                'localcharge_id' => $surchargeObj->id,
                                            ]);
                                        }

                                        //----------------------- ORIGEN DESTINO PUETO PAÍS --------------------------
                                        if ($differentiatorBol) { // country
                                            $existCount = [];
                                            $existCount = LocalCharCountry::where('country_orig', $originVal)
                                                ->where('country_dest', $destinyVal)
                                                ->where('localcharge_id', $surchargeObj->id)
                                                ->first();
                                            if (count((array)$existCount) == 0) {
                                                $SurchargPortArreG = LocalCharCountry::create([ // tabla LocalCharCountry country
                                                    'country_orig' => $originVal,
                                                    'country_dest' => $destinyVal,
                                                    'localcharge_id' => $surchargeObj->id,
                                                ]);
                                            }
                                        } else { // port
                                            $existPort = [];
                                            $existPort = LocalCharPort::where('port_orig', $originVal)
                                                ->where('port_dest', $destinyVal)
                                                ->where('localcharge_id', $surchargeObj->id)
                                                ->first();
                                            if (count((array)$existPort) == 0) {
                                                $SurchargPortArreG = LocalCharPort::create([ // tabla localcharports harbor
                                                    'port_orig' => $originVal,
                                                    'port_dest' => $destinyVal,
                                                    'localcharge_id' => $surchargeObj->id,
                                                ]);
                                            }
                                        }
                                    }
                                }

                                ///////////////////////////////// END GOOD
                            } else {
                                //dd($datos_finales);
                                if ($values != false) {
                                    // ORIGIN -------------------------------------------------------------
                                    if ($origExiBol) {
                                        if ($differentiatorBol == false) {
                                            $originVal = Harbor::find($originVal);
                                            $originVal = $originVal->name;
                                        } elseif ($differentiatorBol == true) {
                                            $originVal = Country::find($originVal);
                                            $originVal = $originVal['name'];
                                        }
                                    }
                                    // DESTINATION --------------------------------------------------------
                                    if ($destiExitBol) {
                                        if ($differentiatorBol == false) {
                                            $destinyVal = Harbor::find($destinyVal);
                                            $destinyVal = $destinyVal->name;
                                        } elseif ($differentiatorBol == true) {
                                            $destinyVal = Country::find($destinyVal);
                                            $destinyVal = $destinyVal->name;
                                        }
                                    }
                                    //---------------------------- CALCULATION TYPE -----------------------
                                    if ($calculationtypeExiBol == true && $ct_converted_Bol == false) {
                                        $calculationtypeVal = CalculationType::find($calculationtypeVal);
                                        $calculationtypeVal = $calculationtypeVal['name'];
                                        $ct_converted_Bol = true;
                                    }
                                    //---------------------------- TYPE - SURCHARGE -----------------------
                                    if (strnatcasecmp($chargeExc_val, $chargeVal) != 0) {
                                        if ($typeChargeExiBol) {
                                            $surchargeVal = Surcharge::find($surchargeVal);
                                            $surchargeVal = $surchargeVal->name;
                                            $typeChargeExiBol = false;
                                        }
                                    }
                                    //---------------------------- CARRIER --------------------------------
                                    if ($carriExitBol) {
                                        $carrierVal = Carrier::find($carrierVal);
                                        $carrierVal = $carrierVal->name;
                                        $carriExitBol = false;
                                    }
                                    //---------------------------- TYPE DESTINY ---------------------------
                                    if ($typedestinyExitBol == true && strnatcasecmp($chargeExc_val, $chargeVal) != 0) {
                                        try {
                                            $typedestinyVal = TypeDestiny::find($typedestinyVal);
                                            $typedestinyVal = $typedestinyVal->description;
                                            $typedestinyExitBol = false;
                                        } catch (\Exception $e) {
                                            dd($datos_finales);
                                        }
                                    }

                                    if (strnatcasecmp($chargeExc_val, $chargeVal) == 0) {
                                        $twuenty_val = 0;
                                        $forty_val = 0;
                                        $fortyhc_val = 0;
                                        $fortynor_val = 0;
                                        $fortyfive_val = 0;
                                        $currency_val = null;
                                        $container_json = [];
                                        if ($differentiatorBol == false) {
                                            if ($groupContainer_id != 1) { //DISTINTO A DRY
                                                foreach ($columna_cont as $key => $conta_row) {
                                                    if ($conta_row[4] == false) {
                                                        $rspVal = null;
                                                        $rspVal = HelperAll::currencyJoin(
                                                            $statusCurrency,
                                                            $currency_bol[$key],
                                                            $conta_row[0],
                                                            $conta_row[1]
                                                        );
                                                        $container_json['C' . $key] = '' . $rspVal;
                                                    }
                                                    if ($conta_row[3] != true) {
                                                        if ($currency_bol[$key] == false) {
                                                            $currency_val = $conta_row[1];
                                                        } else {
                                                            $currencyObj = Currency::find($conta_row[1]);
                                                            $currency_val = $currencyObj->alphacode;
                                                        }
                                                    }
                                                }
                                                $container_json = json_encode($container_json);
                                            } else { // DRY
                                                foreach ($columna_cont as $key => $conta_row) {
                                                    if ($conta_row[4] == false) { // columna contenedores
                                                        $rspVal = null;
                                                        $rspVal = HelperAll::currencyJoin(
                                                            $statusCurrency,
                                                            $currency_bol[$key],
                                                            $conta_row[0],
                                                            $conta_row[1]
                                                        );
                                                        $container_json['C' . $key] = '' . $rspVal;
                                                    } else { // por columna específica
                                                        if (strnatcasecmp($columns_rt_ident[$key], 'twuenty') == 0) {
                                                            $twuenty_val = HelperAll::currencyJoin(
                                                                $statusCurrency,
                                                                $currency_bol[$key],
                                                                $conta_row[0],
                                                                $conta_row[1]
                                                            );
                                                        } elseif (strnatcasecmp($columns_rt_ident[$key], 'forty') == 0) {
                                                            $forty_val = HelperAll::currencyJoin(
                                                                $statusCurrency,
                                                                $currency_bol[$key],
                                                                $conta_row[0],
                                                                $conta_row[1]
                                                            );
                                                        } elseif (strnatcasecmp($columns_rt_ident[$key], 'fortyhc') == 0) {
                                                            $fortyhc_val = HelperAll::currencyJoin(
                                                                $statusCurrency,
                                                                $currency_bol[$key],
                                                                $conta_row[0],
                                                                $conta_row[1]
                                                            );
                                                        } elseif (strnatcasecmp($columns_rt_ident[$key], 'fortynor') == 0) {
                                                            $fortynor_val = HelperAll::currencyJoin(
                                                                $statusCurrency,
                                                                $currency_bol[$key],
                                                                $conta_row[0],
                                                                $conta_row[1]
                                                            );
                                                        } elseif (strnatcasecmp($columns_rt_ident[$key], 'fortyfive') == 0) {
                                                            $fortyfive_val = HelperAll::currencyJoin(
                                                                $statusCurrency,
                                                                $currency_bol[$key],
                                                                $conta_row[0],
                                                                $conta_row[1]
                                                            );
                                                        }
                                                    }
                                                    if ($conta_row[3] != true) {
                                                        $currency_val = $conta_row[1];
                                                    }
                                                }
                                                $container_json = json_encode($container_json);
                                            }

                                            $exists = null;
                                            $exists = FailRate::where('origin_port', $originVal)
                                                ->where('destiny_port', $destinyVal)
                                                ->where('carrier_id', $carrierVal)
                                                ->where('contract_id', $contract_id)
                                                ->where('twuenty', $twuenty_val)
                                                ->where('forty', $forty_val)
                                                ->where('fortyhc', $fortyhc_val)
                                                ->where('fortynor', $fortynor_val)
                                                ->where('fortyfive', $fortyfive_val)
                                                ->where('containers', $container_json)
                                                ->where('currency_id', $currency_val)
                                                ->get();

                                            if (count($exists) == 0) {
                                                $respFR = FailRate::create([
                                                    'origin_port' => $originVal,
                                                    'destiny_port' => $destinyVal,
                                                    'carrier_id' => $carrierVal,
                                                    'contract_id' => $contract_id,
                                                    'twuenty' => $twuenty_val,
                                                    'forty' => $forty_val,
                                                    'fortyhc' => $fortyhc_val,
                                                    'fortynor' => $fortynor_val,
                                                    'fortyfive' => $fortyfive_val,
                                                    'containers' => $container_json,
                                                    'currency_id' => $currency_val,
                                                ]);
                                            }
                                        }
                                    } else {
                                        if ($differentiatorBol) {
                                            $differentiatorVal = 2;
                                        } else {
                                            $differentiatorVal = 1;
                                        }
                                        if ($calculationtypeExiBol == true && $ct_converted_Bol == true) {
                                            // Es PER_CONTAINER PER_CONATINER_IMO .....
                                            if (in_array($calculation_type_exc, $behaviourContainers)) {
                                                $equals_values = [];
                                                $key = null;
                                                foreach ($columna_cont as $key => $conta_row) {
                                                    if ($conta_row[3] == true && $conta_row[2] != true) {
                                                        array_push($equals_values, $conta_row[0]);
                                                    } elseif ($conta_row[3] == false) {
                                                        array_push($equals_values, $conta_row[0]);
                                                    }
                                                }

                                                if (count(array_unique($equals_values)) == 1) { // Valores iguales.
                                                    $currency_val = null;
                                                    $ammount = null;
                                                    $key = null;
                                                    $currency_bol_f = true;
                                                    foreach ($columna_cont as $key => $conta_row) {
                                                        if ($conta_row[3] != true) {
                                                            $ammount = $conta_row[0];
                                                        }
                                                        if ($variant_currency) {
                                                            $currency_val = $conta_row[1];
                                                            break;
                                                        } else {
                                                            if ($currency_bol[$key] == false) {
                                                                $currency_bol_f = false;
                                                                $currency_val = $conta_row[1];
                                                            }
                                                        }
                                                    }

                                                    $ammount = HelperAll::currencyJoin(
                                                        $statusCurrency,
                                                        $currency_bol_f,
                                                        $ammount,
                                                        $currency_val
                                                    );
                                                    if ($currency_bol_f) {
                                                        $currencyObj = Currency::find($currency_val);
                                                        $currency_val = $currencyObj->alphacode;
                                                    }

                                                    //dd($ammount,$currency_val);
                                                    $failSurcharge = [];
                                                    $failSurcharge = FailSurCharge::where('surcharge_id', $surchargeVal)
                                                        ->where('port_orig', $originVal)
                                                        ->where('port_dest', $destinyVal)
                                                        ->where('typedestiny_id', $typedestinyVal)
                                                        ->where('contract_id', $contract_id)
                                                        ->where('calculationtype_id', $calculationtypeVal)
                                                        ->where('ammount', $ammount)
                                                        ->where('currency_id', $currency_val)
                                                        ->where('carrier_id', $carrierVal)
                                                        ->where('differentiator', $differentiatorVal);
                                                    if ($ct_options['limits_ow'] == true) {
                                                        $failSurcharge->whereHas('fail_overweight_ranges', function ($query) use ($limits_val) {
                                                            $query->where('lower_limit', $limits_val[0])
                                                                ->where('upper_limit', $limits_val[1])
                                                                ->where('model_type', 'App\\FailSurCharge');
                                                        });
                                                    }
                                                    $failSurcharge = $failSurcharge->get();

                                                    if ($failSurcharge->isEmpty()) {
                                                        $failSurcharge = FailSurCharge::create([
                                                            'surcharge_id' => $surchargeVal,
                                                            'port_orig' => $originVal,
                                                            'port_dest' => $destinyVal,
                                                            'typedestiny_id' => $typedestinyVal,
                                                            'contract_id' => $contract_id,
                                                            'calculationtype_id' => $calculationtypeVal, //////
                                                            'ammount' => $ammount, //////
                                                            'currency_id' => $currency_val, //////
                                                            'carrier_id' => $carrierVal,
                                                            'differentiator' => $differentiatorVal,
                                                        ]);
                                                        if ($ct_options['limits_ow'] == true) {
                                                            $failowRange = FailOverweightRange::where('lower_limit', $limits_val[0])
                                                                ->where('upper_limit', $limits_val[1])
                                                                ->where('model_id', $failSurcharge->id)
                                                                ->where('model_type', 'App\\FailSurCharge')
                                                                ->get();
                                                            if ($failowRange->isEmpty()) {
                                                                FailOverweightRange::create([
                                                                    'lower_limit' => $limits_val[0],
                                                                    'upper_limit' => $limits_val[1],
                                                                    'model_id' => $failSurcharge->id,
                                                                    'model_type' => 'App\\FailSurCharge',
                                                                ]);
                                                            }
                                                        }
                                                    }
                                                } elseif (count(array_unique($equals_values)) > 1) { //Valores distintos
                                                    $key = null;
                                                    $rows_calculations = [];
                                                    foreach ($columna_cont as $key => $conta_row) { // Cargamos cada columna para despues insertarlas en la BD
                                                        $calculationtypeVal = CalculationType::find($conatiner_calculation_id[$calculation_type_exc][$key]['id']);
                                                        $calculationtypeVal = $calculationtypeVal->name;
                                                        if ($currency_bol[$key]) {
                                                            $currency_val = Currency::find($conta_row[1]);
                                                            $currency_val = $currency_val->alphacode;
                                                        } else {
                                                            $currency_val = $conta_row[1];
                                                        }
                                                        $ammount = null;
                                                        $ammount = HelperAll::currencyJoin(
                                                            $statusCurrency,
                                                            $currency_bol[$key],
                                                            $conta_row[0],
                                                            $conta_row[1]
                                                        );
                                                        $ammoun_zero = false;
                                                        if ($conta_row[0] == 0.0 || $conta_row[0] == 0) {
                                                            $ammoun_zero = true;
                                                        }
                                                        $rows_calculations[$key] = [
                                                            'calculationtype' => $calculationtypeVal,
                                                            'ammount' => $ammount,
                                                            'ammount_zero' => $ammoun_zero,
                                                            'currency' => $currency_val,
                                                            'limits_ow' => $conatiner_calculation_id[$calculation_type_exc][$key]['limits_ow'],
                                                        ];
                                                    }
                                                    //dd($rows_calculations);
                                                    foreach ($rows_calculations as $key => $row_calculation) {
                                                        if ($row_calculation['ammount_zero'] != true) {
                                                            $failSurcharge = [];
                                                            $failSurcharge = FailSurCharge::where('surcharge_id', $surchargeVal)
                                                                ->where('port_orig', $originVal)
                                                                ->where('port_dest', $destinyVal)
                                                                ->where('typedestiny_id', $typedestinyVal)
                                                                ->where('contract_id', $contract_id)
                                                                ->where('calculationtype_id', $row_calculation['calculationtype'])
                                                                ->where('ammount', $row_calculation['ammount'])
                                                                ->where('currency_id', $row_calculation['currency'])
                                                                ->where('carrier_id', $carrierVal)
                                                                ->where('differentiator', $differentiatorVal);

                                                            if ($row_calculation['limits_ow'] == true) {
                                                                $failSurcharge->whereHas('fail_overweight_ranges', function ($query) use ($limits_val) {
                                                                    $query->where('lower_limit', $limits_val[0])
                                                                        ->where('upper_limit', $limits_val[1])
                                                                        ->where('model_type', 'App\\FailSurCharge');
                                                                });
                                                            }
                                                            $failSurcharge = $failSurcharge->get();
                                                            if ($failSurcharge->isEmpty()) {
                                                                $failSurcharge = FailSurCharge::create([
                                                                    'surcharge_id' => $surchargeVal,
                                                                    'port_orig' => $originVal,
                                                                    'port_dest' => $destinyVal,
                                                                    'typedestiny_id' => $typedestinyVal,
                                                                    'contract_id' => $contract_id,
                                                                    'calculationtype_id' => $row_calculation['calculationtype'], //////
                                                                    'ammount' => $row_calculation['ammount'], //////
                                                                    'currency_id' => $row_calculation['currency'], //////
                                                                    'carrier_id' => $carrierVal,
                                                                    'differentiator' => $differentiatorVal,
                                                                ]);
                                                                if ($row_calculation['limits_ow'] == true) {
                                                                    FailOverweightRange::create([
                                                                        'lower_limit' => $limits_val[0],
                                                                        'upper_limit' => $limits_val[1],
                                                                        'model_id' => $failSurcharge->id,
                                                                        'model_type' => 'App\\FailSurCharge',
                                                                    ]);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            } else {
                                                $ammount = null;
                                                $key = null;
                                                foreach ($columna_cont as $key => $conta_row) {
                                                    if ($conta_row[3] != true && $conta_row[0] != 0.00 && $conta_row[0] != null) {
                                                        $ammount = $conta_row[0];
                                                        break;
                                                    }
                                                }
                                                $key = null;
                                                $currency_val = null;
                                                $currency_bol_f = true;
                                                foreach ($columna_cont as $key => $conta_rowT) {
                                                    if ($variant_currency) {
                                                        $currency_val = $conta_rowT[1];
                                                        break;
                                                    } else {
                                                        if ($currency_bol[$key] == false) {
                                                            $currency_bol_f = false;
                                                            $currency_val = $conta_rowT[1];
                                                        }
                                                    }
                                                }

                                                $ammount = HelperAll::currencyJoin(
                                                    $statusCurrency,
                                                    $currency_bol_f,
                                                    $ammount,
                                                    $currency_val
                                                );
                                                if ($currency_bol_f) {
                                                    $currencyObj = Currency::find($currency_val);
                                                    $currency_val = $currencyObj->alphacode;
                                                }
                                                //dd('registro pr ship',$variant_currency,$columna_cont,$currency_val,$ammount);
                                                $failSurcharge = [];
                                                $failSurcharge = FailSurCharge::where('surcharge_id', $surchargeVal)
                                                    ->where('port_orig', $originVal)
                                                    ->where('port_dest', $destinyVal)
                                                    ->where('typedestiny_id', $typedestinyVal)
                                                    ->where('contract_id', $contract_id)
                                                    ->where('calculationtype_id', $calculationtypeVal)
                                                    ->where('ammount', $ammount)
                                                    ->where('currency_id', $currency_val)
                                                    ->where('carrier_id', $carrierVal)
                                                    ->where('differentiator', $differentiatorVal);

                                                if ($ct_options['limits_ow'] == true) {
                                                    $failSurcharge->whereHas('fail_overweight_ranges', function ($query) use ($limits_val) {
                                                        $query->where('lower_limit', $limits_val[0])
                                                            ->where('upper_limit', $limits_val[1])
                                                            ->where('model_type', 'App\\FailSurCharge');
                                                    });
                                                }

                                                $failSurcharge = $failSurcharge->get();
                                                if ($failSurcharge->isEmpty()) {
                                                    $failSurcharge = FailSurCharge::create([
                                                        'surcharge_id' => $surchargeVal,
                                                        'port_orig' => $originVal,
                                                        'port_dest' => $destinyVal,
                                                        'typedestiny_id' => $typedestinyVal,
                                                        'contract_id' => $contract_id,
                                                        'calculationtype_id' => $calculationtypeVal, //////
                                                        'ammount' => $ammount, //////
                                                        'currency_id' => $currency_val, //////
                                                        'carrier_id' => $carrierVal,
                                                        'differentiator' => $differentiatorVal,
                                                    ]);
                                                    if ($ct_options['limits_ow'] == true) {

                                                        FailOverweightRange::create([
                                                            'lower_limit' => $limits_val[0],
                                                            'upper_limit' => $limits_val[1],
                                                            'model_id' => $failSurcharge->id,
                                                            'model_type' => 'App\\FailSurCharge',
                                                        ]);
                                                    }
                                                }
                                            }
                                        } else { // Calculation Type desconocido

                                            $key = null;
                                            $rows_calculations = [];
                                            foreach ($columna_cont as $key => $conta_row) { // Cargamos cada columna para despues insertarlas en la BD
                                                $calculationtypeValFail = null;
                                                $calculationtypeValFail = $key . ' ' . $calculationtypeVal;
                                                if ($currency_bol[$key]) {
                                                    $currency_val = Currency::find($conta_row[1]);
                                                    $currency_val = $currency_val->alphacode;
                                                } else {
                                                    $currency_val = $conta_row[1];
                                                }
                                                $ammount = null;
                                                $ammount = HelperAll::currencyJoin(
                                                    $statusCurrency,
                                                    $currency_bol[$key],
                                                    $conta_row[0],
                                                    $conta_row[1]
                                                );
                                                $ammoun_zero = false;
                                                if ($conta_row[0] == 0.0 || $conta_row[0] == 0) {
                                                    $ammoun_zero = true;
                                                }
                                                $limit_ow_bool = false;
                                                if (array_key_exists($calculation_type_exc, $conatiner_calculation_id)) {
                                                    $limit_ow_bool = $conatiner_calculation_id[$calculation_type_exc][$key]['limits_ow'];
                                                }

                                                $rows_calculations[$key] = [
                                                    'calculationtype' => $calculationtypeValFail . ' Fila ' . $countRow,
                                                    'ammount' => $ammount,
                                                    'ammount_zero' => $ammoun_zero,
                                                    'currency' => $currency_val,
                                                    'limits_ow' => $limit_ow_bool,
                                                ];
                                            }
                                            //dd('llega aqui Cals',$rows_calculations);
                                            foreach ($rows_calculations as $key => $row_calculation) {
                                                if ($row_calculation['ammount_zero'] != true) {
                                                    $failSurcharge = [];
                                                    $failSurcharge = FailSurCharge::where('surcharge_id', $surchargeVal)
                                                        ->where('port_orig', $originVal)
                                                        ->where('port_dest', $destinyVal)
                                                        ->where('typedestiny_id', $typedestinyVal)
                                                        ->where('contract_id', $contract_id)
                                                        ->where('calculationtype_id', $row_calculation['calculationtype'])
                                                        ->where('ammount', $row_calculation['ammount'])
                                                        ->where('currency_id', $row_calculation['currency'])
                                                        ->where('carrier_id', $carrierVal)
                                                        ->where('differentiator', $differentiatorVal);
                                                    if ($row_calculation['limits_ow'] == true) {
                                                        $failSurcharge->whereHas('fail_overweight_ranges', function ($query) use ($limits_val) {
                                                            $query->where('lower_limit', $limits_val[0])
                                                                ->where('upper_limit', $limits_val[1])
                                                                ->where('model_type', 'App\\FailSurCharge');
                                                        });
                                                    }
                                                    $failSurcharge = $failSurcharge->get();
                                                    if ($failSurcharge->isEmpty()) {
                                                        $failSurcharge = FailSurCharge::create([
                                                            'surcharge_id' => $surchargeVal,
                                                            'port_orig' => $originVal,
                                                            'port_dest' => $destinyVal,
                                                            'typedestiny_id' => $typedestinyVal,
                                                            'contract_id' => $contract_id,
                                                            'calculationtype_id' => $row_calculation['calculationtype'], //////
                                                            'ammount' => $row_calculation['ammount'], //////
                                                            'currency_id' => $row_calculation['currency'], //////
                                                            'carrier_id' => $carrierVal,
                                                            'differentiator' => $differentiatorVal,
                                                        ]);
                                                        if ($row_calculation['limits_ow'] == true) {

                                                            FailOverweightRange::create([
                                                                'lower_limit' => $limits_val[0],
                                                                'upper_limit' => $limits_val[1],
                                                                'model_id' => $failSurcharge->id,
                                                                'model_type' => 'App\\FailSurCharge',
                                                            ]);
                                                        }
                                                    }
                                                }
                                            }
                                            //dd('registro');
                                        }
                                    }
                                }
                            }
                            // ELSE O FIN DEL IF PARA FALLIDOS O BUENOS

                            /////////////////////////////////
                        }
                    }
                }
                $countRow++;
            }
            //dd($collection);

            $nopalicaHs = Harbor::where('name', 'No Aplica')->get();
            $nopalicaCs = Country::where('name', 'No Aplica')->get();
            foreach ($nopalicaHs as $nopalicaH) {
                $nopalicaH = $nopalicaH['id'];
            }
            foreach ($nopalicaCs as $nopalicaC) {
                $nopalicaC = $nopalicaC['id'];
            }

            //            $failsurchargeS = FailSurCharge::where('contract_id','=',$this->contract_id)->where('port_orig','LIKE','%No Aplica%')->delete();
            //            $failsurchargeS = FailSurCharge::where('contract_id','=',$this->contract_id)->where('port_dest','LIKE','%No Aplica%')->delete();
            //
            //            $surchargecollection = LocalCharge::where('contract_id',$this->contract_id)
            //                ->whereHas('localcharcountries',function($query) use($nopalicaC){
            //                    $query->where('country_dest',$nopalicaC)->orWhere('country_orig',$nopalicaC);
            //                })
            //                ->orWhereHas('localcharports',function($q) use($nopalicaH){
            //                    $q->where('port_dest','=',$nopalicaH)->orWhere('port_orig',$nopalicaH);
            //                })->forceDelete();

            // dd($collection);

            //            $userNotifique = User::find($this->user_id);
            //            $message = 'The file imported was processed :' .$this->contract_id ;
            //            $userNotifique->notify(new SlackNotification($message));
            //            $userNotifique->notify(new N_general($userNotifique,$message));
        } else {
            //imprimir en el log error
            Log::error('Container calculation type relationship error');
        }

        Storage::disk('FclImport')->Delete($mediaItem->file_name);
        if (strnatcasecmp($ext, 'csv') != 0) {
            $file_csv = str_replace(['.xlsx', '.xls'], '.csv', $mediaItem->file_name);
            Storage::disk('FclImport')->Delete($file_csv);
        }
        $ncontractRq->status = 'Imp Finished';
        $ncontractRq->update();
    }

    public function LoadFails($id, $tab)
    {
        $countrates = Rate::where('contract_id', '=', $id)->count();
        $countfailrates = FailRate::where('contract_id', '=', $id)->count();
        $countfailsurcharge = FailSurCharge::where('contract_id', '=', $id)->count();
        $countgoodsurcharge = LocalCharge::where('contract_id', '=', $id)->count();
        $contract = Contract::find($id);
        if (!empty($contract->gp_container_id)) {
            $equiment_id = $contract->gp_container_id;
        } else {
            $equiment_id = 1;
        }
        $equiment = HelperAll::LoadHearderDataTable($equiment_id, 'rates');
        //dd($equiment);

        //$tab = 'FailSurcharge';
        return view('importationV2.Fcl.show_fails', compact('countfailrates', 'countrates', 'contract', 'id', 'tab', 'equiment', 'countfailsurcharge', 'countgoodsurcharge'));
    }

    public function redirectProcessedInformation($id)
    {
        $contract = Contract::find($id);

        return view('importationV2.Fcl.processedInformation', compact('id', 'contract'));
    }

    // Multiples Rates ------------------------------------------------------------------

    //Edita solo el origen y destino para rates fallidos, solo se coloca una vez
    public function EdicionRatesMultiples(Request $request)
    {
        $harbor = Harbor::pluck('display_name', 'id');
        $arreglo = $request->idAr;
        $contract_id = $request->contract_id;
        //dd($harbor,$arreglo);
        return view('importationV2.Fcl.Body-Modals.storeFailRatesMultiples', compact('harbor', 'arreglo', 'contract_id'));
    }

    public function StoreFailRatesMultiples(Request $request)
    {
        //dd($request->all());
        $id = $request->contract_id;
        $dataArr = ['id' => $id, 'data' => $request->toArray()];
        //dd($dataArr);
        if (env('APP_VIEW') == 'operaciones') {
            GeneralJob::dispatch('edit_mult_rates_fcl', $dataArr)->onQueue('operaciones');
        } else {
            GeneralJob::dispatch('edit_mult_rates_fcl', $dataArr);
        }

        $request->session()->flash('message.content', 'Updating Rate');
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');

        return redirect()->route('Failed.Developer.For.Contracts', [$id, $request->nameTab]);
    }

    //Carga la edicion multiple de rates fallidos, para todos los datos del Rate
    public function loadArrayEditMult(Request $request)
    {
        $array = $request->idAr;
        $array_count = count($array);
        $contract_id = $request->contract_id;

        return view('importationV2.Fcl.Body-Modals.FailEditByDetalls', compact('array', 'array_count', 'contract_id'));
    }

    public function showRatesMultiplesPorDetalles(Request $request)
    {
        //dd($request->all());
        $fail_rates_total = collect([]);
        $contract_id = $request->contract_id;
        $contract = Contract::find($contract_id);
        $harbor = Harbor::pluck('display_name', 'id');
        $carrier = Carrier::pluck('name', 'id');
        $currency = Currency::pluck('alphacode', 'id');
        $equiment_id = $contract->gp_container_id;
        $equiment = HelperAll::LoadHearderContaniers($equiment_id, 'rates');
        //dd($equiment);
        foreach ($request->idAr as $rate_fail_id) {
            $failrate = FailRate::find($rate_fail_id);

            $originV = null;
            $destinationV = null;
            $carrierV = null;
            $currencyV = null;
            $originA = null;
            $destinationA = null;
            $carrierA = null;
            $currencyA = null;
            $failed = [];
            $colec = [];

            $carrAIn = null;
            $pruebacurre = null;
            $classdorigin = 'green';
            $classddestination = 'green';
            $classcarrier = 'green';
            $classcurrency = 'green';

            $originA = explode('_', $failrate['origin_port']);
            $destinationA = explode('_', $failrate['destiny_port']);
            $carrierA = explode('_', $failrate['carrier_id']);
            $currencyA = explode('_', $failrate['currency_id']);
            $containers = json_decode($failrate->containers, true);

            $originOb = Harbor::where('varation->type', 'like', '%' . strtolower($originA[0]) . '%')
                ->first();
            if (count($originA) <= 1) {
                $originV = $originOb['id'];
            } else {
                $classdorigin = 'red';
            }

            $destinationOb = Harbor::where('varation->type', 'like', '%' . strtolower($destinationA[0]) . '%')
                ->first();
            if (count($destinationA) <= 1) {
                $destinationV = $destinationOb['id'];
            } else {
                $classddestination = 'red';
            }

            if (count($carrierA) <= 1) {
                $carrierOb = Carrier::where('name', '=', $carrierA[0])->first();
                $carrierV = $carrierOb['id'];
            } else {
                $classcarrier = 'red';
            }

            if (count($currencyA) <= 1) {
                $currenc = Currency::where('alphacode', '=', $currencyA[0])->orWhere('id', '=', $currencyA[0])->first();
                $currencyV = $currenc['id'];
            } else {
                $classcurrency = 'red';
            }

            $failed = [
                'rate_id' => $failrate->id,
                'contract_id' => $failrate->contract_id,
                'origin_port' => $originV,
                'destiny_port' => $destinationV,
                'carrierAIn' => $carrierV,
                'currencyAIn' => $currencyV,
                'classorigin' => $classdorigin,
                'classdestiny' => $classddestination,
                'classcarrier' => $classcarrier,
                'classcurrency' => $classcurrency,
            ];

            $equiments = GroupContainer::with('containers')->find($equiment_id);
            $columns_rt_ident = [];
            if ($equiment_id == 1) {
                $contenedores_rt = Container::where('gp_container_id', $equiment_id)->where('options->column', true)->get();
                foreach ($contenedores_rt as $conten_rt) {
                    $conten_rt->options = json_decode($conten_rt->options);
                    $columns_rt_ident[$conten_rt->code] = $conten_rt->options->column_name;
                }
                foreach ($equiments->containers as $containersEq) {
                    if (strnatcasecmp($columns_rt_ident[$containersEq->code], 'twuenty') == 0) {
                        $colec['C' . $containersEq->code] = HelperAll::validatorErrorWitdColor($failrate->twuenty);
                        $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                    } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'forty') == 0) {
                        $colec['C' . $containersEq->code] = HelperAll::validatorErrorWitdColor($failrate->forty);
                        $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                    } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortyhc') == 0) {
                        $colec['C' . $containersEq->code] = HelperAll::validatorErrorWitdColor($failrate->fortyhc);
                        $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                    } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortynor') == 0) {
                        $colec['C' . $containersEq->code] = HelperAll::validatorErrorWitdColor($failrate->fortynor);
                        $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                    } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortyfive') == 0) {
                        $colec['C' . $containersEq->code] = HelperAll::validatorErrorWitdColor($failrate->fortyfive);
                        $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                    }
                }
            } else {
                foreach ($equiments->containers as $containersEq) {
                    if (array_key_exists('C' . $containersEq->code, $containers)) {
                        $colec['C' . $containersEq->code] = HelperAll::validatorErrorWitdColor($containers['C' . $containersEq->code]);
                        $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                    } else {
                        $colec['C' . $containersEq->code] = ['value' => 0, 'color' => null, 'name' => $containersEq->code];
                    }
                }
            }
            $failed['containers'] = $colec;
            $fail_rates_total->push($failed);
        }

        //dd($fail_rates_total);
        return view('importationV2.Fcl.EditByDetallFailRates', compact('fail_rates_total', 'equiment', 'contract_id', 'equiment_id', 'contract', 'harbor', 'carrier', 'currency'));
    }

    public function StoreFailRatesMultiplesByDetalls(Request $request)
    {
        //dd($request->all());
        $contract_id = $request->contract_id;
        $data_rates = $request->rate_fail_id;
        $data_origins = $request->origin_id;
        $data_destinations = $request->destiny_id;
        $data_carrier = $request->carrier_id;
        $data_currency = $request->currency_id;

        $equiment_id = $request->equiment_id;
        $equiments = GroupContainer::with('containers')->find($equiment_id);
        $columns_rt_ident = [];

        foreach ($data_rates as $key => $data_rate) {
            //dd($request->all(),$data_rate,$key);
            $twuenty = 0;
            $forty = 0;
            $fortyhc = 0;
            $fortynor = 0;
            $fortyfive = 0;
            $containers = null;
            $colec = [];
            if ($equiment_id == 1) {
                $contenedores_rt = Container::where('gp_container_id', $equiment_id)->where('options->column', true)->get();
                foreach ($contenedores_rt as $conten_rt) {
                    $conten_rt->options = json_decode($conten_rt->options);
                    $columns_rt_ident[$conten_rt->code] = $conten_rt->options->column_name;
                }
            }
            if ($equiment_id == 1) {
                foreach ($equiments->containers as $containersEq) {
                    if (strnatcasecmp($columns_rt_ident[$containersEq->code], 'twuenty') == 0) {
                        $twuenty = floatval($request->input('C' . $containersEq->code)[$key]);
                    } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'forty') == 0) {
                        $forty = floatval($request->input('C' . $containersEq->code)[$key]);
                    } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortyhc') == 0) {
                        $fortyhc = floatval($request->input('C' . $containersEq->code)[$key]);
                    } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortynor') == 0) {
                        $fortynor = floatval($request->input('C' . $containersEq->code)[$key]);
                    } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortyfive') == 0) {
                        $fortyfive = floatval($request->input('C' . $containersEq->code)[$key]);
                    }
                }
            } else {
                foreach ($equiments->containers as $containersEq) {
                    $colec['C' . $containersEq->code] = '' . floatval($request->input('C' . $containersEq->code)[$key]);
                }
            }
            $containers = json_encode($colec);
            //dd($twuenty,$forty,$fortyhc,$fortynor,$fortyfive,$containers);

            foreach ($data_origins[$key] as $origin) {
                foreach ($data_destinations[$key] as $destiny) {
                    // dd($request->all(),$key,$origin,$destiny);
                    if ($origin != $destiny) {
                        $exists_rate = Rate::where('origin_port', $origin)
                            ->where('destiny_port', $destiny)
                            ->where('carrier_id', $data_carrier[$key])
                            ->where('contract_id', $contract_id)
                            ->where('twuenty', $twuenty)
                            ->where('forty', $forty)
                            ->where('fortyhc', $fortyhc)
                            ->where('fortynor', $fortynor)
                            ->where('fortyfive', $fortyfive)
                            ->where('containers', $containers)
                            ->where('currency_id', $data_currency[$key])
                            ->first();
                        if (count((array) $exists_rate) == 0) {
                            $return = Rate::create([
                                'origin_port' => $origin,
                                'destiny_port' => $destiny,
                                'carrier_id' => $data_carrier[$key],
                                'contract_id' => $contract_id,
                                'twuenty' => $twuenty,
                                'forty' => $forty,
                                'fortyhc' => $fortyhc,
                                'fortynor' => $fortynor,
                                'fortyfive' => $fortyfive,
                                'containers' => $containers,
                                'currency_id' => $data_currency[$key],
                                'schedule_type_id' => null,
                                'transit_time' => 0,
                                'via' => null,
                            ]);
                        }
                    }
                }
            }
            $failrate = FailRate::find($data_rate);
            $failrate->forceDelete();
            //eliminar fail aqui
        }

        $request->session()->flash('message.content', 'Updated Rates');
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');

        return redirect()->route('Failed.Developer.For.Contracts', [$contract_id, 0]);
    }
    ///////////////////////// edit multiple surcharge

    public function loadArrayEditMultSurcharge(Request $request)
    {
        $Surcharge = array();
        foreach ($request->idAr as $surch) {
            $id = preg_replace('([^0-9])', '', $surch);
            $Surcharge[] = $id;
        }
        $array = $Surcharge;
        $array_count = count($array);
        $contract_id = $request->contract_id;

        return view('importationV2.Fcl.Body-Modals.FailEditByDetallsSurcharge', compact('array', 'array_count', 'contract_id'));
    }

    public function showSurchargeMultiplesPorDetalles(Request $request)
    {
        // dd($request);
        $fail_surcharge_total = collect([]);
        $contract_id = $request->contract_id;
        $contract = Contract::find($contract_id);
        $countries = Country::pluck('name', 'id');
        $harbor = Harbor::pluck('display_name', 'id');
        $carrier = Carrier::pluck('name', 'id');
        $currency = Currency::pluck('alphacode', 'id');
        $equiment_id = $contract->gp_container_id;
        $surcharges = Surcharge::pluck('name', 'id');
        $calculation_type = CalculationType::pluck('name', 'id');
        $type_destiny = TypeDestiny::pluck('description', 'id');
        $equiment = HelperAll::LoadHearderContaniers($equiment_id, 'rates');

        foreach ($request->idAr as $surcharge_fail_id) {
            $failsurcharge = FailSurCharge::find($surcharge_fail_id);
            $failsurcharge->load('fail_overweight_ranges');
            $surchargesV = null;
            $originV = null;
            $destinationV = null;
            $calculation_typeV = null;
            $type_destinyV = null;
            $amountV = null;
            $carrierV = null;
            $currencyV = null;
            $originA = null;
            $destinationA = null;
            $carrierA = null;
            $currencyA = null;
            $failed = [];
            $colec = [];
            $type_rate = null;

            $carrAIn = null;
            $classsurcharger = 'green';
            $classdorigin = 'green';
            $classddestination = 'green';
            $classtypedestiny = 'green';
            $classcalculationtype = 'green';
            $classamount = 'green';
            $classcarrier = 'green';
            $classcurrency = 'green';
            $classupperlimit = 'green';
            $classlowerlimit = 'green';

            $surchargeA = explode('_', $failsurcharge['surcharge_id']);
            $originA = explode('_', $failsurcharge['port_orig']);
            $destinationA = explode('_', $failsurcharge['port_dest']);
            $calculation_typeA = explode('_', $failsurcharge['calculationtype_id']);
            $type_destinyA = explode('_', $failsurcharge['typedestiny_id']);
            $amountA = explode('_', $failsurcharge['ammount']);
            $carrierA = explode('_', $failsurcharge['carrier_id']);
            $currencyA = explode('_', $failsurcharge['currency_id']);

            $is_ow_limits = $failsurcharge->fail_overweight_ranges->isEmpty();
            if (!$is_ow_limits) {
                $lower_limitA = explode('_', $failsurcharge->fail_overweight_ranges->first()->lower_limit);
                $upper_limitA = explode('_', $failsurcharge->fail_overweight_ranges->first()->upper_limit);
            } else {
                $lower_limitA = [0];
                $upper_limitA = [0];
            }
            if (count($lower_limitA) > 1) {
                $lower_limitA = $lower_limitA[0] . ' (error)';
                $classlowerlimit = 'red';
            } else {
                $lower_limitA = $lower_limitA[0];
            }

            if (count($upper_limitA) > 1) {
                $upper_limitA = $upper_limitA[0] . ' (error)';
                $classupperlimit = 'red';
            } else {
                $upper_limitA = $upper_limitA[0];
            }

            if ($failsurcharge->differentiator == 1) {
                $originOb = Harbor::where('varation->type', 'like', '%' . strtolower($originA[0]) . '%')
                    ->first();
            } elseif ($failsurcharge->differentiator == 2) {
                $originOb = Country::where('variation->type', 'like', '%' . strtolower($originA[0]) . '%')
                    ->first();
            }
            if (count($originA) <= 1) {
                $originV = $originOb['id'];
            } else {
                $classdorigin = 'red';
            }

            if ($failsurcharge->differentiator == 1) {
                $destinationOb = Harbor::where('varation->type', 'like', '%' . strtolower($destinationA[0]) . '%')
                    ->first();
            } elseif ($failsurcharge->differentiator == 2) {
                $destinationOb = Country::where('variation->type', 'like', '%' . strtolower($destinationA[0]) . '%')
                    ->first();
            }

            if (count($destinationA) <= 1) {
                $destinationV = $destinationOb['id'];
            } else {
                $classddestination = 'red';
            }

            if (count($carrierA) <= 1) {
                $carrierOb = Carrier::where('name', '=', $carrierA[0])->first();
                $carrierV = $carrierOb['id'];
            } else {
                $classcarrier = 'red';
            }

            if (count($currencyA) <= 1) {
                $currenc = Currency::where('alphacode', '=', $currencyA[0])->first();
                $currencyV = $currenc['id'];
            } else {
                $classcurrency = 'red';
            }

            if (count($calculation_typeA) <= 1) {
                $calculatioT = CalculationType::where('name', '=', $calculation_typeA[0])->first();
                $calculation_typeV = $calculatioT['id'];
            } else {
                $classcalculationtype = 'red';
            }

            if (count($type_destinyA) <= 1) {
                $typeDest = TypeDestiny::where('description', '=', $type_destinyA[0])->first();
                $type_destinyV = $typeDest['id'];
            } else {
                $classtypedestiny = 'red';
            }

            if (count($amountA) <= 1) {
                if ($amountA[0] >= 1) {
                    $amountV = $amountA[0];
                } else {
                    $classamount = 'red';
                }
            } else {
                $classamount = 'red';
            }

            if (count($surchargeA) <= 1) {
                $Surcharg = Surcharge::where('name', '=', $surchargeA[0])->where('company_user_id', '=', $failsurcharge->contract->company_user_id)->first();
                // $Surcharg = Surcharge::where('name', '=', $surchargeA[0])->orWhere('id', '=', $surchargeA[0])->first();
                $surchargesV = $Surcharg['id'];
            } else {
                $classsurcharger = 'red';
            }

            if ($failsurcharge->differentiator == 1) {
                $type_rate = 'port';
            } else {
                $type_rate = 'country';
            }

            $failed = [
                'surcharge_id' => $failsurcharge->id,
                'contract_id' => $failsurcharge->contract_id,
                'surcharge' => $surchargesV,
                'origin_port' => $originV,
                'destiny_port' => $destinationV,
                'calculation_type' => $calculation_typeV,
                'type_destiny' => $type_destinyV,
                'amount' => $amountV,
                'lower_limit' => $lower_limitA,
                'upper_limit' => $upper_limitA,
                'carrierAIn' => $carrierV,
                'currencyAIn' => $currencyV,
                'classorigin' => $classdorigin,
                'classdestiny' => $classddestination,
                'classcarrier' => $classcarrier,
                'classcurrency' => $classcurrency,
                'classsurcharger' => $classsurcharger,
                'classtypedestiny' => $classtypedestiny,
                'classcalculationtype' => $classcalculationtype,
                'classupperlimit' => $classupperlimit,
                'classlowerlimit' => $classlowerlimit,
                'classamount' => $classamount,
                'type_rate' => $type_rate,
            ];
            $fail_surcharge_total->push($failed);
        }

        // dd($fail_surcharge_total);
        return view('importationV2.Fcl.EditByDetailFailSurcharge', compact('fail_surcharge_total', 'equiment', 'contract_id', 'equiment_id', 'contract', 'harbor', 'carrier', 'currency', 'surcharges', 'calculation_type', 'type_destiny', 'countries'));
    }

    public function StoreFailsurchargeMultiplesByDetalls(Request $request)
    {
        $contract_id = $request->contract_id;
        $data_surcharges = $request->surcharge_fail_id;
        $data_origins = $request->origin_id;
        $data_destinations = $request->destiny_id;
        $data_surcharge_id = $request->id_surcharge;
        $data_type_destiny = $request->type_destiny_id;
        $data_type_calculation = $request->type_calculation__id;
        $data_amount = $request->amountS;
        $data_carrier = $request->carrier_id;
        $data_currency = $request->currency_id;
        $typerate = $request->typerate;
        $data_lower_limit = $request->lower_limit;
        $data_upper_limit = $request->upper_limit;
        $equiment_id = $request->equiment_id;
        $calculationtype = CalculationType::all();
        $calculationtype = $calculationtype->map(function ($item, $key) {
            $item->setAttribute('options_decode', (!empty($item->options)) ? json_decode($item->options, true) : []);
            return $item;
        });

        // dd($typerate);
        foreach ($data_surcharges as $key => $data_surcharge) {
            $ct_get = $calculationtype->get($data_type_calculation[$key]);
            $limits_val = [];
            $ammount = $data_amount[$key];
            $surcharge_id = LocalCharge::where('surcharge_id', $data_surcharge_id[$key])
                ->where('typedestiny_id', $data_type_destiny[$key])
                ->where('contract_id', $contract_id)
                ->where('calculationtype_id', $data_type_calculation[$key])
                ->where('ammount', $ammount)
                ->where('currency_id', $data_currency[$key]);
            if ($ct_get->options_decode['limits_ow']) {
                $limits_val[0] = (!empty($data_lower_limit[$key])) ? intval($data_lower_limit[$key]) : null;
                $limits_val[1] = (!empty($data_upper_limit[$key])) ? intval($data_upper_limit[$key]) : null;
                $limits_val[0] = ($limits_val[0] == 0) ? null : $limits_val[0];
                $limits_val[1] = ($limits_val[1] == 0) ? null : $limits_val[1];
                $surcharge_id->whereHas('overweight_ranges', function ($query) use ($limits_val, $ammount) {
                    $query->where('lower_limit', $limits_val[0])
                        ->where('upper_limit', $limits_val[1])
                        ->where('amount', $ammount)
                        ->where('model_type', 'App\\LocalCharge');
                });
            }
            $surcharge_id = $surcharge_id->get();
            //dd($surcharge_id, $limits_val, $ct_get);
            if ($surcharge_id->isEmpty()) {
                $surcharge_id = new LocalCharge();
                $surcharge_id->surcharge_id = $data_surcharge_id[$key];
                $surcharge_id->typedestiny_id = $data_type_destiny[$key];
                $surcharge_id->calculationtype_id = $data_type_calculation[$key];
                $surcharge_id->ammount = $ammount;
                $surcharge_id->currency_id = $data_currency[$key];
                $surcharge_id->contract_id = $contract_id;
                if ($ct_get->options_decode['limits_ow']) {
                    if ((($limits_val[0] == null && $limits_val[1] == null) != true)) {
                        $surcharge_id->save();
                        OverweightRange::create([
                            'lower_limit' => $limits_val[0],
                            'upper_limit' => $limits_val[1],
                            'amount' => $ammount,
                            'model_id' => $surcharge_id->id,
                            'model_type' => 'App\\LocalCharge',
                        ]);
                    }
                } else {
                    $surcharge_id->save();
                }
            } else {
                $surcharge_id = $surcharge_id->first();
            }

            if ($typerate[$key]  == 'port') {
                foreach ($data_origins[$key] as $origin) {
                    foreach ($data_destinations[$key] as $destiny) {
                        $existsLP = null;
                        $existsLP = LocalCharPort::where('port_orig', $origin)
                            ->where('port_dest', $destiny)
                            ->where('localcharge_id', $surcharge_id->id)
                            ->first();
                        if (empty($existsLP)) {
                            LocalCharPort::create([
                                'port_orig' => $origin,
                                'port_dest' => $destiny,
                                'localcharge_id' => $surcharge_id->id,
                            ]); //
                        }
                    }
                }
            } elseif ($typerate[$key]  == 'country') {
                foreach ($data_origins[$key] as $origin) {
                    foreach ($data_destinations[$key] as $destiny) {
                        $existsLC = null;
                        $existsLC = LocalCharCountry::where('country_orig', $origin)
                            ->where('country_dest', $destiny)
                            ->where('localcharge_id', $surcharge_id->id)
                            ->first();
                        if (empty($existsLC)) {
                            LocalCharCountry::create([
                                'country_orig' => $origin,
                                'country_dest' => $destiny,
                                'localcharge_id' => $surcharge_id->id,
                            ]); //
                        }
                    }
                }
            }

            foreach ($data_carrier[$key] as $carrier) {
                $localcharcarriersV = null;
                $localcharcarriersV = LocalCharCarrier::where('carrier_id', $carrier)->where('localcharge_id', $surcharge_id->id)->get();

                if (count($localcharcarriersV) == 0) {
                    LocalCharCarrier::create([
                        'carrier_id' => $carrier,
                        'localcharge_id' => $surcharge_id->id,
                    ]);
                }
            }
            $failSurcharge = FailSurCharge::find($data_surcharge);
            if ($ct_get->options_decode['limits_ow']) {
                if ((($limits_val[0] == null && $limits_val[1] == null) != true)) {
                    $failSurcharge->forceDelete();
                }
            } else {
                $failSurcharge->forceDelete();
            }
            //eliminar fail aqui
        }

        $request->session()->flash('message.content', 'Updated Rates');
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');

        return redirect()->route('Failed.Developer.For.Contracts', [$contract_id, 0]);
    }
    ////////////////////////

    // Rates ----------------------------------------------------------------------------
    public function EditRatesGood($id)
    {
        $harbor = Harbor::pluck('display_name', 'id');
        $carrier = Carrier::pluck('name', 'id');
        $currency = Currency::pluck('alphacode', 'id');
        $schedulesT = [null => 'Please Select'];
        $scheduleTo = ScheduleType::all();
        foreach ($scheduleTo as $d) {
            $schedulesT[$d['id']] = $d->name;
        }
        $rate = Rate::find($id);
        $contract = Contract::find($rate->contract_id);
        $equiment_id = $contract->gp_container_id;
        $containers = json_decode($rate->containers, true);
        $columns_rt_ident = [];
        $equiments = GroupContainer::with('containers')->find($equiment_id);
        $colec = [];
        if ($equiment_id == 1) {
            $contenedores_rt = Container::where('gp_container_id', $equiment_id)->where('options->column', true)->get();
            foreach ($contenedores_rt as $conten_rt) {
                $conten_rt->options = json_decode($conten_rt->options);
                $columns_rt_ident[$conten_rt->code] = $conten_rt->options->column_name;
            }
            foreach ($equiments->containers as $containersEq) {
                if (strnatcasecmp($columns_rt_ident[$containersEq->code], 'twuenty') == 0) {
                    $colec['C' . $containersEq->code]['value'] = $rate->twuenty;
                    $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'forty') == 0) {
                    $colec['C' . $containersEq->code]['value'] = $rate->forty;
                    $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortyhc') == 0) {
                    $colec['C' . $containersEq->code]['value'] = $rate->fortyhc;
                    $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortynor') == 0) {
                    $colec['C' . $containersEq->code]['value'] = $rate->fortynor;
                    $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortyfive') == 0) {
                    $colec['C' . $containersEq->code]['value'] = $rate->fortyfive;
                    $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                }
            }
        } else {
            foreach ($equiments->containers as $containersEq) {
                if (array_key_exists('C' . $containersEq->code, $containers)) {
                    $colec['C' . $containersEq->code]['value'] = $containers['C' . $containersEq->code];
                    $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                } else {
                    $colec['C' . $containersEq->code]['value'] = 0;
                    $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                }
            }
        }
        //dd($colec);
        return view('importationV2.Fcl.Body-Modals.GoodEditRates', compact('rate', 'colec', 'equiment_id', 'harbor', 'carrier', 'schedulesT', 'currency'));
    }

    public function EditRatesFail($id)
    {
        $harbor = Harbor::all()->pluck('display_name', 'id');
        $carrier = Carrier::all()->pluck('name', 'id');
        $currency = Currency::all()->pluck('alphacode', 'id');
        $schedulesT = HelperAll::addOptionSelect(ScheduleType::all(), 'id', 'name');

        $failrate = FailRate::find($id);
        $containers = json_decode($failrate->containers, true);
        $contract = Contract::find($failrate->contract_id);
        $equiment_id = $contract->gp_container_id;
        //dd($failrate);

        $carrAIn;
        $currency_val = null;
        $classdorigin = 'green';
        $classddestination = 'green';
        $classcarrier = 'green';
        $classcurrency = 'green';

        $classscheduleT = 'green';
        $classtransittime = 'green';
        $classvia = 'green';

        $originA = explode('_', $failrate['origin_port']);
        //dd($originA);
        $destinationA = explode('_', $failrate['destiny_port']);
        $carrierA = explode('_', $failrate['carrier_id']);
        $currencyA = explode('_', $failrate['currency_id']);
        //        $twuentyA       = explode("_",$failrate['twuenty']);
        //        $fortyA         = explode("_",$failrate['forty']);
        //        $fortyhcA       = explode("_",$failrate['fortyhc']);
        //        $fortynorA      = explode("_",$failrate['fortynor']);
        //        $fortyfiveA     = explode("_",$failrate['fortyfive']);
        $schedueleTA = explode('_', $failrate['schedule_type']);

        if (count($schedueleTA) <= 1) {
            $schedueleTA = ScheduleType::where('name', $schedueleTA[0])->first();
            $schedueleTA = $schedueleTA['id'];
        } else {
            $schedueleTA = '';
            $classscheduleT = 'red';
        }

        $originOb = Harbor::where('varation->type', 'like', '%' . strtolower($originA[0]) . '%')
            ->first();
        $originA = [];
        if (count($originA) <= 1) {
            $originA = $originOb['name'];
            $originAIn = $originOb['id'];
        } else {
            $originA = $originA[0] . ' (error)';
            $classdorigin = 'red';
        }

        $destinationOb = Harbor::where('varation->type', 'like', '%' . strtolower($destinationA[0]) . '%')
            ->first();
        $destinationAIn = [];
        if (count($destinationA) <= 1) {
            $destinationAIn = $destinationOb['id'];
            $destinationA = $destinationOb['name'];
        } else {
            $destinationA = $destinationA[0] . ' (error)';
            $classddestination = 'red';
        }

        $carrierOb = Carrier::where('name', '=', $carrierA[0])->first();
        $carrAIn = $carrierOb['id'];
        if (count($carrierA) <= 1) {
            $carrierA = $carrierA[0];
        } else {
            $carrierA = $carrierA[0] . ' (error)';
            $classcarrier = 'red';
        }

        if (count($currencyA) <= 1) {
            $currenc = Currency::where('alphacode', '=', $currencyA[0])->orWhere('id', '=', $currencyA[0])->first();
            $currency_val = $currenc['id'];
            $currencyA = $currencyA[0];
        } else {
            $currencyA = $currencyA[0] . ' (error)';
            $classcurrency = 'red';
        }
        //dd($destinationAIn);
        $columns_rt_ident = [];
        $equiments = GroupContainer::with('containers')->find($equiment_id);
        $colec = [];
        if ($equiment_id == 1) {
            $contenedores_rt = Container::where('gp_container_id', $equiment_id)->where('options->column', true)->get();
            foreach ($contenedores_rt as $conten_rt) {
                $conten_rt->options = json_decode($conten_rt->options);
                $columns_rt_ident[$conten_rt->code] = $conten_rt->options->column_name;
            }
            foreach ($equiments->containers as $containersEq) {
                if (strnatcasecmp($columns_rt_ident[$containersEq->code], 'twuenty') == 0) {
                    $colec['C' . $containersEq->code] = HelperAll::validatorErrorWitdColor($failrate->twuenty);
                    $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'forty') == 0) {
                    $colec['C' . $containersEq->code] = HelperAll::validatorErrorWitdColor($failrate->forty);
                    $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortyhc') == 0) {
                    $colec['C' . $containersEq->code] = HelperAll::validatorErrorWitdColor($failrate->fortyhc);
                    $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortynor') == 0) {
                    $colec['C' . $containersEq->code] = HelperAll::validatorErrorWitdColor($failrate->fortynor);
                    $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortyfive') == 0) {
                    $colec['C' . $containersEq->code] = HelperAll::validatorErrorWitdColor($failrate->fortyfive);
                    $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                }
            }
        } else {
            foreach ($equiments->containers as $containersEq) {
                if (array_key_exists('C' . $containersEq->code, $containers)) {
                    $colec['C' . $containersEq->code] = HelperAll::validatorErrorWitdColor($containers['C' . $containersEq->code]);
                    $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                } else {
                    $colec['C' . $containersEq->code] = ['value' => 0, 'color' => 'green'];
                    $colec['C' . $containersEq->code]['name'] = $containersEq->code;
                }
            }
        }

        $failrates = [
            'rate_id' => $failrate->id,
            'contract_id' => $contract->id,
            'equiment_id' => $equiment_id,
            'origin_port' => $originAIn,
            'destiny_port' => $destinationAIn,
            'carrierAIn' => $carrAIn,
            'containers' => $colec,
            'currencyAIn' => $currency_val,
            'transit_time' => $failrate->transit_time,
            'via' => $failrate->via,
            'schedueleT' => $schedueleTA,
            'classtransittime' => $classtransittime,
            'classvia' => $classvia,
            'classscheduleT' => $classscheduleT,
            'classorigin' => $classdorigin,
            'classdestiny' => $classddestination,
            'classcarrier' => $classcarrier,
            'classcurrency' => $classcurrency,
        ];

        $pruebacurre = '';
        $carrAIn = '';
        //dd($failrates);
        //return view('importation.Body-Modals.FailEditRates',compact('failrates','schedulesT','harbor','carrier','currency','equiment_id'));
        return view('importationV2.Fcl.Body-Modals.failedRate', compact('failrates', 'schedulesT', 'harbor', 'carrier', 'currency', 'equiment_id'));
    }

    public function CreateRates(Request $request, $id)
    {
        //dd($request->all(),$request->input('C20DV'));
        $origins = $request->origin_port;
        $destinis = $request->destiny_port;
        $equiment_id = $request->equiment_id;
        $twuenty = 0;
        $forty = 0;
        $fortyhc = 0;
        $fortynor = 0;
        $fortyfive = 0;
        $containers = null;
        $columns_rt_ident = [];
        $equiments = GroupContainer::with('containers')->find($equiment_id);
        $colec = [];
        if ($equiment_id == 1) {
            $contenedores_rt = Container::where('gp_container_id', $equiment_id)->where('options->column', true)->get();
            foreach ($contenedores_rt as $conten_rt) {
                $conten_rt->options = json_decode($conten_rt->options);
                $columns_rt_ident[$conten_rt->code] = $conten_rt->options->column_name;
            }
            foreach ($equiments->containers as $containersEq) {
                if (strnatcasecmp($columns_rt_ident[$containersEq->code], 'twuenty') == 0) {
                    $twuenty = floatval($request->input('C' . $containersEq->code));
                } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'forty') == 0) {
                    $forty = floatval($request->input('C' . $containersEq->code));
                } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortyhc') == 0) {
                    $fortyhc = floatval($request->input('C' . $containersEq->code));
                } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortynor') == 0) {
                    $fortynor = floatval($request->input('C' . $containersEq->code));
                } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortyfive') == 0) {
                    $fortyfive = floatval($request->input('C' . $containersEq->code));
                }
            }
        } else {
            foreach ($equiments->containers as $containersEq) {
                $colec['C' . $containersEq->code] = '' . floatval($request->input('C' . $containersEq->code));
            }
        }
        $containers = json_encode($colec);
        //dd($twuenty,$forty,$fortyhc,$fortynor,$fortyfive,$containers);

        foreach ($origins as $origin) {
            foreach ($destinis as $destiny) {
                if ($origin != $destiny) {
                    $exists_rate = Rate::where('origin_port', $origin)
                        ->where('destiny_port', $destiny)
                        ->where('carrier_id', $request->carrier_id)
                        ->where('contract_id', $request->contract_id)
                        ->where('twuenty', $twuenty)
                        ->where('forty', $forty)
                        ->where('fortyhc', $fortyhc)
                        ->where('fortynor', $fortynor)
                        ->where('fortyfive', $fortyfive)
                        ->where('containers', $containers)
                        ->where('currency_id', $request->currency_id)
                        ->where('schedule_type_id', $request->scheduleT)
                        ->where('transit_time', $request->transit_time)
                        ->where('via', $request->via)
                        ->first();
                    if (count((array) $exists_rate) == 0) {
                        $return = Rate::create([
                            'origin_port' => $origin,
                            'destiny_port' => $destiny,
                            'carrier_id' => $request->carrier_id,
                            'contract_id' => $request->contract_id,
                            'twuenty' => $twuenty,
                            'forty' => $forty,
                            'fortyhc' => $fortyhc,
                            'fortynor' => $fortynor,
                            'fortyfive' => $fortyfive,
                            'containers' => $containers,
                            'currency_id' => $request->currency_id,
                            'schedule_type_id' => $request->scheduleT,
                            'transit_time' => $request->transit_time,
                            'via' => $request->via,
                        ]);
                    }
                }
            }
        }

        $failrate = FailRate::find($id);
        if (is_null($failrate)) {
            return redirect()->route('Failed.Developer.For.Contracts', [$request->contract_id, $request->nameTab]);
        } else {
            $failrate->forceDelete();
            $request->session()->flash('message.content', 'Updated Rate');
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            return redirect()->route('Failed.Developer.For.Contracts', [$request->contract_id, $request->nameTab]);
        }
    }

    public function UpdateRatesD(Request $request, $id)
    {
        //dd($request->all());

        $equiment_id = $request->equiment_id;
        $twuenty = 0;
        $forty = 0;
        $fortyhc = 0;
        $fortynor = 0;
        $fortyfive = 0;
        $containers = null;
        $columns_rt_ident = [];
        $equiments = GroupContainer::with('containers')->find($equiment_id);
        $colec = [];
        if ($equiment_id == 1) {
            $contenedores_rt = Container::where('gp_container_id', $equiment_id)->where('options->column', true)->get();
            foreach ($contenedores_rt as $conten_rt) {
                $conten_rt->options = json_decode($conten_rt->options);
                $columns_rt_ident[$conten_rt->code] = $conten_rt->options->column_name;
            }
            foreach ($equiments->containers as $containersEq) {
                if (strnatcasecmp($columns_rt_ident[$containersEq->code], 'twuenty') == 0) {
                    $twuenty = floatval($request->input('C' . $containersEq->code));
                } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'forty') == 0) {
                    $forty = floatval($request->input('C' . $containersEq->code));
                } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortyhc') == 0) {
                    $fortyhc = floatval($request->input('C' . $containersEq->code));
                } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortynor') == 0) {
                    $fortynor = floatval($request->input('C' . $containersEq->code));
                } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortyfive') == 0) {
                    $fortyfive = floatval($request->input('C' . $containersEq->code));
                }
            }
        } else {
            foreach ($equiments->containers as $containersEq) {
                $colec['C' . $containersEq->code] = '' . floatval($request->input('C' . $containersEq->code));
            }
        }
        $containers = json_encode($colec);
        //dd($twuenty,$forty,$fortyhc,$fortynor,$fortyfive,$containers);

        $rate = Rate::find($id);
        $rate->origin_port = $request->origin_id;
        $rate->destiny_port = $request->destiny_id;
        $rate->carrier_id = $request->carrier_id;
        $rate->contract_id = $request->contract_id;
        $rate->currency_id = $request->currency_id;
        $rate->twuenty = $twuenty;
        $rate->forty = $forty;
        $rate->fortyhc = $fortyhc;
        $rate->fortynor = $fortynor;
        $rate->fortyfive = $fortyfive;
        $rate->containers = $containers;
        $rate->schedule_type_id = $request->scheduleT;
        $rate->transit_time = (int) $request->transit_time;
        $rate->via = $request->via;
        $rate->update();

        $request->session()->flash('message.content', 'Updated Rate');
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $tab = 0;

        return redirect()->route('Failed.Developer.For.Contracts', [$request->contract_id, $request->nameTab]);
    }

    public function DestroyRatesF($id)
    {
        try {
            $failRate = FailRate::find($id);
            $failRate->forceDelete();

            return 1;
        } catch (\Exception $e) {
            return 2;
        }
    }

    public function DestroyRatesG($id)
    {
        try {
            $Rate = Rate::find($id);
            $Rate->forceDelete();

            return 1;
        } catch (\Exception $e) {
            return 2;
        }
    }

    // Surcharge ------------------------------------------------------------------------
    public function FailedSurchargeDeveloper($id, $tab)
    {
        //$id se refiere al id del contracto
        $countfailsurcharge = FailSurCharge::where('contract_id', '=', $id)->count();
        $countgoodsurcharge = LocalCharge::where('contract_id', '=', $id)->count();
        $contract = Contract::find($id);

        return view('importation.SurchargersFailOF', compact('countfailsurcharge', 'contract', 'countgoodsurcharge', 'id', 'tab'));
    }

    public function checkSurcharges(Request $request, $id)
    {
        $contract = Contract::with('carriers', 'direction', 'companyUser', 'direction', 'carriers.carrier', 'gpContainer')->find($id);
        $carrier_contract = $contract->carriers->pluck('carrier_id');
        $contract->result_validator = json_encode([]);
        $contract->validator = false;
        $contract->update();
        $direction_array = null;

        $locals = LocalCharge::with('localcharcarriers.carrier', 'surcharge')->where('contract_id', $id)->get();
        if (count($locals) > 100) {
            $data_job = ['id' => $id];
            if (env('APP_VIEW') == 'operaciones') {
                ValidatorSurchargeJob::dispatch($data_job)->onQueue('operaciones');
            } else {
                ValidatorSurchargeJob::dispatch($data_job);
            }
        } else {
            if ($contract->direction_id == 3) {
                $direction_array = [1, 2, 3];
            } else {
                $direction_array = [$contract->direction_id, 3];
            }
            $surcharge_detail = MasterSurcharge::where('group_container_id', $contract->gp_container_id)
                ->orWhere('group_container_id', null)
                ->with('surcharge')
                ->get();
            $surcharge_detail = $surcharge_detail->whereIn('carrier_id', $carrier_contract);
            $surcharge_detail = $surcharge_detail->whereIn('direction_id', $direction_array);
            //dd($direction_array,$surcharge_detail);
            $local_found_in_sur_mast = collect([]);
            $local_not_found_in_sur_mast = collect([]);
            $carrier_not_content_contract = collect([]);
            $surcharge_not_registred = collect([]);
            $surcharge_duplicated = collect([]);
            $data_final = collect();
            //dd($surcharge_detail,$locals,$contract,$carrier_contract);
            foreach ($locals as $local) {
                $surchargersFined = PrvSurchargers::get_single_surcharger($local->surcharge->name);
                //dd($surchargersFined);
                if ($local->typedestiny_id == 3) {
                    $type_destiny_array = [1, 2, 3];
                } else {
                    $type_destiny_array = [$local->typedestiny_id, 3];
                }

                if ($surchargersFined['boolean'] == true && $surchargersFined['count'] == 1) {
                    $filtered_carrier = $local->localcharcarriers->whereNotIn('carrier_id', $carrier_contract);
                    if (count($filtered_carrier) >= 1) {
                        // Agregar la excepcion de que hay un carrier en el local no registrado en el contracto
                        //dd($filtered_carrier->pluck('carrier')->pluck('name')->implode(' | '));
                    }

                    $master_surcharge_fineds = MasterSurcharge::where('surcharge_id', $surchargersFined['data'])
                        //->whereIn('direction_id',$direction_array)
                        //->whereIn('typedestiny_id',$type_destiny_array)
                        ->where('group_container_id', $contract->gp_container_id)
                        ->orWhere('group_container_id', null)
                        ->get();

                    $master_surcharge_fineds = $master_surcharge_fineds->whereIn('carrier_id', $local->localcharcarriers->pluck('carrier_id'));
                    $master_surcharge_fineds = $master_surcharge_fineds->whereIn('direction_id', $direction_array);
                    $master_surcharge_fineds = $master_surcharge_fineds->whereIn('typedestiny_id', $type_destiny_array);

                    //dd($local,$local->localcharcarriers->pluck('carrier_id'), $surchargersFined['data'],$master_surcharge_fineds,$contract->direction_id );
                    $local_collated = false;
                    foreach ($master_surcharge_fineds as $master_surcharge_fined) {
                        if ($master_surcharge_fined->calculationtype_id == $local->calculationtype_id) {
                            //El calculation T. del Reacargo es igual al del Master Surcharge
                            //Agregar a lista exitosa 1
                            $local_collated = true;
                            //dd('//Agregar a lista exitosa 1',$surcharge_detail,$local_collated,$master_surcharge_fined,$local);
                            $local_found_in_sur_mast->push($master_surcharge_fined->id);
                            break;
                        } else {
                            //No es igual el caculation type
                            //dd($master_surcharge_fined,'//No es igual el caculation type');
                            $calculationTypeContent = CalculationTypeContent::where('calculationtype_base_id', $master_surcharge_fined->calculationtype_id)
                                ->where('calculationtype_content_id', $local->calculationtype_id)
                                ->get();
                            if (count($calculationTypeContent) >= 1) {
                                //Agregar a lista exitosa 2
                                $local_collated = true;
                                //dd('//Agregar a lista exitosa 2',$local_collated,$master_surcharge_fined,$local);
                                $local_found_in_sur_mast->push($master_surcharge_fined->id);
                                break;
                            } else {
                                $calculationTypeContent = null;
                                $calculationTypeContent = CalculationTypeContent::where('calculationtype_content_id', $master_surcharge_fined->calculationtype_id)
                                    ->where('calculationtype_base_id', $local->calculationtype_id)
                                    ->get();
                                if (count($calculationTypeContent) >= 1) {
                                    //Agregar a lista exitosa 3
                                    $local_collated = true;
                                    //dd('//Agregar a lista exitosa 3',$local_collated,$master_surcharge_fined,$local);
                                    $local_found_in_sur_mast->push($master_surcharge_fined->id);
                                    break;
                                } else {
                                    //dd('//No coincide');
                                }
                            }
                        }
                    }
                    if ($local_collated) {
                        //dd('recargo de este local fue encontrado');
                    } else {
                        // informar que no encontro el recargo. agregar a master surchar
                        //dd('recargo de este local no fue encontrado');
                        $local_not_found_in_sur_mast->push($local->surcharge_id);
                    }
                } else {
                    if ($surchargersFined['count'] == 0) {
                        // No encontro el recargo en variaciones de Surcharge list
                        $surcharge_not_registred->push($local->surcharge_id);
                    } elseif ($surchargersFined['count'] >= 1) {
                        // Encontro mas de un Surcharge para una variacion. Listar Error de ID semejantes
                        $surcharge_duplicated->push($surchargersFined['data']);
                    }
                }
            }

            // Se listan en Verde
            $surcharMas_locals_found = $surcharge_detail->whereIn('id', $local_found_in_sur_mast->unique());
            // Se pinta en Rojo
            $surcharMas_locals_not_found = $surcharge_detail->whereNotIn('id', $local_found_in_sur_mast->unique());

            //        dd('Surcharge Detaills - Surcharge de Locals encontrados   //  Surcharge_master_id',
            //           $surcharMas_locals_found->pluck('id'),
            //           'Surcharge Detaills - Surcharge de Locals NO encontrados //  Surcharge_master_id',
            //           $surcharMas_locals_not_found->pluck('id'),
            //           'Surcharge de Locals No encontrados - Agregar a Surcharge Detaills //  Surcharge_id',
            //           $local_not_found_in_sur_mast->unique(),
            //           'Surcharge No Registrado en variacion //  surcharge_id',
            //           $surcharge_not_registred->unique(),
            //           'Surcharge Duplicado en variacion //  surcharge_id',
            //           $surcharge_duplicated->unique(),
            //           'Surcharge Master Listado General',
            //           $surcharge_detail->pluck('id')
            //          );
            $array = [];
            $array['surcharMas_locals_found'] = [];
            $array['surcharMas_locals_not_found'] = [];
            $array['local_not_found_in_sur_mast'] = [];
            $array['surcharge_not_registred'] = [];
            $array['surcharge_duplicated'] = [];

            $surcharMas_locals_found->load('direction', 'calculationtype', 'typedestiny');
            foreach ($surcharMas_locals_found as $surcharMas_local_found) {
                //dd($surcharMas_local_not_found);
                array_push(
                    $array['surcharMas_locals_found'],
                    $surcharMas_local_found->surcharge->name . ' ____ ' .
                        $surcharMas_local_found->direction->name . ' ____ ' .
                        $surcharMas_local_found->calculationtype->name . ' ____ ' .
                        $surcharMas_local_found->typedestiny->description
                );
            }
            $surcharMas_locals_not_found->load('direction', 'calculationtype', 'typedestiny');
            foreach ($surcharMas_locals_not_found as $surcharMas_local_not_found) {
                //dd($surcharMas_local_not_found);
                array_push(
                    $array['surcharMas_locals_not_found'],
                    $surcharMas_local_not_found->surcharge->name . ' ____ ' .
                        $surcharMas_local_not_found->direction->name . ' ____ ' .
                        $surcharMas_local_not_found->calculationtype->name . ' ____ ' .
                        $surcharMas_local_not_found->typedestiny->description
                );
            }

            foreach ($local_not_found_in_sur_mast->unique() as $local_surch) {
                $surchar_name = Surcharge::find($local_surch);
                array_push($array['local_not_found_in_sur_mast'], $surchar_name->name);
            }

            foreach ($surcharge_not_registred->unique() as $surch_not_rg) {
                $surchar_name = Surcharge::find($surch_not_rg);
                array_push($array['surcharge_not_registred'], $surchar_name->name);
            }

            foreach ($surcharge_duplicated->unique() as $surch_dp) {
                array_push($array['surcharge_duplicated'], $surch_dp);
            }

            $contract->result_validator = json_encode($array);
            $contract->validator = true;
            $contract->update();

            $data = json_decode($contract->result_validator, true);
            //dd($data);
            return view('RequestV2.Fcl.validator', compact('data', 'contract', 'id'));
        }

        $request->session()->flash('message.nivel', 'warning');
        $request->session()->flash('message.content', 'Your background validation is being processed. In minutes you can review');

        return redirect()->route('RequestFcl.index');
    }

    public function showValidatorSurcharge(Request $request, $id)
    {
        $contract = Contract::with('carriers', 'direction', 'companyUser', 'direction', 'carriers.carrier', 'gpContainer')->find($id);
        if ($contract->validator == true) {
            $data = json_decode($contract->result_validator, true);

            return view('RequestV2.Fcl.validator', compact('data', 'contract', 'id'));
        } else {
            $request->session()->flash('message.nivel', 'warning');
            $request->session()->flash('message.content', 'Your background validation is being processed. In minutes you can review');

            return redirect()->route('RequestFcl.index');
        }
        //dd($data);
    }

    public function EditSurchargersGood($id)
    {
        $countries = Country::pluck('name', 'id');

        $typedestiny = TypeDestiny::all()->pluck('description', 'id');
        $carrierSelect = Carrier::all()->pluck('name', 'id');
        $harbor = Harbor::all()->pluck('display_name', 'id');
        $currency = Currency::all()->pluck('alphacode', 'id');
        $calculationtypeselect = CalculationType::all();
        $calculationtypeselect = $calculationtypeselect->map(function ($item, $key) {
            $item->setAttribute('options_decode', (!empty($item->options)) ? json_decode($item->options, true) : []);
            return $item;
        });

        $goodsurcharges = LocalCharge::with(
            'currency',
            'calculationtype',
            'surcharge',
            'typedestiny',
            'localcharcarriers.carrier',
            'localcharports.portOrig',
            'localcharports.portDest',
            'localcharcountries.countryOrig',
            'localcharcountries.countryDest',
            'overweight_ranges'
        )->find($id);
        $is_ow_limits = !$goodsurcharges->overweight_ranges->isEmpty();
        $limits = ($is_ow_limits) ? $goodsurcharges->overweight_ranges->first() : ['lower_limit' => null, 'upper_limit' => null];
        $surchargeSelect = Surcharge::where('company_user_id', '=', $goodsurcharges->contract->company_user_id)->pluck('name', 'id');
        //dd($goodsurcharges,$is_ow_limits,$limits);
        return view('importationV2.Fcl.Body-Modals.GoodEditSurcharge', compact(
            'harbor',
            'currency',
            'countries',
            'typedestiny',
            'carrierSelect',
            'goodsurcharges',
            'surchargeSelect',
            'calculationtypeselect',
            'is_ow_limits',
            'limits'
        ));
    }
    /////lllalalala
    public function EditSurchargersFail($id)
    {

        $countries = Country::pluck('name', 'id');
        $typedestiny = TypeDestiny::pluck('description', 'id');
        $carrierSelect = Carrier::pluck('name', 'id');
        $harbor = Harbor::pluck('display_name', 'id');
        $currency = Currency::pluck('alphacode', 'id');
        $calculationtypeselect = CalculationType::all();
        $calculationtypeselect = $calculationtypeselect->map(function ($item, $key) {
            $item->setAttribute('options_decode', (!empty($item->options)) ? json_decode($item->options, true) : []);
            return $item;
        });
        $failsurcharge = FailSurCharge::find($id);
        $failsurcharge->load('contract', 'fail_overweight_ranges');
        $surchargeSelect = Surcharge::where('company_user_id', '=', $failsurcharge->contract->company_user_id)->pluck('name', 'id');
        $is_ow_limits = $failsurcharge->fail_overweight_ranges->isEmpty();
        $differentiator = $failsurcharge->differentiator;

        $classdorigin = 'color:green';
        $classddestination = 'color:green';
        $classtypedestiny = 'color:green';
        $classcarrier = 'color:green';
        $classsurcharger = 'color:green';
        $classcalculationtype = 'color:green';
        $classammount = 'color:green';
        $classcurrency = 'color:green';
        $classupperlimit = 'color:green';
        $classlowerlimit = 'color:green';
        $surchargeA = explode('_', $failsurcharge['surcharge_id']);
        $originA = explode('_', $failsurcharge['port_orig']);
        $destinationA = explode('_', $failsurcharge['port_dest']);
        $calculationtypeA = explode('_', $failsurcharge['calculationtype_id']);
        $ammountA = explode('_', $failsurcharge['ammount']);
        $currencyA = explode('_', $failsurcharge['currency_id']);
        $carrierA = explode('_', $failsurcharge['carrier_id']);
        $typedestinyA = explode('_', $failsurcharge['typedestiny_id']);
        //dd($failsurcharge->fail_overweight_ranges);
        if (!$is_ow_limits) {
            $lower_limitA = explode('_', $failsurcharge->fail_overweight_ranges->first()->lower_limit);
            $upper_limitA = explode('_', $failsurcharge->fail_overweight_ranges->first()->upper_limit);
        } else {
            $lower_limitA = [0];
            $upper_limitA = [0];
        }

        if (count($lower_limitA) > 1) {
            $lower_limitA = $lower_limitA[0] . ' (error)';
            $classlowerlimit = 'color:red';
        } else {
            $lower_limitA = $lower_limitA[0];
        }

        if (count($upper_limitA) > 1) {
            $upper_limitA = $upper_limitA[0] . ' (error)';
            $classupperlimit = 'color:red';
        } else {
            $upper_limitA = $upper_limitA[0];
        }
        // -------------- ORIGIN -------------------------------------------------------------

        if ($failsurcharge->differentiator == 1) {
            $originOb = PrvHarbor::get_harbor($originA[0]);
        } elseif ($failsurcharge->differentiator == 2) {
            $originOb = PrvHarbor::get_country($originA[0]);
        }

        if ($originOb['boolean']) {
            if ($failsurcharge->differentiator == 1) {
                $originA = $originOb['puerto'];
            } else {
                $originA = $originOb['country'];
            }
        } else {
            $originA = null;
            $classdorigin = 'color:red';
        }


        // -------------- DESTINATION --------------------------------------------------------

        if ($failsurcharge->differentiator == 1) {
            $destinationOb = PrvHarbor::get_harbor($destinationA[0]);
        } elseif ($failsurcharge->differentiator == 2) {
            $destinationOb = PrvHarbor::get_country($destinationA[0]);
        }
        if ($destinationOb['boolean']) {
            if ($failsurcharge->differentiator == 1) {
                $destinationA = $destinationOb['puerto'];
            } else {
                $destinationA = $destinationOb['country'];
            }
        } else {
            $destinationA = null;
            $classddestination = 'color:red';
        }


        // -------------- SURCHARGE ....-----------------------------------------------------
        $surchargeOb = Surcharge::where('name', '=', $surchargeA[0])->where('company_user_id', '=', $failsurcharge->contract->company_user_id)->first();
        $surcharAin = $surchargeOb['id'];
        $surchargeC = count($surchargeA);
        if ($surchargeC <= 1) {
            //$surchargeA = $surchargeA[0];
        } else {
            //$surchargeA         = $surchargeA[0].' (error)';
            $classsurcharger = 'color:red';
        }

        // -------------- CARRIER -----------------------------------------------------------
        $carrierOb = Carrier::where('name', '=', $carrierA[0])->first();
        $carrAIn = $carrierOb['id'];
        $carrierC = count($carrierA);
        if ($carrierC <= 1) {
            //$carrierA = $carrierA[0];
        } else {
            //$carrierA       = $carrierA[0].' (error)';
            $classcarrier = 'color:red';
        }

        // -------------- CALCULATION TYPE --------------------------------------------------
        $calculationtypeOb = CalculationType::where('name', '=', $calculationtypeA[0])->first();
        $calculationtypeAIn = $calculationtypeOb['id'];
        $calculationtypeC = count($calculationtypeA);
        if ($calculationtypeC <= 1) {
            //$calculationtypeA = $calculationtypeA[0];
        } else {
            //$calculationtypeA       = $calculationtypeA[0].' (error)';
            $classcalculationtype = 'color:red';
        }

        // -------------- AMMOUNT -----------------------------------------------------------
        $ammountC = count($ammountA);
        if ($ammountC <= 1) {
            $ammountA = $failsurcharge['ammount'];
        } else {
            $ammountA = $ammountA[0] . ' (error)';
            $classammount = 'color:red';
        }

        // -------------- CURRENCY ----------------------------------------------------------
        $currencyOb = Currency::where('alphacode', '=', $currencyA[0])->first();
        $currencyAIn = $currencyOb['id'];
        $currencyC = count($currencyA);
        if ($currencyC <= 1) {
            // $currencyA = $currencyA[0];
        } else {
            $currencyA = $currencyA[0] . ' (error)';
            $classcurrency = 'color:red';
        }

        // -------------- TYPE DESTINY -----------------------------------------------------
        //dd($failsurcharge['typedestiny_id']);
        $typedestinyobj = TypeDestiny::where('description', $typedestinyA[0])->first();
        if (count($typedestinyA) <= 1) {
            $typedestinyLB = $typedestinyobj['id'];
        } else {
            $typedestinyLB = $typedestinyA[0] . ' (error)';
            $classtypedestiny = 'color:red';
        }

        ////////////////////////////////////////////////////////////////////////////////////
        $failsurchargeArre = [
            'id' => $failsurcharge['id'],
            'surcharge' => $surcharAin,
            'origin_port' => $originA,
            'destiny_port' => $destinationA,
            'carrier' => $carrAIn,
            'contract_id' => $failsurcharge['contract_id'],
            'typedestiny' => $typedestinyLB,
            'ammount' => $ammountA,
            'calculationtype' => $calculationtypeAIn,
            'currency' => $currencyAIn,
            'lower_limit' => $lower_limitA,
            'upper_limit' => $upper_limitA,
            'is_ow_limits' => $is_ow_limits,
            'classlowerlimit' => $classlowerlimit,
            'classupperlimit' => $classupperlimit,
            'classsurcharge' => $classsurcharger,
            'classorigin' => $classdorigin,
            'classdestiny' => $classddestination,
            'classtypedestiny' => $classtypedestiny,
            'classcarrier' => $classcarrier,
            'classcalculationtype' => $classcalculationtype,
            'classammount' => $classammount,
            'classcurrency' => $classcurrency,
        ];
        //dd($arreglo);

        //dd($failsurchargeArre);
        return view('importationV2.Fcl.Body-Modals.FailEditSurcharge', compact(
            'failsurchargeArre',
            'harbor',
            'carrierSelect',
            'currency',
            'countries',
            'surchargeSelect',
            'typedestiny',
            'differentiator',
            'calculationtypeselect'
        ));
    }

    public function CreateSurchargers(Request $request, $id)
    {
        //dd($request->all());

        $surchargeVar = $request->surcharge_id;
        $typedestinyVar = $request->changetype;
        $carrierVarArr = $request->carrier_id;
        $calculationtypeVar = $request->calculationtype_id;
        $ammountVar = floatval($request->ammount);
        $currencyVar = $request->currency_id;
        $contractVar = $request->contract_id;
        $typerate = $request->typeroute;
        $lower_limit = $request->lower_limit;
        $upper_limit = $request->upper_limit;

        $failSurcharge = new FailSurCharge();
        $failSurcharge = FailSurCharge::find($id);
        $SurchargeId = null;
        $SurchargeId = LocalCharge::where('surcharge_id', $surchargeVar)
            ->where('typedestiny_id', $typedestinyVar)
            ->where('contract_id', $contractVar)
            ->where('calculationtype_id', $calculationtypeVar)
            ->where('ammount', $ammountVar)
            ->where('currency_id', $currencyVar);
        if ($request->is_ow_limits == true) {
            $SurchargeId->whereHas('overweight_ranges', function ($query) use ($lower_limit, $upper_limit, $ammountVar) {
                $query->where('lower_limit', $lower_limit)
                    ->where('upper_limit', $upper_limit)
                    ->where('amount', $ammountVar)
                    ->where('model_type', 'App\\LocalCharge');
            });
        }
        $SurchargeId = $SurchargeId->get();
        if ($SurchargeId->isEmpty()) {
            $SurchargeId = LocalCharge::create([
                'surcharge_id' => $surchargeVar,
                'typedestiny_id' => $typedestinyVar,
                'contract_id' => $contractVar,
                'calculationtype_id' => $calculationtypeVar,
                'ammount' => $ammountVar,
                'currency_id' => $currencyVar,
            ]);
            // ---------------------- Limits OW ------------------------------------------
            if ($request->is_ow_limits == true) {
                OverweightRange::create([
                    'lower_limit' => $lower_limit,
                    'upper_limit' => $upper_limit,
                    'amount' => $ammountVar,
                    'model_id' => $SurchargeId->id,
                    'model_type' => 'App\\LocalCharge',
                ]);
            }
        } else {
            $SurchargeId = $SurchargeId->first();
        }

        if ($typerate == 'port') {
            $originVarArr = $request->port_origlocal;
            $destinationVarArr = $request->port_destlocal;
            foreach ($originVarArr as $originVar) {
                foreach ($destinationVarArr as $destinationVar) {
                    $existsLP = null;
                    $existsLP = LocalCharPort::where('port_orig', $originVar)
                        ->where('port_dest', $destinationVar)
                        ->where('localcharge_id', $SurchargeId->id)
                        ->first();
                    if (empty($existsLP)) {
                        LocalCharPort::create([
                            'port_orig' => $originVar,
                            'port_dest' => $destinationVar,
                            'localcharge_id' => $SurchargeId->id,
                        ]); //
                    }
                }
            }
        } elseif ($typerate == 'country') {
            $originVarCounArr = $request->country_orig;
            $destinationCounVarArr = $request->country_dest;

            foreach ($originVarCounArr as $originCounVar) {
                foreach ($destinationCounVarArr as $destinationCounVar) {
                    $existsLC = null;
                    $existsLC = LocalCharCountry::where('country_orig', $originCounVar)
                        ->where('country_dest', $destinationCounVar)
                        ->where('localcharge_id', $SurchargeId->id)
                        ->first();
                    if (empty($existsLC)) {
                        LocalCharCountry::create([
                            'country_orig' => $originCounVar,
                            'country_dest' => $destinationCounVar,
                            'localcharge_id' => $SurchargeId->id,
                        ]); //
                    }
                }
            }
        }

        foreach ($carrierVarArr as $carrierVar) {
            $localcharcarriersV = null;
            $localcharcarriersV = LocalCharCarrier::where('carrier_id', $carrierVar)->where('localcharge_id', $SurchargeId->id)->get();
            if (count($localcharcarriersV) == 0) {
                LocalCharCarrier::create([
                    'carrier_id' => $carrierVar,
                    'localcharge_id' => $SurchargeId->id,
                ]);
            }
        }
        if (is_null($failSurcharge)) {
            return redirect()->route('Failed.Developer.For.Contracts', [$request->contract_id, $request->nameTab]);
        } else {
            $failSurcharge->forceDelete();
            $request->session()->flash('message.content', 'Surcharge Updated');
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            return redirect()->route('Failed.Developer.For.Contracts', [$request->contract_id, $request->nameTab]);
        }
    }

    public function UpdateSurchargersD(Request $request, $id)
    {
        $surchargeVar = $request->surcharge_id; // id de la columna surchage_id
        $contractVar = $request->contract_id;
        $typedestinyVar = $request->changetype;
        $calculationtypeVar = $request->calculationtype_id;
        $ammountVar = floatval($request->ammount);
        $currencyVar = $request->currency_id;
        $carrierVarArr = $request->carrier_id;
        $typerate = $request->typeroute;
        $is_ow_limits = $request->is_ow_limits;
        $lower_limit = $request->lower_limit;
        $upper_limit = $request->upper_limit;

        $SurchargeId = new LocalCharge();
        $SurchargeId = LocalCharge::find($id);
        $SurchargeId->surcharge_id = $surchargeVar;
        $SurchargeId->typedestiny_id = $typedestinyVar;
        $SurchargeId->contract_id = $contractVar;
        $SurchargeId->calculationtype_id = $calculationtypeVar;
        $SurchargeId->ammount = $ammountVar;
        $SurchargeId->currency_id = $currencyVar;
        $SurchargeId->update();

        LocalCharPort::where('localcharge_id', '=', $SurchargeId->id)->forceDelete();
        LocalCharCountry::where('localcharge_id', '=', $SurchargeId->id)->forceDelete();

        LocalCharCarrier::where('localcharge_id', '=', $SurchargeId->id)->forceDelete();
        foreach ($carrierVarArr as $carrierVar) {
            // $localcharcarriersV = null;
            $localcharcarriersV = LocalCharCarrier::where('carrier_id', $carrierVar)->where('localcharge_id', $SurchargeId->id)->get();
            if (count($localcharcarriersV) == 0) {
                LocalCharCarrier::create([
                    'carrier_id' => $carrierVar,
                    'localcharge_id' => $SurchargeId->id,
                ]); //
            }
        }

        if ($typerate == 'port') {
            $originVarArr = $request->port_origlocal;
            $destinationVarArr = $request->port_destlocal;
            foreach ($originVarArr as $originVar) {
                foreach ($destinationVarArr as $destinationVar) {
                    LocalCharPort::create([
                        'port_orig' => $originVar,
                        'port_dest' => $destinationVar,
                        'localcharge_id' => $SurchargeId->id,
                    ]); //
                }
            }
        } elseif ($typerate == 'country') {
            $originVarCounArr = $request->country_orig;
            $destinationCounVarArr = $request->country_dest;

            foreach ($originVarCounArr as $originCounVar) {
                foreach ($destinationCounVarArr as $destinationCounVar) {
                    LocalCharCountry::create([
                        'country_orig' => $originCounVar,
                        'country_dest' => $destinationCounVar,
                        'localcharge_id' => $SurchargeId->id,
                    ]); //
                }
            }
        }
        $SurchargeId = $SurchargeId->load('overweight_ranges');
        $ow_r = $SurchargeId->overweight_ranges;
        if ($is_ow_limits) {
            if ($ow_r->isEmpty()) {
                OverweightRange::create([
                    'lower_limit' => $lower_limit,
                    'upper_limit' => $upper_limit,
                    'amount' => $ammountVar,
                    'model_id' => $SurchargeId->id,
                    'model_type' => 'App\\LocalCharge',
                ]);
            } else {
                $ow_r = $ow_r->first();
                $ow_r->upper_limit = $upper_limit;
                $ow_r->lower_limit = $lower_limit;
                $ow_r->update();
            }
        } else {
            if (!$ow_r->isEmpty()) {
                $ow_r->map(function ($item, $key) {
                    $item->delete();
                });
            }
        }

        $request->session()->flash('message.content', 'Surcharge Updated');
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');

        return redirect()->route('Failed.Developer.For.Contracts', [$request->contract_id, $request->nameTab]);
    }

    public function DestroySurchargersF($id)
    {
        try {
            $failsurCharge = FailSurCharge::find($id);
            $failsurCharge->forceDelete();

            return 1;
        } catch (\Exception $e) {
            return 2;
        }
    }

    public function DestroySurchargersG($id)
    {
        try {
            $surchargers = LocalCharge::find($id);
            if (isset($surchargers)) {
                $surchargers->forceDelete();
            }
            return 1;
        } catch (\Exception $e) {
            return 2;
        }
    }

    //Datatable Rates Y Surchargers -----------------------------------------------------

    public function LoadDataTable($id, $selector, $type)
    {
        if (strnatcasecmp($type, 'rates') == 0) {
            //$id se refiere al id del contracto
            $objharbor = new Harbor();
            $objcurrency = new Currency();
            $objcarrier = new Carrier();
            $failrates = collect([]);
            $contract = Contract::find($id);
            if (empty($contract->gp_container_id)) {
                $equiment_id = 1;
            } else {
                $equiment_id = $contract->gp_container_id;
            }
            $equiments = GroupContainer::with('containers')->find($equiment_id);
            $columns_rt_ident = [];
            if ($equiment_id == 1) {
                $contenedores_rt = Container::where('gp_container_id', $equiment_id)->where('options->column', true)->get();
                foreach ($contenedores_rt as $conten_rt) {
                    $conten_rt->options = json_decode($conten_rt->options);
                    $columns_rt_ident[$conten_rt->code] = $conten_rt->options->column_name;
                }
            }

            if ($selector == 1) {
                $failratesFor = DB::select('call  proc_fail_rates_fcl(' . $id . ')');
                ///$failratesFor   = DB::select('call  proc_fail_rates_fcl('.$id.')');
                //$failratesFor = FailRate::where('contract_id','=',$id)->get();
                foreach ($failratesFor as $failrate) {
                    $carrAIn;
                    $pruebacurre = '';
                    $containers = null;
                    $originA = explode('_', $failrate->origin_port);
                    $destinationA = explode('_', $failrate->destiny_port);
                    $carrierA = explode('_', $failrate->carrier_id);
                    $currencyA = explode('_', $failrate->currency_id);
                    $containers = json_decode($failrate->containers, true);

                    $originOb = PrvHarbor::get_harbor($originA[0]);
                    if ($originOb['boolean']) {
                        $originA = Harbor::find($originOb['puerto']);
                        $originA = $originA->name;
                    } else {
                        $originA = $originA[0] . ' (error)';
                        $classdorigin = 'color:red';
                    }
                    // DESTINY ------------------------------------------------------------------------------
                    $destinationOb = PrvHarbor::get_harbor($destinationA[0]);
                    if ($destinationOb['boolean']) {
                        $destinationOb = Harbor::find($destinationOb['puerto']);
                        $destinationA = $destinationOb->name;
                    } else {
                        $destinationA = $destinationA[0] . ' (error)';
                    }

                    $carrierOb = Carrier::where('name', '=', $carrierA[0])->first();
                    $carrierC = count($carrierA);
                    if ($carrierC <= 1) {
                        //dd($carrierAIn);
                        $carrierA = $carrierA[0];
                    } else {
                        $carrierA = $carrierA[0] . ' (error)';
                    }

                    $currencyC = count($currencyA);
                    if ($currencyC <= 1) {
                        $currenc = Currency::where('alphacode', '=', $currencyA[0])->orWhere('id', '=', $currencyA[0])->first();
                        $currencyA = $currenc['alphacode'];
                    } else {
                        $currencyA = $currencyA[0] . ' (error)';
                    }

                    $colec = [
                        'id' => $failrate->id,
                        'contract_id' => $id,
                        'origin' => $originA, //
                        'destiny' => $destinationA, //
                        'carrier' => $carrierA, //
                        'operation' => '1',
                    ];
                    if ($equiment_id == 1) {
                        foreach ($equiments->containers as $containersEq) {
                            if (strnatcasecmp($columns_rt_ident[$containersEq->code], 'twuenty') == 0) {
                                $colec['C' . $containersEq->code] = HelperAll::validatorError($failrate->twuenty);
                            } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'forty') == 0) {
                                $colec['C' . $containersEq->code] = HelperAll::validatorError($failrate->forty);
                            } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortyhc') == 0) {
                                $colec['C' . $containersEq->code] = HelperAll::validatorError($failrate->fortyhc);
                            } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortynor') == 0) {
                                $colec['C' . $containersEq->code] = HelperAll::validatorError($failrate->fortynor);
                            } elseif (strnatcasecmp($columns_rt_ident[$containersEq->code], 'fortyfive') == 0) {
                                $colec['C' . $containersEq->code] = HelperAll::validatorError($failrate->fortyfive);
                            }
                        }
                    } else {
                        foreach ($equiments->containers as $containersEq) {
                            if (array_key_exists('C' . $containersEq->code, $containers)) {
                                $colec['C' . $containersEq->code] = HelperAll::validatorError($containers['C' . $containersEq->code]);
                            } else {
                                $colec['C' . $containersEq->code] = 0;
                            }
                        }
                    }
                    $colec['currency'] = $currencyA;
                    //dd($colec,$equiments->containers,$containers,$failrate->id);

                    $failrates->push($colec);
                }

                return DataTables::of($failrates)->addColumn('action', function ($failrate) {
                    return '<a href="#" class="" onclick="showModalsavetorate(' . $failrate['id'] . ',' . $failrate['operation'] . ')"><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-FailRate" data-id-failrate="' . $failrate['id'] . '" class=""><i class="la la-trash"></i></a>';
                })
                    ->editColumn('id', '{{$id}}')->toJson();
            } elseif ($selector == 2) {
                $ratescol = PrvRates::get_rates($id);

                return DataTables::of($ratescol)
                    ->addColumn('action', function ($ratescol) {
                        return '
                <a href="#" onclick="showModalsavetorate(' . $ratescol['id'] . ',' . $ratescol['operation'] . ')" class=""><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-Rate" data-id-rate="' . $ratescol['id'] . '" class=""><i class="la la-trash"></i></a>';
                    })
                    ->editColumn('id', '{{$id}}')->toJson();
            }
        } else {
            if ($selector == 1) {
                /*$objharbor = new Harbor();
                $objcurrency = new Currency();
                $objcarrier = new Carrier();
                $objsurcharge = new Surcharge();
                $objtypedestiny = new TypeDestiny();
                $objCalculationType = new CalculationType();
                $typedestiny = $objtypedestiny->all()->pluck('description', 'id');
                $surchargeSelect = $objsurcharge->where('company_user_id', '=', \Auth::user()->company_user_id)->pluck('name', 'id');
                $carrierSelect = $objcarrier->all()->pluck('name', 'id');
                $harbor = $objharbor->all()->pluck('display_name', 'id');
                $currency = $objcurrency->all()->pluck('alphacode', 'id');
                $calculationtypeselect = $objCalculationType->all()->pluck('name', 'id');*/

                $failsurchargeS = DB::select('call  proc_fails_surchargers_fcl(' . $id . ')');
                //$failsurchargeS = FailSurCharge::where('contract_id','=',$id)->get();
                $failsurchargecoll = collect([]);
                foreach ($failsurchargeS as $failsurcharge) {
                    $classdorigin = 'color:green';
                    $classddestination = 'color:green';
                    $classtypedestiny = 'color:green';
                    $classcarrier = 'color:green';
                    $classsurcharger = 'color:green';
                    $classcalculationtype = 'color:green';
                    $classammount = 'color:green';
                    $classcurrency = 'color:green';
                    $surchargeA = explode('_', $failsurcharge->surcharge_id);
                    $originA = explode('_', $failsurcharge->port_orig);
                    $destinationA = explode('_', $failsurcharge->port_dest);
                    $calculationtypeA = explode('_', $failsurcharge->calculationtype_id);
                    $ammountA = explode('_', $failsurcharge->ammount);
                    $currencyA = explode('_', $failsurcharge->currency_id);
                    $carrierA = explode('_', $failsurcharge->carrier_id);
                    $typedestinyA = explode('_', $failsurcharge->typedestiny_id);
                    $lower_limitA = explode('_', $failsurcharge->lower_limit);
                    $upper_limitA = explode('_', $failsurcharge->upper_limit);

                    // -------------- AMMOUNT ------------------------------------------------------------

                    $lower_limit = (count($lower_limitA) <= 1) ? $failsurcharge->lower_limit : $lower_limitA[0] . ' (error)';
                    $upper_limit = (count($upper_limitA) <= 1) ? $failsurcharge->upper_limit : $upper_limitA[0] . ' (error)';
                    $lower_limit = (empty($lower_limit)) ? '-----' : $lower_limit;
                    $upper_limit = (empty($upper_limit)) ? '-----' : $upper_limit;
                    // -------------- ORIGIN -------------------------------------------------------------
                    if ($failsurcharge->differentiator == 1) {
                        $originOb = PrvHarbor::get_harbor($originA[0]);
                    } elseif ($failsurcharge->differentiator == 2) {
                        $originOb = PrvHarbor::get_country($originA[0]);
                    }

                    if ($originOb['boolean']) {
                        if ($failsurcharge->differentiator == 1) {
                            $originA = Harbor::find($originOb['puerto']);
                            $originA = $originA->name;
                        } else {
                            $originA = Country::find($originOb['country']);
                            $originA = $originA->name;
                        }
                    } else {
                        $originA = $originA[0] . ' (error)';
                        $classdorigin = 'color:red';
                    }

                    // -------------- DESTINY ------------------------------------------------------------
                    if ($failsurcharge->differentiator == 1) {
                        $destinationOb = PrvHarbor::get_harbor($destinationA[0]);
                    } elseif ($failsurcharge->differentiator == 2) {
                        $destinationOb = PrvHarbor::get_country($destinationA[0]);
                    }
                    if ($destinationOb['boolean']) {
                        if ($failsurcharge->differentiator == 1) {
                            $destinationA =  Harbor::find($destinationOb['puerto']);
                            $destinationA = $destinationA->name;
                        } else {
                            $destinationA = Country::find($destinationOb['country']);
                            $destinationA = $destinationA->name;
                        }
                    } else {
                        $destinationA = $destinationA[0] . ' (error)';
                        $classddestination = 'color:red';
                    }
                    // -------------- SURCHARGE -----------------------------------------------------------

                    $surchargeOb = Surcharge::where('name', '=', $surchargeA[0])->where('company_user_id', '=', \Auth::user()->company_user_id)->first();
                    $surcharAin = $surchargeOb['id'];
                    $surchargeC = count($surchargeA);
                    if ($surchargeC <= 1) {
                        $surchargeA = $surchargeA[0];
                    } else {
                        $surchargeA = $surchargeA[0] . ' (error)';
                        $classsurcharger = 'color:red';
                    }

                    // -------------- CARRIER -------------------------------------------------------------
                    $carrierOb = Carrier::where('name', '=', $carrierA[0])->first();
                    $carrAIn = $carrierOb['id'];
                    $carrierC = count($carrierA);
                    if ($carrierC <= 1) {
                        $carrierA = $carrierA[0];
                    } else {
                        $carrierA = $carrierA[0] . ' (error)';
                        $classcarrier = 'color:red';
                    }

                    // -------------- CALCULATION TYPE ----------------------------------------------------
                    $calculationtypeOb = CalculationType::where('name', '=', $calculationtypeA[0])->first();
                    $calculationtypeAIn = $calculationtypeOb['id'];
                    $calculationtypeC = count($calculationtypeA);
                    if ($calculationtypeC <= 1) {
                        $calculationtypeA = $calculationtypeA[0];
                    } else {
                        $calculationtypeA = $calculationtypeA[0] . ' (error)';
                        $classcalculationtype = 'color:red';
                    }

                    // -------------- AMMOUNT ------------------------------------------------------------
                    $ammountC = count($ammountA);
                    if ($ammountC <= 1) {
                        $ammountA = $failsurcharge->ammount;
                    } else {
                        $ammountA = $ammountA[0] . ' (error)';
                        $classammount = 'color:red';
                    }

                    // -------------- CURRENCY ----------------------------------------------------------
                    $currencyOb = Currency::where('alphacode', '=', $currencyA[0])->first();
                    $currencyAIn = $currencyOb['id'];
                    $currencyC = count($currencyA);
                    if ($currencyC <= 1) {
                        $currencyA = $currencyA[0];
                    } else {
                        $currencyA = $currencyA[0] . ' (error)';
                        $classcurrency = 'color:red';
                    }
                    // -------------- TYPE DESTINY -----------------------------------------------------
                    //dd($failsurcharge['typedestiny_id']);
                    $typedestinyobj = TypeDestiny::where('description', $typedestinyA[0])->first();
                    if (count($typedestinyA) <= 1) {
                        $typedestinyLB = $typedestinyobj['description'];
                    } else {
                        $typedestinyLB = $typedestinyA[0] . ' (error)';
                        $classcurrency = 'color:red';
                    }
                    $select = '';
                    ////////////////////////////////////////////////////////////////////////////////////
                    $arreglo = [
                        'id' => $failsurcharge->id,
                        'select' => $select,
                        'surchargelb' => $surchargeA,
                        'origin_portLb' => $originA,
                        'destiny_portLb' => $destinationA,
                        'carrierlb' => $carrierA,
                        'typedestinylb' => $typedestinyLB,
                        'ammount' => $ammountA,
                        'calculationtypelb' => $calculationtypeA,
                        'currencylb' => $currencyA,
                        'lower_limit' => $lower_limit,
                        'upper_limit' => $upper_limit,
                        'classsurcharge' => $classsurcharger,
                        'classorigin' => $classdorigin,
                        'classdestiny' => $classddestination,
                        'classtypedestiny' => $classtypedestiny,
                        'classcarrier' => $classcarrier,
                        'classcalculationtype' => $classcalculationtype,
                        'classammount' => $classammount,
                        'classcurrency' => $classcurrency,
                        'operation' => 1,
                    ];
                    //dd($arreglo);
                    $failsurchargecoll->push($arreglo);
                }
                //dd($failsurchargecoll);
                return DataTables::of($failsurchargecoll)->addColumn('action', function ($failsurchargecoll) {
                    return '<a href="#" class="" onclick="showModalsavetosurcharge(' . $failsurchargecoll['id'] . ',' . $failsurchargecoll['operation'] . ')"><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-Fail-Surcharge" data-id-failSurcharge="' . $failsurchargecoll['id'] . '" class=""><i class="la la-remove"></i></a>';
                })
                    ->editColumn('id', 'ID: {{$id}}')->toJson();
            } elseif ($selector == 2) {
                $surchargecollection = '';
                $surchargecollection = PrvSurchargers::get_surchargers($id);

                return DataTables::of($surchargecollection)->addColumn('action', function ($surchargecollection) {
                    return '<a href="#" class="" onclick="showModalsavetosurcharge(' . $surchargecollection['id'] . ',' . $surchargecollection['operation'] . ')"><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-Surcharge" data-id-Surcharge="' . $surchargecollection['id'] . '" class=""><i class="la la-remove"></i></a>';
                })
                    ->editColumn('id', 'ID: {{$id}}')->toJson();
            }
        }
    }

    // Descargar Archivos de referencia para la importacion -----------------------------

    public function DowLoadFiles($id)
    {
        if ($id == 1) {
            return Storage::disk('DownLoadFile')->download('COMPANIES.xlsx');
        } elseif ($id == 2) {
            return Storage::disk('DownLoadFile')->download('CONTACTS.xlsx');
        }
    }

    // Companies ------------------------------------------------------------------------
    public function UploadCompanies(Request $request)
    {

        try {
            $file = $request->file('file');
            $now = new \DateTime();
            $now = $now->format('dmY_His');
            $ext = strtolower($file->getClientOriginalExtension());
            $validator = \Validator::make(
                ['ext' => $ext],
                ['ext' => 'in:xls,xlsx,csv']
            );

            if ($validator->fails()) {
                $request->session()->flash('message.nivel', 'danger');
                $request->session()->flash('message.content', 'Only files with csv, xls and xlsx extensions are allowed');
                return redirect()->route('companies.index');
            }

            $nombre = $file->getClientOriginalName();
            $nombre = $now . '_' . $nombre;
            Storage::disk('UpLoadFile')->put($nombre, \File::get($file));

            Excel::selectSheetsByIndex(0)
                ->Load(\Storage::disk('UpLoadFile')
                    ->url($nombre), function ($reader) use ($request) {
                    $businessnameread = 'business_name';
                    $phoneRead = 'phone';
                    $emailRead = 'email';
                    $taxnumberead = 'tax_number';
                    $addressRead = 'address';
                    $ownerVal = \Auth::user()->id;
                    $company_user_id = \Auth::user()->company_user_id;

                    foreach ($reader->get() as $read) {
                        $businessnameVal = '';
                        $phoneVal = '';
                        $emailVal = '';
                        $taxnumbeVal = '';
                        $addressVal = '';

                        $businessnameVal = $read[$businessnameread];
                        $phoneVal = $read[$phoneRead];
                        $emailVal = $read[$emailRead];
                        $taxnumbeVal = $read[$taxnumberead];
                        $addressVal = $read[$addressRead];

                        Company::updateOrCreate(
                            ['business_name' => $businessnameVal, 'company_user_id' => $company_user_id],
                            [
                                'phone' => $phoneVal,
                                'address' => $addressVal,
                                'email' => $emailVal,
                                'tax_number' => $taxnumbeVal,
                                'owner' => $ownerVal
                            ]
                        );
                    }
                });
            Storage::Delete($nombre);

            return redirect()->route('companies.index');
        } catch (\Exception $e) {
            $request->session()->flash('message.content', 'An error has occurred: ' . $e->getMessage());
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.title', 'Error!');
            return redirect()->route('companies.index');
        }
    }

    public function FailedCompnaiesView()
    {
        $companyuser = \Auth::user()->company_user_id;
        $countfailcompanies = Failcompany::where('company_user_id', $companyuser)->count();

        return view('importation.failcompanies', compact('companyuser', 'countfailcompanies'));
        dd($countfailcompanies);
    }

    public function FailedCompnaieslist($id)
    {
        $failcompanies = Failcompany::where('company_user_id', $id)->get();
        //dd($failcompanies);
        $collections = collect([]);
        foreach ($failcompanies as $failcompany) {
            $businessnameVal = '';
            $phoneVal = '';
            $emailVal = '';
            $taxnumberVal = '';

            $businessnameArr = explode('_', $failcompany->business_name);
            $phoneArr = explode('_', $failcompany->phone);
            $emailArr = explode('_', $failcompany->email);
            $taxnumberArr = explode('_', $failcompany->tax_number);

            if (count($businessnameArr) == 1) {
                $businessnameVal = $businessnameArr[0];
            } else {
                $businessnameVal = $businessnameArr[0] . '(Error)';
            }

            if (count($phoneArr) == 1) {
                $phoneVal = $phoneArr[0];
            } else {
                $phoneVal = $phoneArr[0] . '(Error)';
            }

            if (count($emailArr) == 1) {
                $emailVal = $emailArr[0];
            } else {
                $emailVal = $emailArr[0] . '(Error)';
            }

            if (count($taxnumberArr) == 1) {
                $taxnumberVal = $taxnumberArr[0];
            } else {
                $taxnumberVal = $taxnumberArr[0] . '(Error)';
            }

            $compnyuser = CompanyUser::find($id);
            $user = User::find($failcompany->owner);
            $idFC = $failcompany->id;
            $detalle = [
                'id' => $idFC,
                'businessname' => $businessnameVal,
                'phone' => $phoneVal,
                'address' => $failcompany->address,
                'email' => $emailVal,
                'taxnumber' => $taxnumberVal,
                'compnyuser' => $compnyuser->name,
                'owner' => $user->name . ' ' . $user->lastname,
            ];
            //dd($detalle);
            $collections->push($detalle);
        }

        return DataTables::of($collections)->addColumn('action', function ($collection) {
            return '
                <a href="#" onclick="showModalcompany(' . $collection['id'] . ')" class=""><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-failcompany" data-id-failcompany="' . $collection['id'] . '" class=""><i class="la la-remove"></i></a>';
        })
            ->editColumn('id', 'ID: {{$id}}')->toJson();
    }

    public function ShowFailCompany($id)
    {
        $failcompany = Failcompany::find($id);
        $businessnameVal = '';
        $phoneVal = '';
        $emailVal = '';
        $taxnumberVal = '';

        $classbusiness = 'color:green';
        $classphone = 'color:green';
        $classemail = 'color:green';
        $classtaxnumber = 'color:green';

        $businessnameArr = explode('_', $failcompany->business_name);
        $phoneArr = explode('_', $failcompany->phone);
        $emailArr = explode('_', $failcompany->email);
        $taxnumberArr = explode('_', $failcompany->tax_number);

        if (count($businessnameArr) == 1) {
            $businessnameVal = $businessnameArr[0];
        } else {
            $businessnameVal = $businessnameArr[0] . '(Error)';
            $classbusiness = 'color:red';
        }

        if (count($phoneArr) == 1) {
            $phoneVal = $phoneArr[0];
        } else {
            $phoneVal = $phoneArr[0] . '(Error)';
            $classphone = 'color:red';
        }

        if (count($emailArr) == 1) {
            $emailVal = $emailArr[0];
        } else {
            $emailVal = $emailArr[0] . '(Error)';
            $classemail = 'color:red';
        }

        if (count($taxnumberArr) == 1) {
            $taxnumberVal = $taxnumberArr[0];
        } else {
            $taxnumberVal = $taxnumberArr[0] . '(Error)';
            $classtaxnumber = 'color:red';
        }

        $compnyuser = CompanyUser::find($failcompany->company_user_id);
        $user = User::find($failcompany->owner);
        $idFC = $failcompany->id;
        $detalle = [
            'id' => $idFC,
            'businessname' => $businessnameVal,
            'phone' => $phoneVal,
            'address' => $failcompany->address,
            'email' => $emailVal,
            'taxnumber' => $taxnumberVal,
            'compnyuser' => $compnyuser->name,
            'compnyuserid' => $compnyuser->id,
            'owner' => $user->name . ' ' . $user->lastname,
            'ownerid' => $user->id,
            'classbusiness' => $classbusiness,
            'classphone' => $classphone,
            'classemail' => $classemail,
            'classtaxnumber' => $classtaxnumber,
        ];

        return view('importation.Body-Modals.failedCompany', compact('detalle'));
        dd($detalle);
    }

    public function UpdateFailedCompany(Request $request, $id)
    {
        //dd($request->all());
        $company = new Company();
        $company->business_name = $request->businessname;
        $company->phone = $request->phone;
        $company->address = $request->address;
        $company->email = $request->email;
        $company->tax_number = $request->taxnumber;
        $company->company_user_id = $request->compnyuserid;
        $company->owner = $request->ownerid;
        $company->save();

        if (empty($company->id) != true) {
            $failcompany = Failcompany::find($id);
            $failcompany->delete();
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'The company was updated');

        $failcompany = Failcompany::where('company_user_id', $request->compnyuserid)->count();
        if ($failcompany >= 1) {
            return redirect()->route('view.fail.company');
        } else {
            return redirect()->route('companies.index');
        }
    }

    public function DeleteFailedCompany($id)
    {
        try {
            $fcompany = Failcompany::find($id);
            $fcompany->delete();

            return 1;
        } catch (Exception $e) {
            return 2;
        }
    }

    // Contacts -------------------------------------------------------------------------

    public function UploadContacts(Request $request)
    {
        //dd($request->all());
        $file = $request->file('file');
        $now = new \DateTime();
        $now = $now->format('dmY_His');
        $nombre = $file->getClientOriginalName();
        $nombre = $now . '_' . $nombre;

        $ext = strtolower($file->getClientOriginalExtension());
        $validator = \Validator::make(
            ['ext' => $ext],
            ['ext' => 'in:xls,xlsx,csv']
        );

        if ($validator->fails()) {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');

            return redirect()->route('contacts.index');
        }

        Storage::disk('UpLoadFile')->put($nombre, \File::get($file));
        $errors = 0;
        Excel::selectSheetsByIndex(0)
            ->Load(\Storage::disk('UpLoadFile')
                ->url($nombre), function ($reader) use ($errors, $request) {
                $firstname = 'first_name';
                $lastname = 'last_name';
                $email = 'email';
                $phone = 'phone';
                $position = 'position';
                $company = 'company';

                foreach ($reader->get() as $read) {
                    $firstnameVal = $read[$firstname];
                    $lastnameVal = $read[$lastname];
                    $emailVal = $read[$email];
                    $phoneVal = $read[$phone];
                    $positionVal = $read[$position];
                    $companyVal = $read[$company];

                    $companyBol = false;
                    $firstnameBol = false;
                    $lastnameBol = false;
                    $phoneBol = false;
                    $emailBol = false;
                    $positionBol = false;

                    $companies = Company::where('business_name', $companyVal)->get();

                    if (count($companies) == 1) { // !empty
                        foreach ($companies as $companyobj) {
                            $companyVal = $companyobj->id;
                        }
                    } else {
                        $companyVal = $companyVal . '_E_E';
                    }

                    if (empty($firstnameVal) != true) {
                        $firstnameBol = true;
                    } else {
                        $firstnameVal = $firstnameVal . '_E_E';
                    }

                    if (empty($lastnameVal) != true) {
                        $lastnameBol = true;
                    } else {
                        $lastnameVal = $lastnameVal . '_E_E';
                    }

                    if (empty($phoneVal) != true) {
                        $phoneBol = true;
                    } else {
                        $phoneVal = $phoneVal . '_E_E';
                    }

                    if (empty($emailVal) != true) {
                        $emailBol = true;
                    } else {
                        $emailVal = $emailVal . '_E_E';
                    }

                    if (empty($positionVal) != true) {
                        $positionBol = true;
                    } else {
                        $positionVal = $positionVal . '_E_E';
                    }

                    if (
                        $companyBol == true && $firstnameBol == true &&
                        $lastnameBol == true && $emailBol == true &&
                        $positionBol == true && $phoneBol == true
                    ) {
                        $contactexits = Contact::where('first_name', $firstnameVal)
                            ->where('last_name', $lastnameVal)
                            ->where('phone', $phoneVal)
                            ->where('email', $emailVal)
                            ->where('position', $positionVal)
                            ->where('company_id', $companyVal)
                            ->get();

                        if (empty($contactexits)) {
                            Contact::create([
                                'first_name' => $firstnameVal,
                                'last_name' => $lastnameVal,
                                'phone' => $phoneVal,
                                'email' => $emailVal,
                                'position' => $positionVal,
                                'company_id' => $companyVal,
                            ]);
                        }
                    } else {
                        $failcontactexits = Failedcontact::where('first_name', $firstnameVal)
                            ->where('last_name', $lastnameVal)
                            ->where('phone', $phoneVal)
                            ->where('email', $emailVal)
                            ->where('position', $positionVal)
                            ->where('company_id', $companyVal)
                            ->where('company_user_id', \Auth::user()->company_user_id)
                            ->get();

                        if (empty($failcontactexits)) {
                            Failedcontact::create([
                                'first_name' => $firstnameVal,
                                'last_name' => $lastnameVal,
                                'phone' => $phoneVal,
                                'email' => $emailVal,
                                'position' => $positionVal,
                                'company_id' => $companyVal,
                                'company_user_id' => \Auth::user()->company_user_id,
                            ]);
                            $errors = $errors + 1;
                        }
                    }
                }

                if ($errors > 0) {
                    $request->session()->flash('message.content', 'You successfully added the companies ');
                    $request->session()->flash('message.nivel', 'danger');
                    $request->session()->flash('message.title', 'Well done!');
                    if ($errors == 1) {
                        $request->session()->flash('message.content', $errors . ' fee is not charged correctly');
                    } else {
                        $request->session()->flash('message.content', $errors . ' Companies did not load correctly');
                    }
                } else {
                    $request->session()->flash('message.nivel', 'success');
                    $request->session()->flash('message.title', 'Well done!');
                }
            });

        Storage::Delete($nombre);

        return redirect()->route('contacts.index');
    }

    public function FailedContactView()
    {
        $companyuser = \Auth::user()->company_user_id;
        $countfailcontacts = Failedcontact::where('company_user_id', $companyuser)->count();

        return view('importation.failedcontacts', compact('countfailcontacts', 'companyuser'));
    }

    public function FailedContactlist($id)
    {
        $failedconatcs = Failedcontact::where('company_user_id', $id)->get();

        $collections = collect([]);
        foreach ($failedconatcs as $failedconatc) {
            $companylb = '';
            $firstnameVal = explode('_', $failedconatc['first_name']);
            $lastnameVal = explode('_', $failedconatc['last_name']);
            $phoneVal = explode('_', $failedconatc['phone']);
            $emailVal = explode('_', $failedconatc['email']);
            $positionVal = explode('_', $failedconatc['position']);
            $company_idVal = explode('_', $failedconatc['company_id']);

            if (count($firstnameVal) == 1) {
                $firstnameVal = $firstnameVal[0];
            } else {
                $firstnameVal = $firstnameVal[0] . '(Error)';
            }

            if (count($lastnameVal) == 1) {
                $lastnameVal = $lastnameVal[0];
            } else {
                $lastnameVal = $lastnameVal[0] . '(Error)';
            }

            if (count($phoneVal) == 1) {
                $phoneVal = $phoneVal[0];
            } else {
                $phoneVal = $phoneVal . '(Error)';
            }

            if (count($emailVal) == 1) {
                $emailVal = $emailVal[0];
            } else {
                $emailVal = $emailVal[0] . '(Error)';
            }

            if (count($positionVal) == 1) {
                $positionVal = $positionVal[0];
            } else {
                $positionVal = $positionVal[0] . '(Error)';
            }
            $company = Company::where('id', $company_idVal[0])->first();
            if (count($company) == 1) {
                $company_idVal = $company['id'];
                $companylb = $company['business_name'];
            } else {
                $companylb = $company_idVal[0] . '(Error)';
                $company_idVal = '';
            }
            $data = [
                'id' => $failedconatc->id,
                'firstname' => $firstnameVal,
                'lastname' => $lastnameVal,
                'phone' => $phoneVal,
                'email' => $emailVal,
                'position' => $positionVal,
                'company' => $company_idVal,
                'companylb' => $companylb,
            ];

            $collections->push($data);
        }
        //dd($collections);

        return DataTables::of($collections)->addColumn('action', function ($collection) {
            return '
                <a href="#" onclick="showModalcontact(' . $collection['id'] . ')" class=""><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-failcontact" data-id-failcontact="' . $collection['id'] . '" class=""><i class="la la-remove"></i></a>';
        })
            ->editColumn('id', 'ID: {{$id}}')->toJson();
    }

    public function DeleteFailedContact($id)
    {
        try {
            $fcontact = Failedcontact::find($id);
            $fcontact->delete();

            return 1;
        } catch (Exception $e) {
            return 2;
        }
    }

    public function ShowFailContact($id)
    {
        $failedcontact = Failedcontact::find($id);

        $firnameVal = '';
        $lastnameVal = '';
        $phoneVal = '';
        $emailVal = '';
        $positionVal = '';
        $companyVal = '';

        $firnameclass = 'color:green';
        $lastnameclass = 'color:green';
        $phoneclass = 'color:green';
        $emailclass = 'color:green';
        $positionclass = 'color:green';
        $companyclass = 'color:green';

        $firnameArr = explode('_', $failedcontact->first_name);
        $lastnameArr = explode('_', $failedcontact->last_name);
        $phoneArr = explode('_', $failedcontact->phone);
        $emailArr = explode('_', $failedcontact->email);
        $positionArr = explode('_', $failedcontact->position);
        $companyArr = explode('_', $failedcontact->company_id);

        if (count($firnameArr) <= 1) {
            $firnameVal = $firnameArr[0];
        } else {
            $firnameVal = $firnameArr[0] . '(Error)';
            $firnameclass = 'color:red';
        }

        if (count($lastnameArr) <= 1) {
            $lastnameVal = $lastnameArr[0];
        } else {
            $lastnameVal = $lastnameArr[0] . '(Error)';
            $lastnameclass = 'color:red';
        }

        if (count($phoneArr) <= 1) {
            $phoneVal = $phoneArr[0];
        } else {
            $phoneVal = $phoneArr[0] . '(Error)';
            $phoneclass = 'color:red';
        }

        if (count($emailArr) <= 1) {
            $emailVal = $emailArr[0];
        } else {
            $emailVal = $emailArr[0] . '(Error)';
            $emailclass = 'color:red';
        }

        if (count($positionArr) <= 1) {
            $positionVal = $positionArr[0];
        } else {
            $positionVal = $positionArr[0] . '(Error)';
            $positionclass = 'color:red';
        }

        if (count($companyArr) <= 1) {
            $companyVal = $companyArr[0];
        } else {
            $companyVal = '';
            $companyclass = 'color:red';
        }

        $companies = Company::where('company_user_id', \Auth::user()->company_user_id)->pluck('business_name', 'id');
        $detalle = [
            'id' => $id,
            'firstname' => $firnameVal,
            'lastname' => $lastnameVal,
            'phone' => $phoneVal,
            'email' => $emailVal,
            'position' => $positionVal,
            'company' => $companyVal,
            'firstnameclass' => $firnameclass,
            'lastnameclass' => $lastnameclass,
            'phoneclass' => $phoneclass,
            'emailclass' => $emailclass,
            'positionclass' => $positionclass,
            'companyclass' => $companyclass,
        ];

        //dd($detalle);

        return view('importation.Body-Modals.FailEditContact', compact('detalle', 'companies'));
    }

    public function UpdateFailedContact(Request $request, $id)
    {
        $contact = new Contact();
        $contact->first_name = $request->firstname;
        $contact->last_name = $request->lastname;
        $contact->phone = $request->phone;
        $contact->email = $request->email;
        $contact->position = $request->position;
        $contact->company_id = $request->company;
        $contact->save();

        if (empty($contact->id) != true) {
            $contact = Failedcontact::find($id);
            $contact->delete();
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'The conatct was updated');

        //Revisar
        $countfail = Failedcontact::where('company_user_id', $id)->count();
        if (count($countfail) > 0) {
            return redirect()->route('contacts.index');
        } else {
            return redirect()->route('view.fail.contact');
        }
    }

    public function ValidateCompany($id)
    {
        $company = CompanyUser::find($id);

        return response()->Json($company);
    }

    // Account Importation --------------------------------------------------------------

    public function indexAccount(Request $request)
    {
        $date_start = $request->dateS;
        $date_end = $request->dateE;
        $date_end = Carbon::parse($date_end);
        $date_end = $date_end->addDay(1);

        $account = \DB::select('call  proc_account_fcl("' . $date_start . '","' . $date_end . '")');

        return DataTables::of($account)
            /*  ->addColumn('status', function ( $account) {
        if(empty($account->contract->status)!=true){
        return  $account->contract->status;
        }else{
        return  'Contract erased';
        }

        })
        ->addColumn('company_user_id', function ( $account) {
        return  $account->companyuser->name;
        })
        ->addColumn('request_id', function ( $account) {
        if(empty($account->request_id) != true){
        return  $account->request_id;
        } else {
        return 'Manual';
        }
        })*/
            ->addColumn('action', function ($account) {
                if (strnatcasecmp($account->namefile, 'N/A') == 0) {
                    if (empty($account->request_dp_id)) {
                        $descarga = '&nbsp;<span style="color:#0072FC;font-size:15px" title="Duplicate Contract">Dp</span>';
                    } else {
                        $descarga = '&nbsp;<a href="#" onclick="AbrirModal(\'showRequestDp\',' . $account->request_dp_id . ',0)"><span  style="color:#0072FC;font-size:15px" title="Duplicate Contract">' . $account->request_dp_id . '</span></a>';
                    }
                } else {
                    $descarga = '&nbsp;
                    <a href="/Importation/DownloadAccountcfcl/' . $account->id . '" class=""><i class="la la-cloud-download" title="Download"></i></a>';
                }
                if ($account->status != 'Contract erased') {
                    return '
                <a href="' . route('Failed.Developer.For.Contracts', [$account->contract_id, 0]) . '" class=""><i class="la la-credit-card" title="Failed - FCL"></i></a>
                &nbsp;
                ' . $descarga . '
                &nbsp;
                <a href="#" id="delete-account-cfcl" data-id-account-cfcl="' . $account->id . '" class=""><i class="la la-remove" title="Delete"></i></a>';
                } else {
                    return $descarga . '&nbsp;
                <a href="#" id="delete-account-cfcl" data-id-account-cfcl="' . $account->id . '" class=""><i class="la la-remove" title="Delete"></i></a>';
                }
            })
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function DestroyAccount($id)
    {
        try {
            $contract = Contract::where('account_id', $id)->first();
            if (count((array) $contract) == 1) {
                $data = PrvValidation::ContractWithJob($contract->id);
                if ($data['bool'] == false) {
                    $account = AccountFcl::find($id);
                    Storage::disk('FclAccount')->delete($account->namefile);
                    $account->delete();
                }

                return response()->json(['success' => 1, 'jobAssociate' => $data['bool']]);
            } else {
                $account = AccountFcl::find($id);
                Storage::disk('FclAccount')->delete($account->namefile);
                $account->delete();

                return response()->json(['success' => 1, 'jobAssociate' => false]);
            }
        } catch (Exception $e) {
            return response()->json(['success' => 2, 'jobAssociate' => false]);
        }
    }

    public function Download($id)
    {
        $account = AccountFcl::find($id);
        $time = new \DateTime();
        $now = $time->format('d-m-y');
        $company = CompanyUser::find($account->company_user_id);
        $extObj = new \SplFileInfo($account->namefile);
        $ext = $extObj->getExtension();
        if (empty($account->namefile)) {
            $mediaItem = $account->getFirstMedia('document');
            $name = explode('_', $mediaItem->file_name);
            $name = str_replace($name[0] . '_', '', $mediaItem->file_name);
            $name = replaceSpecialCharacter($name);
            return Storage::disk('FclAccount')->download($mediaItem->id . '/' . $mediaItem->file_name, $name);
        } else {
            $name = $account->id . '-' . $company->name . '_' . $now . '-FLC.' . $ext;
            $name = replaceSpecialCharacter($name);
            try {
                return Storage::disk('s3_upload')->download('Account/FCL/' . $account->namefile, $name);
            } catch (\Exception $e) {
                return Storage::disk('FclAccount')->download($account->namefile, $name);
            }
        }
    }

    // Account Request duplicated SHOW --------------------------------------------------

    public function ShowRequestDp($id)
    {
        $request = NewContractRequest::find($id);
        $request->load('user', 'direction', 'Requestcarriers.carrier', 'companyuser');
        //dd($request->Requestcarriers->pluck('carrier')->implode('name',', '));
        return view('RequestV2.Fcl.Body-Modals.ShowRequest', compact('request'));
    }

    // Dropzone Importation Fcl----------------------------------------------------------
    public function storeMedia(Request $request)
    {
        $path = storage_path('tmp/importation/fcl');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        //chmod($path, 0777);
        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name' => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }

    public function changeStatusTime($ncontractRq,$start=true){
        $time = new \DateTime();
        $now = $time->format('Y-m-d H:i:s');
        $data_options = json_decode($ncontractRq->data,true);
        $status_time = $data_options["status_time"];
        if(array_key_exists($ncontractRq->status,$status_time)){
            if($start == true){
                if(count($status_time[$ncontractRq->status]) >= 1){
                    $fechaEnd = Carbon::parse($now);
                    $fechaStar = Carbon::parse($status_time[$ncontractRq->status][count($status_time[$ncontractRq->status])-1][1]);
                    $time_exacto = $fechaEnd->diffInMinutes($fechaStar);
                    array_push($status_time[$ncontractRq->status][count($status_time[$ncontractRq->status])-1],$now,$time_exacto);
                }elseif(count($status_time[$ncontractRq->status]) == 0){
                    array_push($status_time[$ncontractRq->status],['admin',$now,$now,'0 mins.']);
                }
            }else{
                array_push($status_time[$ncontractRq->status],['admin',$now]);
            }
        } else {
            $status_time[$ncontractRq->status] = [['admin',$now]];
        }
        $data_options["status_time"] = $status_time;
        $ncontractRq->data = json_encode($data_options);
        dd($data_options,$ncontractRq->data );
        //$ncontractRq->update();
        return $ncontractRq;
    }
    // Solo Para Testear ----------------------------------------------------------------
    public function testExcelImportation(Request $request)
    {
        $ncontractRq = NewContractRequest::find(37179);
        $ncontractRq = $this->changeStatusTime($ncontractRq,true);
        dd(json_decode($ncontractRq->data,true));
    }
}
