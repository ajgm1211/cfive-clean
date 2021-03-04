<?php

namespace App\Http\Traits;

use App\CalculationType;
use App\CompanyUser;
use App\Currency;
use App\Inland;
use App\Price;
use App\Harbor;
use App\Country;
use App\TransitTime;
use App\Container;
use App\GroupContainer;
use GoogleMaps;
use Illuminate\Support\Collection as Collection;

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

                        $arregloInland = ['prov_id' => $inlandsValue->id, 'provider' => 'Inland Haulage', 'providerName' => $inlandsValue->provider, 'port_id' => $ports->ports->id, 'port_name' => $ports->ports->name, 'port_id' => $ports->ports->id, 'validity_start' => $inlandsValue->validity, 'validity_end' => $inlandsValue->expire, 'km' => $distancia, 'monto' => $monto, 'type' => $textType, 'type_currency' => $inlandDetails->first()['currency'], 'idCurrency' => $typeCurrency];
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

            $monto = $monto / $rateFreight;
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

    //Returns array containing group ids present in a container ids array
    public function getEquipmentGroups(Array $equipment)
    {
        $container_groups = Array();

        foreach($equipment as $container_id){
            $container = Container::where('id',$container_id)->first();
            if(!in_array($container['gp_container_id'],$container_groups)){
                array_push($container_groups,$container['gp_container_id']);
            }
        }

        return $container_groups;
    }

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
                $local_charge_amount = isDecimal($local_charge_amount[0], true);
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

    //If rate data comes separate from mode (twuenty, forty, etc) joins them under the "containers" field 
    //ONLY FOR DRY CONTAINERS
    public function joinRateContainers($rates, $search_containers)
    {
        foreach($rates as $rate){
            $container_array = [];
            $container_group_id = $rate->contract->gp_container_id;
            $group_containers = Container::where('gp_container_id',$container_group_id)->get();
            $requested_containers = [];

            if($container_group_id == 1){
                foreach($group_containers as $cont){
                    if(in_array($cont->id,$search_containers)){
                        array_push($requested_containers, $cont->code);
                    }
                }
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
            }
        }
    }

    //Gets country for given port. Output depends on input. It returns ID if ID given, and MODEL if MODEL given
    public function getPortCountry($port)
    {
        //Checking if MODEL
        if(is_a($port,'App\Harbor')){
            //Retrieving country model by id
            $country = Country::where('id',$port->country_id)->first();
            //Checking if int (ID)
        }elseif(is_int($port)){
            //Retrieving port model
            $port = Harbor::where('id',$port)->first();
            //Retrieving country by id and getting ID
            $country = Country::where('id',$port->country_id)->first()->id;
        }

        return $country;
    }

    //groups local + global charges by type (Origin, Destination, Freight)
    public function groupChargesByType($local_charges, $global_charges)
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
        $charges->put('Origin',$origin);
        $charges->put('Destination',$destination);
        $charges->put('Freight',$freight);
        
        return $charges;
    }

    //Get charges per container from calculation type - inputs a charge collection, outputs ordered collection
    public function setChargesPerContainer($charges, $containers, $company_user_id)
    {
        //Retrieving current company
        $company_user = CompanyUser::where('id',$company_user_id)->first();
    
        //getting client profile currency
        $client_currency = $company_user->currency;

        //Looping through charges collection
        foreach($charges as $charges_direction){
            foreach($charges_direction as $charge){

                //Getting calculation info from calculation type id
                $calculation = CalculationType::where('id',$charge->calculationtype_id)->first();

                //Setting arrays for different calculation types, for matching when building final arrays
                $teu_calculations = ['TEU','TEU RF','TEU OT','TEU FR'];
                $container_calculations = ['CONT','CONT RF','CONT OT','CONT FR','SHIP'];

                //Empty array for storing final charges
                $container_charges = [];

                //Checking through the different types of calculation
                    //TEU calculations -> if a container's 'is_teu' option is true, rates are doubled 
                if(in_array($calculation->code,$teu_calculations)){
                    foreach($containers as $container){
                        $options = json_decode($container['options'],true);
                        if(isset($options['is_teu']) && $options['is_teu']){
                            $container_charges['C'.$container['code']] = 2 * $charge->ammount;
                        }else{
                            $container_charges['C'.$container['code']] = $charge->ammount;
                        }
                    }
                }elseif(in_array($calculation->code,$container_calculations)){
                //Calculations that apply to ALL containers
                    foreach($containers as $container){
                        $container_charges['C'.$container['code']] = $charge->ammount;
                    }
                //Individual container calculations
                }else{
                    //Catching poorly formatted calculation codes
                    if($calculation->code == '40'){
                        $container_charges['C40DV'] = $charge->ammount; 
                    }elseif($calculation->code == '20'){
                        $container_charges['C20DV'] = $charge->ammount; 
                    }elseif($calculation->code == '45'){
                        $container_charges['C45HC'] = $charge->ammount; 
                    }elseif($calculation->code == '20R'){
                        $container_charges['C20RF'] = $charge->ammount;
                    //Catching when calculation codes match container codes 
                    }else{
                        $container_charges['C'.$calculation->code] = $charge->ammount;
                    }
                }

                foreach($containers as $code => $container){
                    if(!isset($container_charges['C'.$container['code']])){
                        $container_charges['C'.$container['code']] = 0;
                    }
                }

                //Setting rates per container
                    //In unmodified currency, for general use
                    //In client currency to show in overall totals
                $client_currency_charges = $this->convertToCurrency($charge->currency,$client_currency,$container_charges);

                $charge->setAttribute('containers_client_currency',$client_currency_charges);
                
                $charge->setAttribute('containers',$container_charges);
            }
        }
    }

    //Joining charges where surcharge, carrier and ports match; when join, amounts are added together
    public function joinCharges($charges)
    {
        //Empty array for joint charges
        $joint_charges = [];

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
                    //Index of present charge in its array, for control purposes
                    $original_charge_index = array_search($charge,$charges_direction);
                    //Control variable that indicates whether a charge has been matched and joint
                    $charge_matched = false;
                    //Looping through duplicate array
                    foreach($comparing_array as $comparing_charge){
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
                                //Converting compared array container rates into corresponding currency:
                                    //If currencies don't match, join must be done in client currency
                                    if($charge->currency->id != $comparing_charge->currency->id){
                                        $joint_containers = $charge->containers_client_currency;
                                        //Marking charge as joint under client currency
                                        $charge->setAttribute('joint_as','client_currency');
                                        $comparing_charge_containers = $comparing_charge->containers_client_currency;
                                    //If currencies match, sum is direct
                                    }elseif($charge->currency->id == $comparing_charge->currency->id){
                                        $joint_containers = $charge->containers;
                                        //Marking as joint under charge currency
                                        $charge->setAttribute('joint_as','charge_currency');
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
                                        }
                                        
                                    }
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
                    //Checking if original charge hasnt been matched and joint
                    if(!$charge_matched && !in_array($original_charge_index,$compared_and_joint)){
                        //Including unjoint charge in final array
                        $charge->setAttribute('joint_as', 'charge_currency');
                        array_push($joint_charges[$direction],$charge);
                    }
                }
            }
        }

        return($joint_charges);
    }
}
