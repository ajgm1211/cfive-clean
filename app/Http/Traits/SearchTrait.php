<?php

namespace App\Http\Traits;

use App\CalculationType;
use App\CalculationTypeLcl;
use App\ContainerCalculation;
use App\CompanyUser;
use App\Currency;
use App\Inland;
use App\Price;
use App\Harbor;
use App\Country;
use App\TransitTime;
use App\Container;
use App\RemarkCondition;
use App\GroupContainer;
use App\NewContractRequest;
use App\NewContractRequestLcl;
use App\Contract;
use App\ContractLcl;
use App\ContractFclFile;
use App\ContractLclFile;
use App\TermAndConditionV2;
use GoogleMaps;
use Illuminate\Support\Collection as Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaStream;
use Spatie\MediaLibrary\Models\Media;

trait SearchTrait
{
    public function inlands($inlandParams, $markup, $equipment, $contain, $type, $mode, $groupContainer, $distancia = 0)
    {
        $modality_inland = $mode; // FALTA AGREGAR EXPORT
        $company_inland = $inlandParams['company_id_quote'];

        $company_user_id = $inlandParams['company_user_id'];
        $address = $inlandParams['destination_address'];
        $typeCurrency = $inlandParams['typeCurrency'];

        if ($type == 'destino') {
            $textType = 'Destination';
        } elseif ($type == 'origen') {
            $textType = 'Origin';
        }

        if ($type == 'destino') {
            $port = $inlandParams['destiny_port'];
        } elseif ($type == 'origen') {
            $port = $inlandParams['origin_port'];
        }

        $hideD = '';
        $inlands = Inland::whereHas('inland_company_restriction', function ($a) use ($company_inland) {
            $a->where('company_id', '=', $company_inland);
        })->orDoesntHave('inland_company_restriction')->whereHas('inlandports', function ($q) use ($port) {
            $q->whereIn('port', $port);
        })->where('company_user_id', '=', $company_user_id)->where('gp_container_id', $groupContainer)->with('inlandRange', 'inlandports.ports', 'inlandkms.currency');

        $inlands->where(function ($query) use ($modality_inland) {
            $query->where('type', $modality_inland)->orwhere('type', '3');
        });

        $inlands = $inlands->get();

        $dataDest = array();

        // se agregan los aditional km
        foreach ($inlands as $inlandsValue) {
            foreach ($contain as $cont) {
                $km = 'km' . $cont->code;
                $$km = true;
            }

            $inlandDetails = [];
            foreach ($inlandsValue->inlandports as $ports) {

                $monto = 0;
                if (in_array($ports->ports->id, $port)) {
                    if ($distancia == 0) {
                        if ($type == 'destino') {
                            $origin = $ports->ports->coordinates;
                            $destination = $inlandParams['destination_address'];
                        } elseif ($type == 'origen') {
                            $origin = $inlandParams['origin_address'];
                            $destination = $ports->ports->coordinates;
                        }
                        $response = GoogleMaps::load('directions')
                            ->setParam([
                                'origin' => $origin,
                                'destination' => $destination,
                                'mode' => 'driving',
                                'language' => 'es',
                            ])->get();
                        $var = json_decode($response);
                        if (empty($var->routes)) {
                            $distancia = 1;
                        }
                        //Google MAPS
                        foreach ($var->routes as $resp) {
                            foreach ($resp->legs as $dist) {
                                $km = explode(' ', $dist->distance->text);
                                $distancia = str_replace('.', '', $km[0]);
                                $distancia = floatval($distancia);
                                if ($distancia < 1) {
                                    $distancia = 1;
                                }
                            }
                        }

                        // Fin Google Maps
                    }

                    foreach ($inlandsValue->inlandRange as $range) {
                        $rateI = $this->ratesCurrency($range->currency->id, $typeCurrency);
                        $jsonContainer = json_encode($range->json_containers, JSON_FORCE_OBJECT);
                        $json = json_decode($jsonContainer);

                        foreach ($contain as $cont) {
                            $km = 'km' . $cont->code;
                            //$$km = true;
                            /* $options = json_decode($cont->options);
                            if (@$options->field_rate != 'containers') {
                            $tipo = $options->field_rate;
                            } else {
                            $tipo = $cont->code;
                            }
                             */
                            if (in_array($cont->id, $equipment)) {
                                if ($distancia >= $range->lower && $distancia <= $range->upper) {
                                    if (isset($json->{'C' . $cont->code})) {
                                        $rateMount = $json->{'C' . $cont->code};
                                        $sub_20 = number_format($rateMount / $rateI, 2, '.', '');
                                        $amount_inland = number_format($rateMount, 2, '.', '');
                                        $price_per_unit = number_format($rateMount / $distancia, 2, '.', '');
                                    } else {
                                        $rateMount = 0;
                                        $amount_inland = 0;
                                        $price_per_unit = 0;
                                        $sub_20 = 0;
                                    }
                                    $monto += number_format($sub_20, 2, '.', '');
                                    $$km = false;
                                    // CALCULO MARKUPS
                                    $markupI20 = $this->inlandMarkup($markup['inland']['inlandPercentage'], $markup['inland']['inlandAmmount'], $markup['inland']['inlandMarkup'], $sub_20, $typeCurrency, $markup['inland']['inlandMarkup']);

                                    // FIN CALCULO MARKUPS
                                    $arrayInland20 = ['cant_cont' => '1', 'sub_in' => $sub_20, 'amount' => $amount_inland, 'currency' => $range->currency->alphacode, 'price_unit' => $price_per_unit, 'typeContent' => $cont->code];
                                    $arrayInland20 = array_merge($markupI20, $arrayInland20);
                                    $inlandDetails[] = $arrayInland20;
                                }
                            }
                        }
                    }
                    // KILOMETROS ADICIONALES

                    if (isset($inlandsValue->inlandkms)) {
                        foreach ($inlandsValue->inlandkms as $inlandk) {
                            $rateGeneral = $this->ratesCurrency($inlandk->currency_id, $typeCurrency);
                            $jsonContainerkm = json_encode($inlandk->json_containers, JSON_FORCE_OBJECT);
                            $jsonkm = json_decode($jsonContainerkm);

                            foreach ($contain as $cont) {
                                $km = 'km' . $cont->code;
                                $texto20 = 'Inland ' . $cont->code . ' x 1';

                                //  if (isset($options->field_inland)) {

                                if ($$km && in_array($cont->id, $equipment)) {
                                    if (isset($jsonkm->{'C' . $cont->code})) {
                                        $rateMount = $jsonkm->{'C' . $cont->code};
                                        $montoKm = ($distancia * $rateMount) / $rateGeneral;
                                        // dd($distancia,$rateGeneral,$rateMount,$montoKm);

                                        $sub_20 = number_format($montoKm, 2, '.', '');
                                        $monto += $sub_20;
                                        $amount_inland = $distancia * $rateMount;

                                        $amount_inland = number_format($amount_inland, 2, '.', '');
                                        $price_per_unit = number_format($rateMount / $distancia, 2, '.', '');
                                    } else {
                                        $rateMount = 0;
                                        $amount_inland = 0;
                                        $price_per_unit = 0;
                                        $sub_20 = 0;
                                        $montoKm = 0;
                                    }

                                    // CALCULO MARKUPS
                                    $markupI20 = $this->inlandMarkup($markup['inland']['inlandPercentage'], $markup['inland']['inlandAmmount'], $markup['inland']['inlandMarkup'], $sub_20, $typeCurrency, $markup['inland']['inlandMarkup']);

                                    // FIN CALCULO MARKUPS
                                    $sub_20 = number_format($sub_20, 2, '.', '');
                                    $arrayInland20 = ['cant_cont' => '1', 'sub_in' => $sub_20, 'des_in' => $texto20, 'amount' => $amount_inland, 'currency' => $inlandk->currency->alphacode, 'price_unit' => $price_per_unit, 'typeContent' => $cont->code];
                                    $arrayInland20 = array_merge($markupI20, $arrayInland20);

                                    $inlandDetails[] = $arrayInland20;
                                }
                                // }
                            }
                        }
                    }

                    $monto = number_format($monto, 2, '.', '');

                    if ($monto > 0) {
                        $inlandDetails = Collection::make($inlandDetails);

                        //HECTOR ADDED PROVIDER_ID ON 28/04/2021
                        $arregloInland = ['prov_id' => $inlandsValue->id, 'provider' => 'Inland Haulage', 'providerName' => $inlandsValue->provider, 'port_id' => $ports->ports->id, 'port_name' => $ports->ports->name, 'port_id' => $ports->ports->id, 'validity_start' => $inlandsValue->validity, 'validity_end' => $inlandsValue->expire, 'km' => $distancia, 'monto' => $monto, 'type' => $textType, 'type_currency' => $inlandDetails->first()['currency'], 'idCurrency' => $typeCurrency, 'provider_id' => $inlandsValue->provider_id];
                        $arregloInland['inlandDetails'] = $inlandDetails->groupBy('typeContent')->map(function ($item) use ($arregloInland) {
                            $minimoD = $item->where('sub_in', '>', 0);
                            $minimoDetails = $minimoD->where('sub_in', $minimoD->min('sub_in'))->first();

                            return $minimoDetails;
                        });

                        $dataDest[] = $arregloInland;
                    }
                }
            }
        }

        return $dataDest;
    }

    // Metodos para los rates
    public function rates($equipment, $markup, $data, $rateC, $typeCurrency, $contain, $rateFreight = 1)
    {
        $arreglo = [];
        $arregloRate = [];
        $arregloSaveR = [];
        $arregloSaveM = [];
        $arregloSum = [];
        $equipmentFilter = [];
        foreach ($contain as $cont) {
            foreach ($equipment as $containers) {
                if ($containers == $cont->id) {
                    $options = json_decode($cont->options);
                    if (@$options->field_rate == 'containers') {
                        $test = json_encode($data->{$options->field_rate});
                        $jsonContainer = json_decode($data->{$options->field_rate});
                        if (isset($jsonContainer->{'C' . $cont->code})) {
                            $rateMount = $jsonContainer->{'C' . $cont->code};
                        } else {
                            $rateMount = 0;
                        }
                    } else {
                        $rateMount = $data->{$options->field_rate};
                    }
                    $arreglo = $this->detailRate($markup, $rateMount, $data, $rateC, $typeCurrency, $cont->code, $rateFreight);
                    $arregloRate = array_merge($arreglo['arregloRate'], $arregloRate);
                    $arregloSaveR = array_merge($arreglo['arregloRateSaveR'], $arregloSaveR);
                    $arregloSaveM = array_merge($arreglo['arregloRateSaveM'], $arregloSaveM);
                    $arregloSum = array_merge($arreglo['arregloRateSum'], $arregloSum);
                    if ($rateMount != 0) {
                        array_push($equipmentFilter, $containers);
                    }
                }
            }
        }
     

        $arregloG = array('arregloRate' => $arregloRate, 'arregloSaveR' => $arregloSaveR,  'arregloSum' => $arregloSum ,'arregloSaveM' => $arregloSaveM, 'arregloEquipment' => $equipmentFilter);
        
        return $arregloG;

        return $arregloG;
    }

    public function detailRate($markup, $amount, $data, $rateC, $typeCurrency, $containers, $rateFreight = 1)
    {
     
        $arregloRateSave['rate'] = array();
        $arregloRateSave['rateSum'] = array();
        $arregloRateSave['markups'] = array();
        $arregloRate = array();
      
        $markup = $this->freightMarkupsTrait($markup['freight']['freighPercentage'], $markup['freight']['freighAmmount'], $markup['freight']['freighMarkup'], $amount, $typeCurrency, $containers,$rateFreight);

        $tot_F = $markup['monto' . $containers] / $rateC;
        $tot_R = $markup['monto' . $containers] ;
        //Formato decimal
        $tot_F = number_format($tot_F, 2, '.', '');
        $amount = number_format($amount, 2, '.', '');

        $arrayDetail = ['price' . $containers => $amount, 'currency' . $containers => $data->currency->alphacode, 'idCurrency' . $containers => $data->currency_id, 'total' . $containers => $tot_F];

        // Arreglos para guardar los rates
        $array_save = ['c' . $containers => $amount];
        $array_sum = ['c' . $containers => $tot_R];

        $arregloRateSave['rate'] = array_merge($array_save, $arregloRateSave['rate']);
        $arregloRateSave['rateSum'] = array_merge($array_sum, $arregloRateSave['rateSum']);

        // Markups
        $array_markup = ['m' . $containers => $markup['markup' . $containers]];
        $arregloRateSave['markups'] = array_merge($array_markup, $arregloRateSave['markups']);

        $array = array_merge($arrayDetail, $markup);
        $arregloRate = array_merge($array, $arregloRate);

        $arreglo = ['arregloRate' => $arregloRate, 'arregloRateSaveR' => $arregloRateSave['rate'], 'arregloRateSum' => $arregloRateSave['rateSum'], 'arregloRateSaveM' => $arregloRateSave['markups']];

        return $arreglo;
    }

    // Metodos Para los localcharges

    public function ChargesArray($params, $monto, $montoOrig, $type)
    {
        $local = $params['local'];
        $data = $params['data'];
        $localCarrier = $params['localCarrier'];

        $arreglo = ['surcharge_terms' => $params['terminos'], 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $localCarrier->carrier_id, 'type' => $type, 'rate_id' => $data->id, 'montoOrig' => $montoOrig, 'typecurrency' => $params['typeCurrency'], 'currency_id' => $local->currency->id, 'currency_orig_id' => $params['idCurrency']];

        return $arreglo;
    }

    public function ChargesArray99($params, $calculation_id, $calculation_name)
    {
        $local = $params['local'];
        $data = $params['data'];
        $localCarrier = $params['localCarrier'];

        $arreglo = ['surcharge_terms' => $params['terminos'], 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'montoMarkupO' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $calculation_name, 'contract_id' => $data->contract_id, 'carrier_id' => $localCarrier->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $calculation_id, 'montoOrig' => 0.00, 'typecurrency' => $params['typeCurrency'], 'currency_id' => $local->currency->id, 'currency_orig_id' => $params['idCurrency'], 'markupConvert' => 0.00];

        return $arreglo;
    }

    public function asociarPerCont($calculation_id)
    {
        $calculation = CalculationType::get();
        $valor = [];
        $gp_id = $calculation->where('id', $calculation_id)->first();
        if ($gp_id->group_container_id != 0) {
            $grupo = $calculation->where('group_container_id', $gp_id->group_container_id);
            foreach ($grupo as $val) {
                $options = json_decode($val->options);
                if (@$options->iscont == 'true') {
                    $valor = ['id' => $val->id, 'name' => $val->name];
                }
            }

            if (empty($valor)) {
                $valor = ['id' => $gp_id->id, 'name' => $gp_id->name];
            }
        } else {
            $valor = ['id' => $gp_id->id, 'name' => $gp_id->name];
        }

        return $valor;
    }

    // Metodos Calculo de markups
    public function markups($price_id, $typeCurrency, $request)
    {

        //Markups Freight
        $freighPercentage = 0;
        $freighAmmount = 0;
        $freighMarkup = 0;
        $markupFreightCurre = 0;
        // Markups Local
        $localPercentage = 0;
        $localAmmount = 0;
        $localMarkup = 0;
        $markupLocalCurre = 0;
        // Markups Local
        $inlandPercentage = 0;
        $inlandAmmount = 0;
        $inlandMarkup = 0;
        $markupInlandCurre = 0;

        $fclMarkup = Price::whereHas('company_price', function ($q) use ($price_id) {
            $q->where('price_id', '=', $price_id);
        })->with('freight_markup', 'local_markup', 'inland_markup')->get();

        if ($fclMarkup->isEmpty()) {
            $fclMarkup = Price::where('id', $price_id)->with('freight_markup', 'local_markup', 'inland_markup')->get();
        }

        foreach ($fclMarkup as $freight) {
            // Freight
            $fclFreight = $freight->freight_markup->where('price_type_id', '=', 1);
            // Valor de porcentaje
            $freighPercentage = $this->cleanJsonData($fclFreight->pluck('percent_markup'));
            // markup currency
            $markupFreightCurre = $this->cleanJsonData($fclFreight->pluck('currency'));
            // markup con el monto segun la moneda
            $freighMarkup = $this->ratesCurrency($markupFreightCurre, $typeCurrency);
            // Objeto con las propiedades del currency
            $markupFreightCurre = Currency::find($markupFreightCurre);
            $markupFreightCurre = $markupFreightCurre->alphacode;
            // Monto original
            $freighAmmount = $this->cleanJsonData($fclFreight->pluck('fixed_markup'));
            // monto aplicado al currency
            $freighMarkup = $freighAmmount / $freighMarkup;
            $freighMarkup = number_format($freighMarkup, 2, '.', '');

            // Local y global
            $fclLocal = $freight->local_markup->where('price_type_id', '=', 1);
            // markup currency

            if ((isset($request->mode) && $request->mode == "1")|| $request['direction'] == 2) {
                $markupLocalCurre = $this->cleanJsonData($fclLocal->pluck('currency_export'));
                // valor de la conversion segun la moneda
                $localMarkup = $this->ratesCurrency($markupLocalCurre, $typeCurrency);
                // Objeto con las propiedades del currency por monto fijo
                $markupLocalCurre = Currency::find($markupLocalCurre);
                $markupLocalCurre = $markupLocalCurre->alphacode;
                // En caso de ser Porcentaje
                $localPercentage = intval($this->cleanJsonData($fclLocal->pluck('percent_markup_export')));
                // Monto original
                $localAmmount = intval($this->cleanJsonData($fclLocal->pluck('fixed_markup_export')));
                // monto aplicado al currency
                $localMarkup = $localAmmount / $localMarkup;
                $localMarkup = number_format($localMarkup, 2, '.', '');
            } elseif((isset($request->mode) && $request->mode != "1")|| $request['direction'] == 1) {
                $markupLocalCurre = $this->cleanJsonData($fclLocal->pluck('currency_import'));
                // valor de la conversion segun la moneda
                $localMarkup = $this->ratesCurrency($markupLocalCurre, $typeCurrency);
                // Objeto con las propiedades del currency por monto fijo
                $markupLocalCurre = Currency::find($markupLocalCurre);
                $markupLocalCurre = $markupLocalCurre->alphacode;
                // en caso de ser porcentake
                $localPercentage = intval($this->cleanJsonData($fclLocal->pluck('percent_markup_import')));
                // monto original
                $localAmmount = intval($this->cleanJsonData($fclLocal->pluck('fixed_markup_import')));

                // monto aplicado al currency
                $localMarkup = $localAmmount / $localMarkup;
                $localMarkup = number_format($localMarkup, 2, '.', '');
            }

            //$collectionMarkup = new Collection();

            // Inlands
            $fclInland = $freight->inland_markup->where('price_type_id', '=', 1);

            if ((isset($request->mode) && $request->mode == "1")|| $request['direction'] == 2) {
                $markupInlandCurre = $this->cleanJsonData($fclInland->pluck('currency_export'));
                // valor de la conversion segun la moneda
                $inlandMarkup = $this->ratesCurrency($markupInlandCurre, $typeCurrency);
                // Objeto con las propiedades del currency por monto fijo
                $markupInlandCurre = Currency::find($markupInlandCurre);
                $markupInlandCurre = $markupInlandCurre->alphacode;
                // en caso de ser porcentake
                $inlandPercentage = intval($this->cleanJsonData($fclInland->pluck('percent_markup_export')));
                // Monto original
                $inlandAmmount = intval($this->cleanJsonData($fclInland->pluck('fixed_markup_export')));
                // monto aplicado al currency
                $inlandMarkup = $inlandAmmount / $inlandMarkup;
                $inlandMarkup = number_format($inlandMarkup, 2, '.', '');
            } elseif((isset($request->mode) && $request->mode != "1")|| $request['direction'] == 1) {
                $markupInlandCurre = $this->cleanJsonData($fclInland->pluck('currency_import'));
                // valor de la conversion segun la moneda
                $inlandMarkup = $this->ratesCurrency($markupInlandCurre, $typeCurrency);
                // Objeto con las propiedades del currency por monto fijo
                $markupInlandCurre = Currency::find($markupInlandCurre);
                $markupInlandCurre = $markupInlandCurre->alphacode;
                // en caso de ser porcentake
                $inlandPercentage = intval($this->cleanJsonData($fclInland->pluck('percent_markup_import')));
                // monto original
                $inlandAmmount = intval($this->cleanJsonData($fclInland->pluck('fixed_markup_import')));
                // monto aplicado al currency
                $inlandMarkup = $inlandAmmount / $inlandMarkup;
                $inlandMarkup = number_format($inlandMarkup, 2, '.', '');
            }
        }
        $markup_array['freight'] = ['markupFreightCurre' => $markupFreightCurre, 'freighMarkup' => $freighMarkup, 'freighPercentage' => $freighPercentage, 'freighAmmount' => $freighAmmount];

        $markup_array['charges'] = ['markupLocalCurre' => $markupLocalCurre, 'localMarkup' => $localMarkup, 'localPercentage' => $localPercentage, 'localAmmount' => $localAmmount];

        $markup_array['inland'] = ['markupInlandCurre' => $markupInlandCurre, 'inlandMarkup' => $inlandMarkup, 'inlandPercentage' => $inlandPercentage, 'inlandAmmount' => $inlandAmmount];

        $collectionMarkup = new Collection($markup_array);

        return $collectionMarkup;
    }


    public function freightMarkupsTrait($freighPercentage, $freighAmmount, $freighMarkup, $monto, $typeCurrency, $type,$rateFreight)
    {
        
        if ($freighPercentage != 0) {
            $freighPercentage = intval($freighPercentage);
            $markup = ($monto * $freighPercentage) / 100;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup;
            number_format($monto, 2, '.', '');
            $arraymarkup = ['markup' . $type => $markup, 'markupConvert' . $type => $markup, 'typemarkup' . $type => "$typeCurrency ($freighPercentage%)", 'monto' . $type => $monto, 'montoMarkupO' => $markup];
        } else {
            
            $markup = trim($freighAmmount);
           
           if($freighMarkup != 0)
               $monto += $freighMarkup * $rateFreight;
            else
               $monto += $freighMarkup ;
            $monto = number_format($monto, 2, '.', '');
            
            $arraymarkup = array("markup" . $type => $markup, "markupConvert" . $type => $freighMarkup, "typemarkup" . $type => $typeCurrency, "monto" . $type => $monto, 'montoMarkupO' => $markup);
        }

        return $arraymarkup;
    }

    public function localMarkupsTrait($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $chargeCurrency, $rateFreight)
    {

        if ($localPercentage != 0) {

            // Monto original
            $markupO = ($montoOrig * $localPercentage) / 100;
            $montoOrig += $markupO;
            $montoOrig = number_format($montoOrig, 2, '.', '');

            $markup = ($monto * $localPercentage) / 100;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup;
            $arraymarkup = array("markup" => $markup, "markupConvert" => $markupO, "typemarkup" => "$typeCurrency ($localPercentage%)", 'montoMarkup' => $monto, 'montoMarkupO' => $montoOrig);
        } else { // oki

            $valor = $this->ratesCurrency($chargeCurrency, $typeCurrency);

            $markupOrig = $localMarkup * $valor;

            //$monto = $monto / $rateFreight;
            $markup = trim($localMarkup);
            $markup = number_format($markup, 2, '.', '');
            $monto += $localMarkup;
            $monto = number_format($monto, 2, '.', '');

            $arraymarkup = array("markup" => $markup, "markupConvert" => $markupOrig, "typemarkup" => $markupLocalCurre, 'montoMarkup' => $monto, 'montoMarkupO' => $montoOrig + $markupOrig);
        }

        return $arraymarkup;
    }

    /*public function localMarkupsTrait($localPercentage, $localAmmount, $localMarkup, $monto, $montoOrig, $typeCurrency, $markupLocalCurre, $chargeCurrency,$rateFreight)
    {
        
        if ($localPercentage != 0) {

            // Monto original
            $markupO = ($montoOrig * $localPercentage) / 100;
            $montoOrig += $markupO;
            $montoOrig = number_format($montoOrig, 2, '.', '');

            $markup = ($monto * $localPercentage) / 100;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup;
            $arraymarkup = array("markup" => $markup, "markupConvert" => $markupO, "typemarkup" => "$typeCurrency ($localPercentage%)", 'montoMarkup' => $monto, 'montoMarkupO' => $montoOrig);
        } else { // oki
          
            $valor = $this->ratesCurrency($chargeCurrency, $typeCurrency);


                $markupOrig = $localMarkup * $valor;

         
          
            $monto = $monto / $rateFreight;
            $markup = trim($localMarkup);
            $markup = number_format($markup, 2, '.', '');
            $monto += $localMarkup;
            $monto = number_format($monto, 2, '.', '');

            $arraymarkup = array("markup" => $markup, "markupConvert" => $markupOrig, "typemarkup" => $markupLocalCurre, 'montoMarkup' => $monto, 'montoMarkupO' => $montoOrig + $markupOrig);
        }

        return $arraymarkup;
    }*/

    public function inlandMarkup($inlandPercentage, $inlandAmmount, $inlandMarkup, $monto, $typeCurrency, $markupInlandCurre)
    {
        if ($inlandPercentage != 0) {
            $markup = ($monto * $inlandPercentage) / 100;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup;
            $monto = number_format($monto, 2, '.', '');
            $arraymarkupI = ['markup' => $markup, 'markupConvert' => $markup, 'typemarkup' => "$typeCurrency ($inlandPercentage%)", 'montoInlandT' => $monto, 'montoMarkupO' => $markup];
        } else {
            $markup = $inlandAmmount;
            $markup = number_format($markup, 2, '.', '');
            $monto += number_format($inlandMarkup, 2, '.', '');
            $arraymarkupI = ['markup' => $markup, 'markupConvert' => $inlandMarkup, 'typemarkup' => $markupInlandCurre, 'montoInlandT' => $monto, 'montoMarkupO' => $markup];
        }

        return $arraymarkupI;
    }

    public function validateEquipment($equipmentForm, $container)
    {
        $equipment = new Collection();
        foreach ($container as $cont) {
            foreach ($equipmentForm as $val) {
                if ($val == $cont->id) {
                    $equipment->push($cont->gp_container_id);
                }
            }
        }

        $equipment = $equipment->unique();

        $equipment->values()->all();

        $array = ['gpId' => $equipment[0], 'count' => $equipment->count()];

        return $array;
    }

    public function filtrarRate($arreglo, $equipmentForm, $gpId = null, $container)
    {
        $arreglo->where(function ($query) use ($container, $equipmentForm) {
            foreach ($container as $cont) {
                foreach ($equipmentForm as $val) {
                    $options = json_decode($cont->options);

                    if ($val == $cont->id) {
                        
                        if ($options->field_rate != 'containers') {
                            $query->orwhere(@$options->field_rate, '!=', '0');
                        } else {
                            $query->contain($cont->code);
                        }
                    }
                }
            }
        });

        return $arreglo;
    }

    public function divideCarriers($array)
    {
        $carrier['carriers'] = [];
        $carrier['api'] = [];
        foreach ($array as $element) {
            if (is_numeric($element)) {
                $carriers = [$element];
                $carrier['carriers'] = array_merge($carrier['carriers'], $carriers);
            } else {
                $api[$element] = $element;
                $carrier['api'] = array_merge($carrier['carriers'], $api);
            }
        }

        return $carrier;
    }

    public function contratoFuturo($contractStart, $starDate, $contractExpire, $endDate)
    {
        $contractStart = trim($contractStart);
        $starDate = trim($starDate);
        $endDate = trim($endDate);

        // dd($contractStart,$endDate);

        if (($contractStart > $starDate) && ($contractStart > $endDate)) {
            $contratoFuturo = true;
        } else {
            $contratoFuturo = false;
        }

        return $contratoFuturo;
    }

    public function transitTime($port_orig, $port_dest, $carrier, $status)
    {
        $transitArray = [];
        if ($status != 'api') {
            $transit = TransitTime::where('origin_id', $port_orig)->where('destination_id', $port_dest)->where('carrier_id', $carrier)->first();

            if (!empty($transit)) {
                $transitArray['via'] = $transit->via;
                $transitArray['transit_time'] = $transit->transit_time;
                $transitArray['service'] = $transit->service->name;
                /**if ($transit->service->id == '1') {
                    $transitArray['service'] = '';
                } else {
                    $transitArray['service'] = $transit->service->name;
                }**/

            } else {
                $transitArray['via'] = '';
                $transitArray['transit_time'] = '';
                $transitArray['service'] = '';
            }
        } else {
            $transitArray['via'] = '';
            $transitArray['transit_time'] = '';
            $transitArray['service'] = '';
        }

        return $transitArray;
    }

    public function getTypeCurrency($chargesOrigin, $chargesDestination, $data, $typeCurrency)
    {
        if ($chargesDestination == null && $chargesOrigin == null) {
            $typeCurrencyI = $data->currency->alphacode;
        } else {
            $typeCurrencyI = $typeCurrency;
        }

        return $typeCurrencyI;
    }

    //NEW SEARCH FUNCTIONS

    //Returns only ids from a 2-levels deep array containing ids, names, etc
    public function getIdsFromArray(Array $search_request)
    {
        //Copying original array for structure purposes
        $ids_array = $search_request;

        //iterating input array
        foreach($search_request as $key=>$first_level_parameter){
            //pulling and storing ids at first level
            if(is_array($first_level_parameter) && array_key_exists('id',$first_level_parameter)){
                $ids_array[$key] = $first_level_parameter['id'];
            //pulling and storing ids at second level
            }elseif(is_array($first_level_parameter) && !array_key_exists('id',$first_level_parameter)){
                $i = 0;
                foreach($first_level_parameter as $second_level_parameter){
                    if(is_array($second_level_parameter) && array_key_exists('id',$second_level_parameter)){
                        $ids_array[$key][$i] = $second_level_parameter['id'];
                        $i++;
                    }
                }
            }
        }

        return $ids_array;
    }

    //Getting markups from price Levels
    public function getMarkupsFromPriceLevels($price_level_id, $client_currency, $direction, $type)
    {
        //Querying for price levels and markups associated (freight,local charges and inlands)
            //First company-specific price levels
        $price_level = Price::whereHas('company_price', function ($q) use ($price_level_id) {
            $q->where('price_id', '=', $price_level_id);
        })->with('freight_markup', 'local_markup', 'inland_markup')->get();
            //if none, simply any price level
        if($price_level->isEmpty()){
            $price_level = Price::where('id',$price_level_id)->with('freight_markup', 'local_markup', 'inland_markup')->get();
        }   

        //Looping through each price level to extract markups
        foreach ($price_level as $price) {
            // Filtering freight markups by search type
            if($type == 'FCL'){
                $freight_markup = $price->freight_markup->where('price_type_id', '=', 1);
            }elseif($type == 'LCL'){
                $freight_markup = $price->freight_markup->where('price_type_id', '=', 2);
            }

            //Percent markup
            $freight_percentage = $this->cleanJsonData($freight_markup->pluck('percent_markup'));
            //Fixed markup
            $freight_amount = $this->cleanJsonData($freight_markup->pluck('fixed_markup'));
            //Markup currency
            $freight_currency = $this->cleanJsonData($freight_markup->pluck('currency'));
            //Querying currency model
            $freight_currency = Currency::find($freight_currency);
            //Formatting to client decimal settings
            $freight_amount = isDecimal($freight_amount, true);

            // Filtering Local Charges markups by search type
            if($type == 'FCL'){
                $local_charge_markup = $price->local_markup->where('price_type_id', '=', 1);
            }elseif($type == 'LCL'){
                $local_charge_markup = $price->local_markup->where('price_type_id', '=', 2);
            }
            //Selecting search direction (import or export)
            if ($direction == 2) {
                //Percent markup
                $local_charge_percentage = intval($this->cleanJsonData($local_charge_markup->pluck('percent_markup_export')));
                //Fixed markup
                $local_charge_amount = intval($this->cleanJsonData($local_charge_markup->pluck('fixed_markup_export')));
                //Markup currency
                $local_charge_currency = $this->cleanJsonData($local_charge_markup->pluck('currency_export'));
                //Querying currency model
                $local_charge_currency = Currency::find($local_charge_currency);
                //Formatting to client decimal settings
                $local_charge_amount = isDecimal($local_charge_amount, true);
            } elseif($direction == 1) {
                //Percent markup
                $local_charge_percentage = intval($this->cleanJsonData($local_charge_markup->pluck('percent_markup_import')));
                //Fixed markup
                $local_charge_amount = intval($this->cleanJsonData($local_charge_markup->pluck('fixed_markup_import')));
                //Markup currency
                $local_charge_currency = $this->cleanJsonData($local_charge_markup->pluck('currency_import'));
                //Querying currency model
                $local_charge_currency = Currency::find($local_charge_currency);
                //Formatting to client decimal settings
                $local_charge_amount = isDecimal($local_charge_amount, true);
            }

            // Filtering Inland markups by search type
            if($type == 'FCL'){
                $inland_markup = $price->inland_markup->where('price_type_id', '=', 1);
            }elseif($type == 'LCL'){
                $inland_markup = $price->inland_markup->where('price_type_id', '=', 2);
            }

            if ($direction == 2) {
                //Percent markup
                $inland_percentage = intval($this->cleanJsonData($inland_markup->pluck('percent_markup_export')));
                //Fixed markup
                $inland_amount = intval($this->cleanJsonData($inland_markup->pluck('fixed_markup_export')));
                //Markup currency
                $inland_currency = $this->cleanJsonData($inland_markup->pluck('currency_export'));
                //Querying currency model
                $inland_currency = Currency::find($inland_currency);
                //Formatting to client decimal settings
                $inland_markup = isDecimal($inland_amount[0], true);
            } elseif($direction == 1) {
                //Percent markup
                $inland_percentage = intval($this->cleanJsonData($inland_markup->pluck('percent_markup_import')));
                //Fixed markup
                $inland_amount = intval($this->cleanJsonData($inland_markup->pluck('fixed_markup_import')));
                //Markup currency
                $inland_currency = $this->cleanJsonData($inland_markup->pluck('currency_import'));
                //Querying currency model
                $inland_currency = Currency::find($inland_currency);
                //Formatting to client decimal settings
                $inland_amount = isDecimal($inland_amount, true);
            }
        }
        $markup_array['freight'] = array('freight_amount' => $freight_amount, 'freight_percentage' => $freight_percentage, 'freight_currency' => $freight_currency);

        $markup_array['local_charges'] = array('local_charge_amount' => $local_charge_amount, 'local_charge_percentage' => $local_charge_percentage, 'local_charge_currency' => $local_charge_currency);

        $markup_array['inland'] = array('inland_amount' => $inland_amount, 'inland_percentage' => $inland_percentage, 'inland_currency' => $inland_currency);

        $collectionMarkup = new Collection($markup_array);

        return $collectionMarkup;
    }

    //Cleans old JSON data which is formatted with string type [] and \
    public function cleanJsonData($pluck)
    {
        $skips = ["[", "]", "\""];
        return str_replace($skips, '', $pluck);
    }

    //groups local + global charges by type (Origin, Destination, Freight)
    public function groupChargesByType($local_charges, $global_charges, $search_data)
    {
        //Creating arrays for every type
        $origin = [];
        $destination = [];
        $freight = [];

        //Creating collection for charges
        $charges = collect([]);

        //Joining local and global charges to loop through all
        $all_charges = $local_charges->concat($global_charges);
        
        //Looping through charges and including them in the corresponding array
        foreach($all_charges as $charge){
            if($charge->typedestiny_id == 1){
                array_push($origin,$charge);
            }elseif($charge->typedestiny_id == 2){
                array_push($destination,$charge);
            }elseif($charge->typedestiny_id == 3){
                array_push($freight,$charge);
            }
        }

        //Forming final collection
        if($search_data['originCharges']){
            $charges->put('Origin',$origin);
        }
        $charges->put('Freight',$freight);
        if($search_data['destinationCharges']){
            $charges->put('Destination',$destination);
        }
        
        return $charges;
    }

    //If rate data comes separate from mode (twuenty, forty, etc) joins them under the "containers" field 
    //ONLY FOR DRY CONTAINERS
    public function joinRateContainers($rates, $search_containers)
    {
        foreach($rates as $rate){
            $container_group_id = $rate->contract->gp_container_id;

            $container_array = [];
            $group_containers = Container::where('gp_container_id',$container_group_id)->get();
            $requested_containers = [];

            foreach($group_containers as $cont){
                if(in_array($cont->id,$search_containers)){
                    array_push($requested_containers, $cont->code);
                }
            }
            
            if($container_group_id == 1){
                if($rate->twuenty != null && in_array('20DV',$requested_containers)){
                    $container_array['C20DV'] = $rate->twuenty;
                }if($rate->forty != null && in_array('40DV',$requested_containers)){
                    $container_array['C40DV'] = $rate->forty;
                }if($rate->fortyhc != null && in_array('40HC',$requested_containers)){
                    $container_array['C40HC'] = $rate->fortyhc;
                }if($rate->fortynor != null && in_array('40NOR',$requested_containers)){
                    $container_array['C40NOR'] = $rate->fortynor;
                }if($rate->fortyfive != null && in_array('45HC',$requested_containers)){
                    $container_array['C45HC'] = $rate->fortyfive;
                }

                $rate->containers = json_encode($container_array);
                $rate->save();
            }else{
                $rate_containers = json_decode($rate->containers, true);
                foreach($requested_containers as $requested){
                    if(!isset($rate_containers['C'.$requested])){
                        $container_array['C'.$requested] = 0;
                    }else{
                        $container_array['C'.$requested] = $rate_containers['C'.$requested];
                    }
                }

                $rate->containers = json_encode($container_array);
            }
        }
    }

    //Necessary calculations for LCL Rate : calculating totals with requested LCL type, and converting to client currency for display
    public function setLclRateTotals($rate, $search_data)
    {
        $amount = $rate->uom;

        $chargeable_weigth = $search_data['chargeableWeight'];

        $total = $amount * $chargeable_weigth;

        if($total < $rate->minimum){
            $total = $rate->minimum;
        }

        $total_client_currency = $this->convertToCurrency($rate->currency, $search_data['client_currency'], array($total));

        $rate->setAttribute('units', $chargeable_weigth);
        $rate->setAttribute('total',$total);
        $rate->setAttribute('total_client_currency',$total_client_currency[0]);
    }

    //Necessary calculations for LCL Charges, using calculation types (business rules)
    public function calculateLclChargesPerType($charges_direction, $search_data)
    {
        foreach($charges_direction as $direction){
            foreach($direction as $charge){
                $calculation_options = json_decode($charge->calculationtypelcl->options, true);

                if($calculation_options['type'] == 'unique'){
                    $units = 1;
                }else if($calculation_options['type'] == 'chargeable' || $calculation_options['type'] == 'rate_only'){
                    if($calculation_options['rounded']){
                        $units = ceil($search_data['chargeableWeight']);
                    }else{
                        $units = $search_data['chargeableWeight'];
                    }
                }else if($calculation_options['type'] == 'ton'){
                    if($calculation_options['rounded']){
                        $units = ceil($search_data['weight'] / 1000);
                    }else{
                        $units = $search_data['weight'] / 1000;
                    }
                }else if($calculation_options['type'] == 'm3'){
                    if($calculation_options['rounded']){
                        $units = ceil($search_data['volume']);
                    }else{
                        $units = $search_data['volume'];
                    }                   
                }else if($calculation_options['type'] == 'kg'){
                    $units = $search_data['weight'];
                }else if($calculation_options['type'] == 'package' || $calculation_options['type'] == 'pallet'){
                    $units = $search_data['quantity'];
                }

                $amount = $charge->ammount * $units;
                $charge->units = $units;

                $minimum = $charge->minimum;

                if($amount < $minimum){
                    $amount = $minimum;
                    $charge->ammount = $amount / $units;
                }

                $total_client_currency = $this->convertToCurrency($charge->currency, $search_data['client_currency'], array($amount));

                $charge->setAttribute('total', $amount);
                $charge->setAttribute('total_client_currency',$total_client_currency[0]);
                $charge->setAttribute('client_currency',$search_data['client_currency']);
            }
        }
    }

    //Get charges per container from calculation type - inputs a charge collection, outputs ordered collection
    public function calculateFclCharges($charges, $containers, $rate_containers, $client_currency)
    {
        $container_ids = $this->getIdsFromArray($containers);
        $rate_container_array = json_decode($rate_containers,true);
        $container_calculations = ContainerCalculation::whereIn('container_id',$container_ids)->get();

        //Looping through charges collection
        foreach($charges as $charges_direction){
            foreach($charges_direction as $charge){
                //Getting calculation info from calculation type id
                $calculation = $charge->calculationtype;

                //Empty array for storing final charges
                $container_charges = [];

                foreach($container_calculations as $relation){
                    if($relation->calculationtype_id == $calculation->id){
                        foreach($containers as $container){
                            if($relation->container_id == $container['id']){
                                $options = json_decode($container['options'],true);
                                $calculation_options = json_decode($calculation->options, true);
                                if($calculation_options['isteu'] && isset($options['is_teu']) && $options['is_teu']){
                                    $container_charges['C'.$container['code']] = 2 * $charge->ammount;
                                }else{
                                    $container_charges['C'.$container['code']] = $charge->ammount;
                                }
                            }
                        }
                    }
                }

                foreach($rate_container_array as $code => $price){
                    if(!isset($container_charges[$code]) || $price == 0){
                        $container_charges[$code] = 0;
                    }
                }

                //Setting rates per container
                    //In unmodified currency, for general use
                    //In client currency to show in overall totals

                $client_currency_charges = $this->convertToCurrency($charge->currency,$client_currency,$container_charges);

                $charge->setAttribute('containers_client_currency',$client_currency_charges);
                
                $charge->setAttribute('containers',$container_charges);

                $charge->setAttribute('client_currency',$client_currency);
            }
        }
    }

    //Joining FCL charges where surcharge, carrier and ports match; when join, amounts are added together
    public function joinCharges($charges, $search_data)
    {
        $client_currency = $search_data['client_currency'];
        //Empty array for joint charges
        $joint_charges = [];
        if($search_data['selectedContainerGroup'] == 1){
            $per_container_calculation_type = CalculationType::where('id',5)->first();
        }elseif($search_data['selectedContainerGroup'] == 2){
            $per_container_calculation_type = CalculationType::where('id',19)->first();
        }elseif($search_data['selectedContainerGroup'] == 3){
            $per_container_calculation_type = CalculationType::where('id',20)->first();
        }elseif($search_data['selectedContainerGroup'] == 4){
            $per_container_calculation_type = CalculationType::where('id',21)->first();
        }

        $joining = false;

        //Looping through top charges array (Origin, Destination, Freight)
        foreach($charges as $direction=>$charges_direction){
            //Empty array for final charges with original array structure
            $joint_charges[$direction] = [];
            //If there's only one charge in one of the directions, add that charge directly to the final array
            if(count($charges_direction) == 1){
                $joint_charges[$direction] = $charges_direction;
                $charges_direction[0]->setAttribute('joint_as','charge_currency');
            //If there's more, begin comparison and join routine
            }elseif(count($charges_direction) > 1){
                //Duplicating original array for comparing and joining
                $comparing_array = $charges_direction;
                //Control array for already joint charges
                $compared_and_joint = [];
                //Looping through charges in direction
                foreach($charges_direction as $charge){
                    if(!$charge->hide){
                        //Index of present charge in its array, for control purposes
                        $original_charge_index = array_search($charge,$charges_direction);
                        //Control variable that indicates whether a charge has been matched and joint
                        $charge_matched = false;
                        //Looping through duplicate array
                        foreach($comparing_array as $comparing_charge){
                            if(!$comparing_charge->hide){
                                //Index of present compared change in its array, for control purposes
                                $comparing_charge_index = array_search($comparing_charge,$comparing_array);
                                //Checking that the index of the original charge is lower than the compared charge. This will avoid duplicate joints, as follows:
                                //Original index 0 only compares with 1,2,3...
                                //Original index 1 only compares with 2,3,4...
                                //Original index 2 only compares with 3,4,5... And so on
                                if($original_charge_index < $comparing_charge_index){
                                    //Checking if charge needs to be joint
                                        //Surcharges must match
                                        //Index of compared charge must not be in the compared control array (compared_and_joint)
                                    if($charge->surcharge_id == $comparing_charge->surcharge_id &&
                                        count(array_intersect([$original_charge_index, $comparing_charge_index], $compared_and_joint)) == 0 ){
                                            if($search_data['type'] == 'FCL'){
                                                $joint_success = $this->calculateJointFclCharge($charge,$comparing_charge, $per_container_calculation_type, $client_currency);
                                            }elseif($search_data['type'] == 'LCL'){
                                                $joint_success = $this->calculateJointLclCharge($charge,$comparing_charge, $client_currency);
                                            }

                                            if($joint_success){
                                                //if original charge has been joint and included in final array, replace it. This avoids duplicate joint charges as follows:
                                                    //EXAMPLE
                                                    //Original charge 0 is joint with compared charge 1, original 0 is included in final array
                                                    //Original charge 0 is joint with compared charge 2, original 0 is included in final array -> DUPLICATE
                                                    //SOLUTION -> Original charge must be pulled from final array if present
                                                foreach($joint_charges[$direction] as $key => $j_charge){
                                                    if($j_charge->id == $charge->id){
                                                        unset($joint_charges[$direction][$key]);
                                                    }
                                                }
                                                //Include joint charge in final array
                                                array_push($joint_charges[$direction],$charge);
                                                //Include comparing charge in control array, indicating it has been joint already
                                                array_push($compared_and_joint,$comparing_charge_index);
                                                //Indicating original charge has been matched with at least one comparin charge
                                                $charge_matched = true;
                                            }
                                    }
                                }
                            }
                        }
                        //Checking if original charge hasnt been matched and joint
                        if(!$charge_matched && !in_array($original_charge_index,$compared_and_joint)){
                            //Including unjoint charge in final array
                            $charge->setAttribute('joint_as', 'charge_currency');
                            array_push($joint_charges[$direction],$charge);
                        }
                    }
                }
            }
        }

        return($joint_charges);
    }

    public function calculateJointFclCharge($charge,$comparing_charge, $per_container_calculation_type, $client_currency)
    {
        //Converting compared array container rates into corresponding currency:
            //If currencies don't match, join must be done in client currency
            if($charge->currency->id != $comparing_charge->currency->id){
                $joint_containers = $charge->containers_client_currency;
                //Marking charge as joint under client currency
                $charge->setAttribute('joint_as','client_currency');
                unset($charge->calculationtype);
                unset($charge->calculationtype_id);
                $charge->setAttribute('calculationtype', $per_container_calculation_type);
                $charge->setAttribute('calculationtype_id', $per_container_calculation_type->id);
                $comparing_charge_containers = $comparing_charge->containers_client_currency;
            //If currencies match, sum is direct
            }elseif($charge->currency->id == $comparing_charge->currency->id){
                $joint_containers = $charge->containers;
                //Marking as joint under charge currency
                $charge->setAttribute('joint_as','charge_currency');
                unset($charge->calculationtype);
                unset($charge->calculationtype_id);
                $charge->setAttribute('calculationtype', $per_container_calculation_type);
                $charge->setAttribute('calculationtype_id', $per_container_calculation_type->id);
                $comparing_charge_containers = $comparing_charge->containers;
            }
            //Adding container rates together
            foreach($comparing_charge_containers as $code => $container){
                if(!isset($joint_containers[$code])){
                    $joint_containers[$code] = 0;
                }
                $joint_containers[$code] += $container;
                //Checking join type and using corresponding container array
                if($charge->joint_as == 'client_currency'){
                    $charge->containers_client_currency = $joint_containers;
                }elseif($charge->joint_as == 'charge_currency'){
                    $charge->containers = $joint_containers;
                    $charge->containers_client_currency = $this->convertToCurrency($charge->currency, $client_currency, $joint_containers);
                }
            }

            return true;
    }

    public function calculateJointLclCharge($charge,$comparing_charge, $client_currency)
    {
        $calculation_options = json_decode($charge->calculationtypelcl->options, true);
        $comparing_calculation_options = json_decode($comparing_charge->calculationtypelcl->options, true);

        //CASE 1: Type of calculation is the same, or same type but not unique nor chargeable - EXAMPLE: ton and ton, m3 and m3 ,etc
        if( $charge->calculationtypelcl_id == $comparing_charge->calculationtypelcl_id ){
            //If currencies don't match, join must be done in client currency
            if($charge->currency->id != $comparing_charge->currency->id){
                //Marking charge as joint under client currency
                $charge->setAttribute('joint_as','client_currency');

                //Adding regular amounts
                $joint_amount = $charge->total_client_currency + $comparing_charge->total_client_currency;
                $charge->total_client_currency = $joint_amount;

            //If currencies match, add is direct
            }elseif($charge->currency->id == $comparing_charge->currency->id){
                //Marking as joint under charge currency
                $charge->setAttribute('joint_as','charge_currency');

                //Adding regular amounts
                $joint_amount = $charge->total + $comparing_charge->total;
                $charge->total = $joint_amount;
                $charge->total_client_currency = $this->convertToCurrency($charge->currency, $client_currency, array($joint_amount))[0];
            }

            $charge->ammount = $charge->total / $charge->units;

            return true;
        //CASE 2: Both charges adaptable, different types - ton and m3 (Adapt)
        }/**elseif( $calculation_options['adaptable'] && $comparing_calculation_options['adaptable'] && $calculation_options['type'] != $comparing_calculation_options['type'] ){
            //If currencies don't match, join must be done in client currency
            if($charge->currency->id != $comparing_charge->currency->id){
                //Marking charge as joint under client currency
                $charge->setAttribute('joint_as','client_currency');

                $joint_amount = $charge->total_client_currency + $comparing_charge->adaptable_total_client_currency;
                $charge->total_client_currency = $joint_amount;

                $joint_adaptable_amount = $charge->adaptable_total_client_currency + $comparing_charge->total_client_currency;
                $charge->adaptable_total_client_currency = $joint_adaptable_amount;
            //If currencies match, add is direct
            }elseif($charge->currency->id == $comparing_charge->currency->id){
                //Marking as joint under charge currency
                $charge->setAttribute('joint_as','charge_currency');

                $joint_amount = $charge->total + $comparing_charge->adaptable_total;
                $charge->total = $joint_amount;
                $charge->total_client_currency = $this->convertToCurrency($charge->currency, $client_currency, array($joint_amount))[0];

                $joint_adaptable_amount = $charge->adaptable_total + $comparing_charge->total;
                $charge->adaptable_total = $joint_adaptable_amount;
                $charge->adaptable_total_client_currency = $this->convertToCurrency($charge->currency, $client_currency, array($joint_adaptable_amount))[0];
            }

            return true;
        //CASE 3: Original charge adaptable, Comparing charge NOT adaptable, types differ 
        }elseif( $calculation_options['adaptable'] && !$comparing_calculation_options['adaptable'] && $calculation_options['type'] != $comparing_calculation_options['type'] ){
            //If currencies don't match, join must be done in client currency
            if($charge->currency->id != $comparing_charge->currency->id){
                //Marking charge as joint under client currency
                $charge->setAttribute('joint_as','client_currency');

                $joint_amount = $charge->adaptable_total_client_currency + $comparing_charge->total_client_currency;
                $charge->adaptable_total_client_currency = $joint_amount;
            //If currencies match, add is direct
            }elseif($charge->currency->id == $comparing_charge->currency->id){
                //Marking as joint under charge currency
                $charge->setAttribute('joint_as','charge_currency');

                $joint_amount = $charge->adaptable_total + $comparing_charge->total;
                $charge->adaptable_total = $joint_amount;
                $charge->adaptable_total_client_currency = $this->convertToCurrency($charge->currency, $client_currency, array($joint_amount))[0];
            }

            return true;
        //CASE 4: Original charge NOT adaptable, Comparing charge adaptable, types differ - DONT JOIN, ONLY MODIFIES THE COMPARING ADAPTABLE AMOUNT
        }elseif( !$calculation_options['adaptable'] && $comparing_calculation_options['adaptable'] && $calculation_options['type'] != $comparing_calculation_options['type'] ){
            //If currencies don't match, join must be done in client currency
            if($charge->currency->id != $comparing_charge->currency->id){
                $joint_amount = $charge->total_client_currency + $comparing_charge->adaptable_total_client_currency;
                $comparing_charge->adaptable_total_client_currency = $joint_amount;
            //If currencies match, add is direct
            }elseif($charge->currency->id == $comparing_charge->currency->id){
                $joint_amount = $charge->total + $comparing_charge->adaptable_total;
                $comparing_charge->adaptable_total = $joint_amount;
                $comparing_charge->adaptable_total_client_currency = $this->convertToCurrency($comparing_charge->currency, $client_currency, array($joint_amount))[0];
            }

            return false;
        }**/

        return false;
    }

    public function checkLclAdaptable($charges)
    {
        $final_charges = [];

        foreach($charges as $direction=>$charges_direction){
            $has_ton_adaptable = false;
            $has_m3_adaptable = false;

            foreach($charges_direction as $key=>$charge){
                $calculation_options = json_decode($charge->calculationtypelcl->options, true);

                if($calculation_options['adaptable']){
                    if($calculation_options['type'] == 'ton'){
                        $has_ton_adaptable = true;
                        $ton_adaptable_charge = $charge;
                        unset($charges_direction[$key]);
                    }elseif($calculation_options['type'] == 'm3'){
                        $has_m3_adaptable = true;
                        $m3_adaptable_charge = $charge;
                        unset($charges_direction[$key]);
                    }
                }
            }

            if($has_ton_adaptable && $has_m3_adaptable){
                if($m3_adaptable_charge->total_client_currency > $ton_adaptable_charge->total_client_currency){
                    array_push($charges_direction, $m3_adaptable_charge);
                }else{
                    array_push($charges_direction, $ton_adaptable_charge);
                }
            }elseif($has_m3_adaptable && !$has_ton_adaptable){
                array_push($charges_direction, $m3_adaptable_charge);
            }elseif(!$has_m3_adaptable && $has_ton_adaptable){
                array_push($charges_direction, $ton_adaptable_charge);
            }
            
            $final_charges[$direction] = $charges_direction;
        }

        return $final_charges;
    }

    //appending charges to corresponding Rate
    public function addChargesToRate($rate, $target, $search_data)
    {
        $client_currency = $search_data['client_currency'];
        $rate_charges = [];
        //Looping through charges type for array structure
        foreach ($target as $direction => $charge_direction) {
            $rate_charges[$direction] = [];

            //Looping through charges by type
            foreach ($charge_direction as $charge) {
                if(!$charge->hide){   
                    array_push($rate_charges[$direction], $charge);
                }
                
                if($direction == 'Freight'){
                    if ($charge->joint_as == 'client_currency') {
                        
                        if($search_data['type'] == 'FCL'){
                            $rate_currency_containers = $this->convertToCurrency($client_currency, $rate->currency, $charge->containers_client_currency);
                            $charge->containers_client_currency = $rate_currency_containers;
                        }elseif($search_data['type'] == 'LCL'){
                            $rate_currency_total = $this->convertToCurrency($client_currency, $rate->currency, array($charge->total_client_currency));
                            $charge->total_client_currency = $rate_currency_total[0];
                        }
                    }
                }
            }

            if ($direction == 'Freight') {
                if($search_data['type'] == 'FCL'){
                    $ocean_freight_array = [
                        'surcharge' => ['name' => 'Ocean Freight'],
                        'containers' => json_decode($rate->containers, true),
                        'calculationtype' => ['name' => 'Per Container', 'id' => '5'], 
                        'typedestiny_id' => 3,
                        'currency' => ['alphacode' => $rate->currency->alphacode, 'id' => $rate->currency->id]
                    ];
                }elseif($search_data['type'] == 'LCL'){
                    $ocean_freight_array = [
                        'surcharge' => $rate->surcharge,
                        'total' => $rate->total,
                        'minimum' => $rate->minimum,
                        'units' => $rate->calculation_type->name == "Per Shipment" ? 1 : $search_data['chargeableWeight'],
                        'ammount' => $rate->uom > $rate->minimum ? $rate->uom : $rate->minimum,
                        'calculationtypelcl' => $rate->calculation_type, 
                        'typedestiny_id' => 3,
                        'currency' => $rate->currency,
                    ];
                }

                $ocean_freight_collection = collect($ocean_freight_array);

                array_push($rate_charges[$direction], $ocean_freight_collection);
            }

            if (count($rate_charges[$direction]) == 0) {
                unset($rate_charges[$direction]);
            };
        }
        $rate->setAttribute('charges', $rate_charges);
    }

    //Retrieves Global Remarks
    public function searchRemarks($rate, $search_data)
    {
        //Retrieving current companyto filter remarks
        $company_user = CompanyUser::where('id', $search_data['company_user'])->first();

        $origin_country = $rate->port_origin->country()->first();
        $destination_country = $rate->port_destiny->country()->first();
        $rate_countries_id = [ $origin_country->id, $destination_country->id, 250];

        $rate_ports_id = [$rate->origin_port, $rate->destiny_port , 1485];

        $rate_carriers_id = [$rate->carrier_id, 26];
        
        $remarks = RemarkCondition::where('company_user_id', $company_user->id)->whereHas('remarksCarriers', function ($q) use ($rate_carriers_id) {
            $q->whereIn('carrier_id', $rate_carriers_id);
        })->where(function ($query) use ($rate_countries_id, $rate_ports_id) {
            $query->orwhereHas('remarksHarbors', function ($q) use ($rate_ports_id) {
                $q->whereIn('port_id', $rate_ports_id);
            })->orwhereHas('remarksCountries', function ($q) use ($rate_countries_id) {
                $q->whereIn('country_id', $rate_countries_id);
            });
        })->get();

        $final_remarks = "";
        $included_contracts = [];
        $included_global_remarks = [];

        foreach ($remarks as $remark) {
            if ($search_data['direction'] == 1 && !in_array($remark->id, $included_global_remarks)) {
                $final_remarks .= $remark->import . "<br>";
                array_push($included_global_remarks, $remark->id);
            } elseif ($search_data['direction'] == 2 && !in_array($remark->id, $included_global_remarks)) {
                $final_remarks .= $remark->export . "<br>";
                array_push($included_global_remarks, $remark->id);
            }
        }

        if (!in_array($rate->contract_id, $included_contracts)) {
            $final_remarks .= $rate->contract->remarks . '<br>';
            array_push($included_contracts, $rate->contract->id);
        }

        return $final_remarks;
    }


    //Retrives global Transit Times
    public function searchTransitTime($rate)
    {
        //Setting values fo query
        $origin_port = $rate->origin_port;
        $destination_port = $rate->destiny_port;
        $carrier = $rate->carrier_id;

        //Querying
        $transit_time = TransitTime::where([['origin_id',$origin_port],['destination_id',$destination_port]])->whereIn('carrier_id',[$carrier,26])->first();

        return $transit_time;
    }

    //Converting amounts to string so they display decimal places correctly
    public function stringifyFclRateAmounts($rate)
    {
        //RATE TOTALS GLOBAL
        if(isset($rate->totals_with_markups)){
            $totals_with_markups_string = $rate->totals_with_markups;
            foreach($totals_with_markups_string as $key => $total){
                $totals_with_markups_string[$key] = strval(isDecimal($total, true));
            }

            $rate->totals_with_markups = $totals_with_markups_string;
        }

        if(isset($rate->containers_with_markups)){
            $containers_with_markups_string = $rate->containers_with_markups;
            foreach($containers_with_markups_string as $key => $total){
                $containers_with_markups_string[$key] = strval(isDecimal($total, true));
            }

            $rate->containers_with_markups = $containers_with_markups_string;
        }
        
        if(isset($rate->totals_with_markups_freight_currency)){
            $totals_with_markups_freight_currency_string = $rate->totals_with_markups_freight_currency;
            foreach($totals_with_markups_freight_currency_string as $key => $total){
                $totals_with_markups_freight_currency_string[$key] = strval(isDecimal($total, true));
            }

            $rate->totals_with_markups_freight_currency = $totals_with_markups_freight_currency_string;
        }

        if(isset($rate->totals_freight_currency)){
            $totals_freight_currency_string = $rate->totals_freight_currency;
            foreach($totals_freight_currency_string as $key => $total){
                $totals_freight_currency_string[$key] = strval(isDecimal($total, true));
            }

            $rate->totals_freight_currency = $totals_freight_currency_string;
        }

        $totals_string = $rate->totals;
        foreach($totals_string as $key => $total){
            $totals_string[$key] = strval(isDecimal($total, true));
        }

        $rate->totals = $totals_string;

        //RATE TOTALS BY TYPE 
        $by_type = $rate->charge_totals_by_type;
        
        foreach($by_type as $typeKey => $type){
            foreach($type as $key => $total){
                $by_type[$typeKey][$key] = strval(isDecimal($total, true));
            }
        }

        $rate->charge_totals_by_type = $by_type;

        //CHARGES
        foreach($rate->charges as $direction => $charge_direction){
            foreach($charge_direction as $chargeKey => $charge){
                if(isset($charge->surcharge)){
                    //Plain Container prices
                    $charge_containers_string = $charge->containers;
    
                    foreach($charge_containers_string as $container => $containerTotal){
                        $charge_containers_string[$container] = strval(isDecimal($containerTotal,true));
                    }
    
                    $charge->containers = $charge_containers_string;
    
                    //Containers in client currency
                    $charge_totals_string = $charge->containers_client_currency;
    
                    foreach($charge_totals_string as $container => $containerTotal){
                        $charge_totals_string[$container] = strval(isDecimal($containerTotal,true));
                    }
    
                    $charge->containers_client_currency = $charge_totals_string;
    
                    //Checking if markups
                    if(isset($charge->containers_with_markups)){
                        //Containers With Markups
                        $charge_containers_with_markups_string = $charge->containers_with_markups;
    
                        foreach($charge_containers_with_markups_string as $container => $containerTotal){
                            $charge_containers_with_markups_string[$container] = strval(isDecimal($containerTotal,true));
                        }
        
                        $charge->containers_with_markups = $charge_containers_with_markups_string;
    
                        //Containers with markups in client currency
                        $charge_totals_with_markups_string = $charge->totals_with_markups;
                        
                        foreach($charge_totals_with_markups_string as $container => $containerTotal){
                            $charge_totals_with_markups_string[$container] = strval(isDecimal($containerTotal,true));
                        }
        
                        $charge->totals_with_markups = $charge_totals_with_markups_string;
                    }
                }else{
                    $charge_containers_string = $charge['containers'];
    
                    foreach($charge_containers_string as $container => $containerTotal){
                        $charge_containers_string[$container] = strval(isDecimal($containerTotal,true));
                    }
    
                    $charge['containers'] = $charge_containers_string;

                    if(isset($charge['containers_with_markups'])){
                        //Containers With Markups
                        $charge_containers_with_markups_string = $charge['containers_with_markups'];
    
                        foreach($charge_containers_with_markups_string as $container => $containerTotal){
                            $charge_containers_with_markups_string[$container] = strval(isDecimal($containerTotal,true));
                        }
        
                        $charge['containers_with_markups'] = $charge_containers_with_markups_string;
                    }
                }
            }
        }
    }

    public function stringifyLclRateAmounts($rate)
    {
        //RATE TOTALS GLOBAL
        if(isset($rate->total_with_markups)){
            $total_with_markups_string = strval(isDecimal($rate->total_with_markups, true));
            $rate->total_with_markups = $total_with_markups_string;
        }
        
        if(isset($rate->total_with_markups_freight_currency)){
            $total_with_markups_freight_currency_string = strval(isDecimal($rate->total_with_markups_freight_currency, true));
            $rate->total_with_markups_freight_currency = $total_with_markups_freight_currency_string;
        }

        if(isset($rate->total_freight_currency)){
            $total_freight_currency_string = strval(isDecimal($rate->total_freight_currency, true));
            $rate->total_freight_currency = $total_freight_currency_string;
        }

        $total_string = strval(isDecimal($rate->total, true));
        $rate->total = $total_string;

        //RATE TOTALS BY TYPE 
        $by_type = $rate->charge_totals_by_type;
        
        foreach($by_type as $typeKey => $typeAmount){
            $by_type[$typeKey] = strval(isDecimal($typeAmount, true));
        }

        $rate->charge_totals_by_type = $by_type;

        //CHARGES
        foreach($rate->charges as $direction => $charge_direction){
            foreach($charge_direction as $chargeKey => $charge){
                if(isset($charge->surcharge)){
                    //Plain prices
                    $charge_total_string = strval(isDecimal($charge->total,true));    
                    $charge->total = $charge_total_string;
    
                    //Prices in client currency
                    $charge_total_client_currency_string = strval(isDecimal($charge->total_client_currency,true));    
                    $charge->total_client_currency = $charge_total_client_currency_string;
    
                    //Checking if markups
                    if(isset($charge->total_with_markups)){
                        //Plain prices
                        $charge_total_with_markups_string = strval(isDecimal($charge->total_with_markups,true));    
                        $charge->total_with_markups = $charge_total_with_markups_string;
        
                        //Prices in client currency
                        $charge_total_with_markups_client_currency_string = strval(isDecimal($charge->total_with_markups_client_currency,true));    
                        $charge->total_with_markups_client_currency = $charge_total_with_markups_client_currency_string;
                        }
                }else{
                    //Plain prices
                    $charge_total_string = strval(isDecimal($charge['total'],true));    
                    $charge['total'] = $charge_total_string;

                    if(isset($charge['total_with_markups'])){
                        //Plain prices
                        $charge_total_string = strval(isDecimal($charge['total_with_markups'],true));    
                        $charge['total_with_markups'] = $charge_total_string;
                    }
                }
            }
        }
    }

    public function setDownloadParameters($rate, $search_data)
    {
        if ($rate->contract->status != 'api') {

            if( $search_data['type'] == 'FCL'){
                $contractRequestBackup = ContractFclFile::where('contract_id', $rate->contract->id)->first();
                $contractRequest = NewContractRequest::where('contract_id', $rate->contract->id)->first();
            }elseif( $search_data['type'] == 'LCL'){
                $contractRequestBackup = ContractLclFile::where('contractlcl_id', $rate->contract->id)->first();
                $contractRequest = NewContractRequestLcl::where('contract_id', $rate->contract->id)->first();
            }

            if (!empty($contractRequestBackup)) {
                $contractBackupId = $contractRequestBackup->id;
            } else {
                $contractBackupId = "0";
            }

            if (!empty($contractRequest)) {
                $contractRequestId = $contractRequest->id;
            } else {
                $contractRequestId = "0";
            }

            $mediaItems = $rate->contract->getMedia('document');
            $totalItems = count($mediaItems);
            if ($totalItems > 0) {
                $contractId = $rate->contract->id;
            }else{
                $contractId = "0";
            }
        }else{
            $contractBackupId = "0";
            $contractRequestId = "0";
            $contractId = "0";
        }

        $rate->setAttribute('contractBackupId', $contractBackupId);
        $rate->setAttribute('contractRequestId', $contractRequestId);
        $rate->setAttribute('contractId', $contractId);
    }

    public function downloadContractFromSearch($rate)
    {
        if(isset($rate['contract_id'])){
            $contractId = $rate['contract_id'];
            $type = 'FCL';
        }elseif(isset($rate['contractlcl_id'])){
            $contractId = $rate['contractlcl_id'];
            $type = 'LCL';
        }
        $contractRequestId = $rate['contract_request_id'];
        $contractBackupId = $rate['contract_backup_id'];

        if ($contractId == 0) {
            if($type == 'FCL'){
                $contractFile = NewContractRequest::find($contractRequestId);
            }elseif($type == 'LCL'){
                $contractFile = NewContractRequestLcl::find($contractRequestId);
            }
            $mode_search = false;
            if (!empty($contractFile)) {
                $success = false;
                $download = null;
                if (!empty($contractFile->namefile)) {
                    $time = new \DateTime();
                    $now = $time->format('d-m-y');
                    $company = CompanyUser::find($contractFile->company_user_id);
                    $extObj = new \SplFileInfo($contractFile->namefile);
                    $ext = $extObj->getExtension();
                    $name = $contractFile->id . '-' . $company->name . '_' . $now . '-' . $type . '.' . $ext;
                } else {
                    $mode_search = true;
                    $contractFile->load('companyuser');
                    $data = json_decode($contractFile->data, true);
                    $time = new \DateTime();
                    $now = $time->format('d-m-y');
                    $mediaItem = $contractFile->getFirstMedia('document');
                    $extObj = new \SplFileInfo($mediaItem->file_name);
                    $ext = $extObj->getExtension();
                    $name = $contractFile->id . '-' . $contractFile->companyuser->name . '_' . $data['group_containers']['name'] . '_' . $now . '-' . $type . '.' . $ext;
                    $download = Storage::disk('s3_upload')->url('Request/' . $type . '/' . $mediaItem->id . '/' . $mediaItem->file_name, $name);
                    $success = true;
                }
            } else {
                if($type == 'FCL'){
                    $contractFile = ContractFclFile::find($contractBackupId);
                    $request_location = 'FclRequest';
                }elseif($type == 'LCL'){
                    $contractFile = ContractLclFile::find($contractBackupId);
                    $request_location = 'LclRequest';
                }
                $time = new \DateTime();
                $now = $time->format('d-m-y');
                $extObj = new \SplFileInfo($contractFile->namefile);
                $ext = $extObj->getExtension();
                $name = $contractFile->id . '-' . $now . '-' . $type . '.' . $ext;
            }

            if ($mode_search == false) {
                if (Storage::disk('s3_upload')->exists('Request/' . $type . '/' . $contractFile->namefile, $name)) {
                    $success = true;
                    $download = Storage::disk('s3_upload')->url('Request/' . $type . '/' . $contractFile->namefile, $name);
                } elseif (Storage::disk('s3_upload')->exists('contracts/' . $contractFile->namefile, $name)) {
                    $success = true;
                    $download = Storage::disk('s3_upload')->url('contracts/' . $contractFile->namefile, $name);
                } elseif (Storage::disk($request_location)->exists($contractFile->namefile, $name)) {
                    $success = true;
                    $download = Storage::disk($request_location)->url($contractFile->namefile, $name);
                } elseif (Storage::disk('UpLoadFile')->exists($contractFile->namefile, $name)) {
                    $success = true;
                    $download = Storage::disk('UpLoadFile')->url($contractFile->namefile, $name);
                }
            }
            return response()->json(['success' => $success, 'url' => $download,'zip'=>false ]);
        } else {
            if($type == 'FCL'){
                $contract = Contract::find($contractId);
                $request_location = 'FclRequest';
            }elseif($type == 'LCL'){
                $contract = ContractLcl::find($contractId);
                $request_location = 'LclRequest';
            }
            $downloads = $contract->getMedia('document');
            $total = count($downloads);
            if ($total > 1) {                                         
                
                return response()->json(['success' => true, 'url' => $contract->id,'zip'=>true ]);
            } else {
                $media = $downloads->first();
                $mediaItem = Media::find($media->id);
                //return $mediaItem;
                if($mediaItem->disk == $request_location){
                    return response()->json(['success' => true, 'url' => "https://cargofive-production-21.s3.eu-central-1.amazonaws.com/Request/" . $type . '/' .$mediaItem->file_name,'zip'=>false ]);
                }
                if($mediaItem->disk == 'contracts3'){
                    return response()->json(['success' => true, 'url' => "https://cargofive-production-21.s3.eu-central-1.amazonaws.com/contract_manual/".$mediaItem->id."/".$mediaItem->file_name,'zip'=>false ]);
                }
            }
        }
    }

    //Ordering rates by totals (cheaper to most expensive)
    public function sortRates($rates, $search_data_ids)
    {
        if($search_data_ids['type'] == 'FCL'){
            if (isset($search_data_ids['pricelevel'])) {
                $sortBy = 'totals_with_markups';
            } else {
                $sortBy = 'totals';
            }
        }elseif($search_data_ids['type'] == 'LCL'){
            if (isset($search_data_ids['pricelevel'])) {
                $sortBy = 'total_with_markups';
            } else {
                $sortBy = 'total';
            }
        }

        $sorted = $rates->sortBy($sortBy)->values();

        return ($sorted);
    }

    //Retrieves Terms and Conditions
    public function searchTerms($search_data)
    {
        //Retrieving current companyto filter terms
        $company_user = CompanyUser::where('id', $search_data['company_user'])->first();

        $terms = TermAndConditionV2::where([['company_user_id',$company_user->id],['type',$search_data['type']]])->get();

        $terms_english = '';
        $terms_spanish = '';
        $terms_portuguese = '';

        foreach($terms as $term){

            if($search_data['direction'] == 1){
                $terms_to_add = $term->import;
            }else if($search_data['direction'] == 2){
                $terms_to_add = $term->export;
            }

            if($term->language_id == 1){
                $terms_english .= $terms_to_add . '<br>';
            }else if($term->language_id == 2){
                $terms_spanish .= $terms_to_add . '<br>';
            }else if($term->language_id == 3){
                $terms_portuguese .= $terms_to_add . '<br>';
            }
        }

        $final_terms = ['english' => $terms_english, 'spanish' => $terms_spanish, 'portuguese' => $terms_portuguese ];

        return $final_terms;
    }

    //Clears date in 2021-07-13T01:00:00 format. Options can be:
        // time -> returns only time, no date
        // date -> returns only date, no time
    public function formatSearchDate($date, $option)
    {
        if($option == 'time'){
            $date = substr($date, 11, 8);
        }else if($option == 'date'){
            $date = substr($date, 0, 10);
        }

        return $date;
    }
}
