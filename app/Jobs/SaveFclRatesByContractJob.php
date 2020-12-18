<?php

namespace App\Jobs;

use App\CompanyUser;
use App\Container;
use App\ContainerCalculation;
use App\Contract;
use App\ContractRateApi;
use App\ContractRateFclApi;
use App\Http\Traits\SearchTraitApi;
use App\Http\Traits\UtilTrait;
use App\LocalCharge;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Collection as Collection;

class SaveFclRatesByContractJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use SearchTraitApi;
    use UtilTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $contracts = Contract::where('is_api', 1)
            ->with(['rates' => function ($query) {
                $query->with(['carrier' => function ($q) {
                    $q->select('id', 'name', 'uncode', 'image', 'image as url');
                }]);
            }])->get();

        $container_calculation = ContainerCalculation::get();
        $containers = Container::get();

        foreach ($contracts as $values) {

            $count = ContractRateFclApi::where('contract_id', $values->id)->count();

            if ($count == 0) {

                $company = CompanyUser::find($values->company_user_id);

                $markup = null;

                $equipment = array();
                $totalesCont = array();

                //Colecciones
                $general = new collection();
                $collectionRate = new Collection();

                $idCurrency = $company->currency_id;

                $equipment = array('1', '2', '3', '4', '5');

                $validateEquipment = $this->validateEquipment($equipment, $containers);

                // Consulta base de datos rates

                if ($validateEquipment['count'] < 2) {

                    $rates = $values->rates;

                    $chunk = $rates->chunk(100);

                    foreach ($chunk as $item) {
                        foreach ($item as $data) {

                            $typeCurrency =  $data->currency->alphacode;

                            foreach ($containers as $cont) {
                                $totalesContainer = array($cont->code => array('tot_' . $cont->code . '_F' => 0, 'tot_' . $cont->code . '_O' => 0, 'tot_' . $cont->code . '_D' => 0));
                                $totalesCont = array_merge($totalesContainer, $totalesCont);
                                $var = 'array' . $cont->code;
                                $$var = $container_calculation->where('container_id', $cont->id)->pluck('calculationtype_id')->toArray();
                            }

                            //$contractStatus = $data->contract->status;
                            $collectionRate = new Collection();
                            $collectionOrigin = new collection();
                            $collectionDestiny = new collection();
                            $collectionFreight = new collection();
                            $totalRates = 0;
                            $totalT = 0;

                            //Arreglo totalizador de freight , destination , origin por contenedor
                            $totalesCont = array();
                            $arregloRateSum = array();

                            foreach ($containers as $cont) {
                                $totalesContainer = array($cont->code => array('tot_' . $cont->code . '_F' => 0, 'tot_' . $cont->code . '_O' => 0, 'tot_' . $cont->code . '_D' => 0));
                                $totalesCont = array_merge($totalesContainer, $totalesCont);
                                // Inicializar arreglo rate
                                $arregloRate = array('c' . $cont->code => '0');
                                $arregloRateSum = array_merge($arregloRateSum, $arregloRate);
                            }

                            $carrier[] = $data->carrier_id;

                            $arregloRate = array();
                            //Arreglos para guardar el rate
                            $array_ocean_freight = array('type' => 'Ocean Freight', 'detail' => 'Per Container', 'currency' => $data->currency->alphacode);

                            $arregloRateSave['markups'] = array();
                            $arregloRateSave['rate'] = array();
                            //Arreglo para guardar charges
                            $arregloCharges['origin'] = array();

                            $rateC = $this->ratesCurrency($data->currency->id, $typeCurrency);
                            // Rates
                            $arregloR = $this->ratesSearch($equipment, $markup, $data, $rateC, $typeCurrency, $containers);

                            $arregloRateSum = array_merge($arregloRateSum, $arregloR['arregloSaveR']);

                            $arregloRateSave['rate'] = array_merge($arregloRateSave['rate'], $arregloR['arregloSaveR']);
                            //$arregloRateSave['markups'] = array_merge($arregloRateSave['markups'], $arregloR['arregloSaveM']);
                            $arregloRate = array_merge($arregloRate, $arregloR['arregloRate']);

                            $equipmentFilter = $arregloR['arregloEquipment'];

                            /*$carrier_all = Carrier::where('name', 'ALL')->select('id')->first();*/

                            // ################### Calculos local  Charges #############################

                            $localChar = LocalCharge::where('contract_id', '=', $data->contract_id)->whereHas('localcharcarriers', function ($q) use ($carrier) {
                                $q->whereIn('carrier_id', $carrier);
                            })->with('localcharports.portOrig', 'localcharcarriers.carrier', 'surcharge.saleterm')
                                ->with(['currency' => function ($q) {
                                    $q->select('id', 'alphacode', 'rates as exchange_usd', 'rates_eur as exchange_eur');
                                }])->get();


                            foreach ($localChar as $local) {

                                //$rateMount = $this->ratesCurrency($local->currency->id, $typeCurrency);

                                /*   // Condicion para enviar los terminos de venta o compra
                            if (isset($local->surcharge->saleterm->name)) {
                                $terminos = $local->surcharge->saleterm->name;
                            } else {
                                $terminos = $local->surcharge->name;
                            }*/

                                foreach ($local->localcharcarriers as $localCarrier) {
                                    if ($localCarrier->carrier_id == $data->carrier_id || $localCarrier->carrier_id == $carrier_all->id) {
                                        $localParams = array('local' => $local, 'data' => $data, 'typeCurrency' => $typeCurrency, 'idCurrency' => $idCurrency, 'localCarrier' => $localCarrier);
                                        //Origin
                                        /* if ($chargesOrigin != null) {
                                        if ($local->typedestiny_id == '1') {
                                            foreach ($containers as $cont) {
                                                $name_arreglo = 'array' . $cont->code;
                                                if (in_array($local->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {
                                                    $collectionOrigin->push($this->processLocalCharge($cont, $local, $localParams, $rateMount, $totalesCont));
                                                }
                                            }
                                        }
                                    }*/
                                        //Destiny
                                        /*if ($chargesDestination != null) {
                                        if ($local->typedestiny_id == '2') {
                                            foreach ($containers as $cont) {
            
                                                $name_arreglo = 'array' . $cont->code;
            
                                                if (in_array($local->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {
                                                    $collectionDestiny->push($this->processLocalCharge($cont, $local, $localParams, $rateMount, $totalesCont));
                                                }
                                            }
                                        }
                                    }*/
                                        //Freight

                                        if ($local->typedestiny_id == '3') {

                                            $band = false;
                                            //Se ajusta el calculo para freight tomando en cuenta el rate currency
                                            $rateMount_Freight = $this->ratesCurrency($local->currency->id, $data->currency->alphacode);
                                            $localParams['typeCurrency'] = $data->currency->alphacode;
                                            $localParams['idCurrency'] = $data->currency->id;
                                            //Fin Variables

                                            foreach ($containers as $cont) {

                                                $name_arreglo = 'array' . $cont->code;

                                                if (in_array($local->calculationtype_id, $$name_arreglo) && in_array($cont->id, $equipmentFilter)) {
                                                    $collectionFreight->push($this->processLocalCharge($cont, $local, $localParams, $rateMount_Freight, $totalesCont));
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            $totalRates += $totalT;
                            $array = array('type' => 'Ocean Freight', 'detail' => 'Per Container', 'subtotal' => $totalRates, 'total' => $totalRates . " " . $typeCurrency, 'idCurrency' => $data->currency_id, 'currency_rate' => $data->currency->alphacode, 'rate_id' => $data->id);
                            $array = array_merge($array, $arregloRate);
                            $array = array_merge($array, $arregloRateSave);
                            $collectionRate->push($array);

                            // SCHEDULE 

                            $transit_time = $this->transitTime($data->port_origin->id, $data->port_destiny->id, $data->carrier->id, $data->contract->status);

                            $data->setAttribute('via', $transit_time['via']);
                            $data->setAttribute('transit_time', $transit_time['transit_time']);
                            $data->setAttribute('service', $transit_time['service']);

                            // Valores totales por contenedor
                            $rateTot = $this->ratesCurrency($data->currency->id, $typeCurrency);

                            $sum_origin = 'sum_origin_';
                            $sum_freight = 'sum_freight_';
                            $sum_destination = 'sum_destination_';

                            foreach ($containers as $cont) {
                                ${$sum_origin . $cont->code} = 0;
                                ${$sum_freight . $cont->code} = 0;
                                ${$sum_destination . $cont->code} = 0;
                            }

                            foreach ($containers as $cont) {
                                foreach ($collectionOrigin as $origin) {
                                    if ($cont->code == $origin['type']) {
                                        $rateCurrency = $this->ratesCurrency($origin['currency_id'], $typeCurrency);
                                        ${$sum_origin . $cont->code} +=  $origin['price'] / $rateCurrency;
                                    }
                                }
                                foreach ($collectionFreight as $freight) {
                                    if ($cont->code == $freight['type']) {
                                        $rateCurrency = $this->ratesCurrency($freight['currency_id'], $typeCurrency);
                                        ${$sum_freight . $cont->code} +=  $freight['price'] / $rateCurrency;
                                    }
                                }
                                foreach ($collectionDestiny as $destination) {
                                    if ($cont->code == $destination['type']) {
                                        $rateCurrency = $this->ratesCurrency($destination['currency_id'], $typeCurrency);
                                        ${$sum_destination . $cont->code} +=  $destination['price'] / $rateCurrency;
                                    }
                                }
                            }

                            foreach ($containers as $cont) {
                                $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] = $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] + $arregloRateSum['c' . $cont->code];
                                $data->setAttribute('tot' . $cont->code . 'F', number_format($totalesCont[$cont->code]['tot_' . $cont->code . '_F'], 2, '.', ''));

                                $data->setAttribute('tot' . $cont->code . 'O', number_format($totalesCont[$cont->code]['tot_' . $cont->code . '_O'], 2, '.', ''));
                                $data->setAttribute('tot' . $cont->code . 'D', number_format($totalesCont[$cont->code]['tot_' . $cont->code . '_D'], 2, '.', ''));

                                $totalesCont[$cont->code]['tot_' . $cont->code . '_F']  = $totalesCont[$cont->code]['tot_' . $cont->code . '_F']  / $rateTot;
                                // TOTALES
                                $name_tot = 'total' . $cont->code;
                                $$name_tot = $totalesCont[$cont->code]['tot_' . $cont->code . '_D'] + $totalesCont[$cont->code]['tot_' . $cont->code . '_F'] + $totalesCont[$cont->code]['tot_' . $cont->code . '_O'];
                                $$name_tot += ${$sum_origin . $cont->code} + ${$sum_freight . $cont->code} + ${$sum_destination . $cont->code};
                                $data->setAttribute($name_tot, number_format($$name_tot, 2, '.', ''));
                            }

                            $this->compactResponse($containers, $equipment, $data, $typeCurrency);
                        }
                    }
                }
            }
        }
    }

    public function compactResponse($containers, $equipment, $data, $currency)
    {

        $rates = new ContractRateFclApi();
        $rates->origin_port = $data->port_origin->code;
        $rates->destiny_port = $data->port_destiny->code;
        $rates->via = $data->via;


        foreach ($containers as $cont) {
            ${$cont->code} = $cont->code;
        }

        foreach ($containers as $cont) {
            foreach ($equipment as $eq) {
                if ($eq == $cont->id) {
                    $rates->${$cont->code} = (float) $data['total' . $cont->code];
                }
            }
        }

        $rates->currency = $data->currency->alphacode;
        $rates->transit_time = $data->transit_time ?? 0;
        $rates->remarks = $data->contract->remarks;
        $rates->contract_id = $data->contract->id;

        $rates->save();
    }
}
