<?php
namespace App\Http\Traits;

use App\CalculationType;
use App\Currency;
use App\Inland;
use App\Price;
use GoogleMaps;
use Illuminate\Support\Collection as Collection;
trait SearchTrait
{

    public function inlands($inlandParams, $markup, $equipment, $contain, $type, $mode)
    {

        $modality_inland = $mode; // FALTA AGREGAR EXPORT
        $company_inland = $inlandParams['company_id_quote'];

        $company_user_id = $inlandParams['company_user_id'];
        $address = $inlandParams['destination_address'];
        $typeCurrency = $inlandParams['typeCurrency'];

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
        })->where('company_user_id', '=', $company_user_id)->with('inlandadditionalkms', 'inlandports.ports', 'inlanddetails.currency');

        $inlands->where(function ($query) use ($modality_inland) {
            $query->where('type', $modality_inland)->orwhere('type', '3');
        });

        $inlands = $inlands->get();
        $dataDest = array();
        // se agregan los aditional km
        foreach ($inlands as $inlandsValue) {

            $inlandDetails = array();
            foreach ($inlandsValue->inlandports as $ports) {
                $monto = 0;
                if (in_array($ports->ports->id, $port)) {

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
                    foreach ($var->routes as $resp) {
                        foreach ($resp->legs as $dist) {

                            $km = explode(" ", $dist->distance->text);
                            $distancia = str_replace(".", "", $km[0]);
                            $distancia = floatval($distancia);
                            if ($distancia < 1) {
                                $distancia = 1;
                            }

                            foreach ($inlandsValue->inlanddetails as $details) {
                                $rateI = $this->ratesCurrency($details->currency->id, $typeCurrency);

                                foreach ($contain as $cont) {

                                    $km = 'km' . $cont->code;
                                    $$km = true;
                                    $options = json_decode($cont->options);
                                    if (@$options->field_rate != 'containers') {
                                        $tipo = $options->field_rate;
                                    } else {
                                        $tipo = $cont->code;
                                    }

                                    if ($details->type == $tipo && in_array($cont->id, $equipment)) {

                                        if ($distancia >= $details->lower && $distancia <= $details->upper) {

                                            $sub_20 = number_format($details->ammount / $rateI, 2, '.', '');
                                            $monto += number_format($sub_20, 2, '.', '');
                                            $amount_inland = number_format($details->ammount, 2, '.', '');
                                            $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                                            $$km = false;
                                            // CALCULO MARKUPS
                                            $markupI20 = $this->inlandMarkup($markup['inland']['inlandPercentage'], $markup['inland']['inlandAmmount'], $markup['inland']['inlandMarkup'], $sub_20, $typeCurrency, $markup['inland']['inlandMarkup']);

                                            // FIN CALCULO MARKUPS
                                            $arrayInland20 = array("cant_cont" => '1', "sub_in" => $sub_20, 'amount' => $amount_inland, 'currency' => $details->currency->alphacode, 'price_unit' => $price_per_unit, 'typeContent' => $cont->code);
                                            $arrayInland20 = array_merge($markupI20, $arrayInland20);
                                            $inlandDetails[] = $arrayInland20;
                                        }
                                    }
                                }
                            }
                            // KILOMETROS ADICIONALES

                            if (isset($inlandsValue->inlandadditionalkms)) {

                                $rateGeneral = $this->ratesCurrency($inlandsValue->inlandadditionalkms->currency_id, $typeCurrency);

                                foreach ($contain as $cont) {

                                    $km = 'km' . $cont->code;
                                    $options = json_decode($cont->options);
                                    $texto20 = 'Inland ' . $cont->code . ' x 1';

                                    if (isset($options->field_inland)) {

                                        if ($$km && in_array($cont->id, $equipment)) {

                                            $montoKm = ($distancia * $inlandsValue->inlandadditionalkms->{$options->field_inland}) / $rateGeneral;
                                            $sub_20 = number_format($montoKm, 2, '.', '');
                                            $monto += $sub_20;
                                            $amount_inland = $distancia * $inlandsValue->inlandadditionalkms->{$options->field_inland};
                                            $price_per_unit = number_format($amount_inland / $distancia, 2, '.', '');
                                            $amount_inland = number_format($amount_inland, 2, '.', '');
                                            // CALCULO MARKUPS
                                            $markupI20 = $this->inlandMarkup($markup['inland']['inlandPercentage'], $markup['inland']['inlandAmmount'], $markup['inland']['inlandMarkup'], $sub_20, $typeCurrency, $markup['inland']['inlandMarkup']);

                                            // FIN CALCULO MARKUPS
                                            $sub_20 = number_format($sub_20, 2, '.', '');
                                            $arrayInland20 = array("cant_cont" => '1', "sub_in" => $sub_20, "des_in" => $texto20, 'amount' => $amount_inland, 'currency' => $inlandsValue->inlandadditionalkms->currency->alphacode, 'price_unit' => $price_per_unit, 'typeContent' => $cont->code);
                                            $arrayInland20 = array_merge($markupI20, $arrayInland20);
                                            $inlandDetails[] = $arrayInland20;
                                        }
                                    }
                                }
                            }

                            $monto = number_format($monto, 2, '.', '');

                            if ($monto > 0) {
                                $inlandDetails = Collection::make($inlandDetails);
                                $arregloInland = array("prov_id" => $inlandsValue->id, "provider" => "Inland Haulage", "providerName" => $inlandsValue->provider, "port_id" => $ports->ports->id, "port_name" => $ports->ports->name, 'port_id' => $ports->ports->id, 'validity_start' => $inlandsValue->validity, 'validity_end' => $inlandsValue->expire, "km" => $distancia, "monto" => $monto, 'type' => 'Destination', 'type_currency' => $inlandsValue->inlandadditionalkms->currency->alphacode, 'idCurrency' => $inlandsValue->currency_id);
                                $arregloInland['inlandDetails'] = $inlandDetails->groupBy('typeContent')->map(function ($item) {
                                    $minimoD = $item->where('sub_in', '>', 0);
                                    $minimoDetails = $minimoD->where('sub_in', $minimoD->min('sub_in'))->first();
                                    return $minimoDetails;
                                });

                                $dataDest[] = $arregloInland;

                            }
                        }
                    }
                }
            }
        }
        return $dataDest;
    }

    // Metodos para los rates
    public function rates($equipment, $markup, $data, $rateC, $typeCurrency, $contain)
    {

        $arreglo = array();
        $arregloRate = array();
        $arregloSaveR = array();
        $arregloSaveM = array();
        $equipmentFilter = array();
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
                    $arreglo = $this->detailRate($markup, $rateMount, $data, $rateC, $typeCurrency, $cont->code);
                    $arregloRate = array_merge($arreglo['arregloRate'], $arregloRate);
                    $arregloSaveR = array_merge($arreglo['arregloRateSaveR'], $arregloSaveR);
                    $arregloSaveM = array_merge($arreglo['arregloRateSaveM'], $arregloSaveM);
                    if ($rateMount != 0) {
                        array_push($equipmentFilter, $containers);
                    }

                }
            }
        }

        $arregloG = array('arregloRate' => $arregloRate, 'arregloSaveR' => $arregloSaveR, 'arregloSaveM' => $arregloSaveM, 'arregloEquipment' => $equipmentFilter);
        return $arregloG;

    }

    public function detailRate($markup, $amount, $data, $rateC, $typeCurrency, $containers)
    {

        $arregloRateSave['rate'] = array();
        $arregloRateSave['markups'] = array();
        $arregloRate = array();

        $markup = $this->freightMarkups($markup['freight']['freighPercentage'], $markup['freight']['freighAmmount'], $markup['freight']['freighMarkup'], $amount, $typeCurrency, $containers);

        $tot_F = $markup['monto' . $containers] / $rateC;
        //Formato decimal
        $tot_F = number_format($tot_F, 2, '.', '');
        $amount = number_format($amount, 2, '.', '');

        $arrayDetail = array('price' . $containers => $amount, 'currency' . $containers => $data->currency->alphacode, 'idCurrency' . $containers => $data->currency_id, 'total' . $containers => $tot_F);

        // Arreglos para guardar los rates
        $array_save = array('c' . $containers => $amount);

        $arregloRateSave['rate'] = array_merge($array_save, $arregloRateSave['rate']);
        // Markups
        $array_markup = array('m' . $containers => $markup['markup' . $containers]);
        $arregloRateSave['markups'] = array_merge($array_markup, $arregloRateSave['markups']);

        $array = array_merge($arrayDetail, $markup);
        $arregloRate = array_merge($array, $arregloRate);

        $arreglo = array('arregloRate' => $arregloRate, 'arregloRateSaveR' => $arregloRateSave['rate'], 'arregloRateSaveM' => $arregloRateSave['markups']);

        return $arreglo;
    }

    // Metodos Para los localcharges

    public function ChargesArray($params, $monto, $montoOrig, $type)
    {

        $local = $params['local'];
        $data = $params['data'];
        $localCarrier = $params['localCarrier'];

        $arreglo = array('surcharge_terms' => $params['terminos'], 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => $monto, 'currency' => $local->currency->alphacode, 'calculation_name' => $local->calculationtype->name, 'contract_id' => $data->contract_id, 'carrier_id' => $localCarrier->carrier_id, 'type' => $type, 'rate_id' => $data->id, 'montoOrig' => $montoOrig, 'typecurrency' => $params['typeCurrency'], 'currency_id' => $local->currency->id, 'currency_orig_id' => $params['idCurrency']);
        return $arreglo;
    }

    public function ChargesArray99($params, $calculation_id, $calculation_name)
    {

        $local = $params['local'];
        $data = $params['data'];
        $localCarrier = $params['localCarrier'];

        $arreglo = array('surcharge_terms' => $params['terminos'], 'surcharge_id' => $local->surcharge->id, 'surcharge_name' => $local->surcharge->name, 'monto' => 0.00, 'markup' => 0.00, 'montoMarkup' => 0.00, 'montoMarkupO' => 0.00, 'currency' => $local->currency->alphacode, 'calculation_name' => $calculation_name, 'contract_id' => $data->contract_id, 'carrier_id' => $localCarrier->carrier_id, 'type' => '99', 'rate_id' => $data->id, 'calculation_id' => $calculation_id, 'montoOrig' => 0.00, 'typecurrency' => $params['typeCurrency'], 'currency_id' => $local->currency->id, 'currency_orig_id' => $params['idCurrency'], 'markupConvert' => 0.00);

        return $arreglo;
    }

    public function asociarPerCont($calculation_id)
    {

        $calculation = CalculationType::get();
        $valor = array();
        $gp_id = $calculation->where('id', $calculation_id)->first();
        if ($gp_id->group_container_id != 0) {

            $grupo = $calculation->where('group_container_id', $gp_id->group_container_id);
            foreach ($grupo as $val) {

                $options = json_decode($val->options);
                if (@$options->iscont == 'true') {

                    $valor = array('id' => $val->id, 'name' => $val->name);

                }
            }

            if (empty($valor)) {
                $valor = array('id' => $gp_id->id, 'name' => $gp_id->name);
            }

        } else {
            $valor = array('id' => $gp_id->id, 'name' => $gp_id->name);

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

        foreach ($fclMarkup as $freight) {
            // Freight
            $fclFreight = $freight->freight_markup->where('price_type_id', '=', 1);
            // Valor de porcentaje
            $freighPercentage = $this->skipPluck($fclFreight->pluck('percent_markup'));
            // markup currency
            $markupFreightCurre = $this->skipPluck($fclFreight->pluck('currency'));
            // markup con el monto segun la moneda
            $freighMarkup = $this->ratesCurrency($markupFreightCurre, $typeCurrency);
            // Objeto con las propiedades del currency
            $markupFreightCurre = Currency::find($markupFreightCurre);
            $markupFreightCurre = $markupFreightCurre->alphacode;
            // Monto original
            $freighAmmount = $this->skipPluck($fclFreight->pluck('fixed_markup'));
            // monto aplicado al currency
            $freighMarkup = $freighAmmount / $freighMarkup;
            $freighMarkup = number_format($freighMarkup, 2, '.', '');

            // Local y global
            $fclLocal = $freight->local_markup->where('price_type_id', '=', 1);
            // markup currency

            if ($request->mode == "1") {
                $markupLocalCurre = $this->skipPluck($fclLocal->pluck('currency_export'));
                // valor de la conversion segun la moneda
                $localMarkup = $this->ratesCurrency($markupLocalCurre, $typeCurrency);
                // Objeto con las propiedades del currency por monto fijo
                $markupLocalCurre = Currency::find($markupLocalCurre);
                $markupLocalCurre = $markupLocalCurre->alphacode;
                // En caso de ser Porcentaje
                $localPercentage = intval($this->skipPluck($fclLocal->pluck('percent_markup_export')));
                // Monto original
                $localAmmount = intval($this->skipPluck($fclLocal->pluck('fixed_markup_export')));
                // monto aplicado al currency
                $localMarkup = $localAmmount / $localMarkup;
                $localMarkup = number_format($localMarkup, 2, '.', '');
            } else {
                $markupLocalCurre = $this->skipPluck($fclLocal->pluck('currency_import'));
                // valor de la conversion segun la moneda
                $localMarkup = $this->ratesCurrency($markupLocalCurre, $typeCurrency);
                // Objeto con las propiedades del currency por monto fijo
                $markupLocalCurre = Currency::find($markupLocalCurre);
                $markupLocalCurre = $markupLocalCurre->alphacode;
                // en caso de ser porcentake
                $localPercentage = intval($this->skipPluck($fclLocal->pluck('percent_markup_import')));
                // monto original
                $localAmmount = intval($this->skipPluck($fclLocal->pluck('fixed_markup_import')));

                // monto aplicado al currency
                $localMarkup = $localAmmount / $localMarkup;
                $localMarkup = number_format($localMarkup, 2, '.', '');

            }

            //$collectionMarkup = new Collection();

            // Inlands
            $fclInland = $freight->inland_markup->where('price_type_id', '=', 1);

            if ($request->mode == "1") {
                $markupInlandCurre = $this->skipPluck($fclInland->pluck('currency_export'));
                // valor de la conversion segun la moneda
                $inlandMarkup = $this->ratesCurrency($markupInlandCurre, $typeCurrency);
                // Objeto con las propiedades del currency por monto fijo
                $markupInlandCurre = Currency::find($markupInlandCurre);
                $markupInlandCurre = $markupInlandCurre->alphacode;
                // en caso de ser porcentake
                $inlandPercentage = intval($this->skipPluck($fclInland->pluck('percent_markup_export')));
                // Monto original
                $inlandAmmount = intval($this->skipPluck($fclInland->pluck('fixed_markup_export')));
                // monto aplicado al currency
                $inlandMarkup = $inlandAmmount / $inlandMarkup;
                $inlandMarkup = number_format($inlandMarkup, 2, '.', '');
            } else {
                $markupInlandCurre = $this->skipPluck($fclInland->pluck('currency_import'));
                // valor de la conversion segun la moneda
                $inlandMarkup = $this->ratesCurrency($markupInlandCurre, $typeCurrency);
                // Objeto con las propiedades del currency por monto fijo
                $markupInlandCurre = Currency::find($markupInlandCurre);
                $markupInlandCurre = $markupInlandCurre->alphacode;
                // en caso de ser porcentake
                $inlandPercentage = intval($this->skipPluck($fclInland->pluck('percent_markup_import')));
                // monto original
                $inlandAmmount = intval($this->skipPluck($fclInland->pluck('fixed_markup_import')));
                // monto aplicado al currency
                $inlandMarkup = $inlandAmmount / $inlandMarkup;
                $inlandMarkup = number_format($inlandMarkup, 2, '.', '');
            }
        }
        $markup_array['freight'] = array('markupFreightCurre' => $markupFreightCurre, 'freighMarkup' => $freighMarkup, 'freighPercentage' => $freighPercentage, 'freighAmmount' => $freighAmmount);

        $markup_array['charges'] = array('markupLocalCurre' => $markupLocalCurre, 'localMarkup' => $localMarkup, 'localPercentage' => $localPercentage, 'localAmmount' => $localAmmount);

        $markup_array['inland'] = array('markupInlandCurre' => $markupInlandCurre, 'inlandMarkup' => $inlandMarkup, 'inlandPercentage' => $inlandPercentage, 'inlandAmmount' => $inlandAmmount);

        $collectionMarkup = new Collection($markup_array);

        return $collectionMarkup;
    }

    public function freightMarkups($freighPercentage, $freighAmmount, $freighMarkup, $monto, $typeCurrency, $type)
    {

        if ($freighPercentage != 0) {
            $freighPercentage = intval($freighPercentage);
            $markup = ($monto * $freighPercentage) / 100;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup;
            number_format($monto, 2, '.', '');
            $arraymarkup = array("markup" . $type => $markup, "markupConvert" . $type => $markup, "typemarkup" . $type => "$typeCurrency ($freighPercentage%)", "monto" . $type => $monto, 'montoMarkupO' => $markup);
        } else {

            $markup = trim($freighAmmount);
            $monto += $freighMarkup;
            $monto = number_format($monto, 2, '.', '');
            $arraymarkup = array("markup" . $type => $markup, "markupConvert" . $type => $freighMarkup, "typemarkup" . $type => $typeCurrency, "monto" . $type => $monto, 'montoMarkupO' => $markup);
        }

        return $arraymarkup;

    }

    public function inlandMarkup($inlandPercentage, $inlandAmmount, $inlandMarkup, $monto, $typeCurrency, $markupInlandCurre)
    {

        if ($inlandPercentage != 0) {
            $markup = ($monto * $inlandPercentage) / 100;
            $markup = number_format($markup, 2, '.', '');
            $monto += $markup;
            $monto = number_format($monto, 2, '.', '');
            $arraymarkupI = array("markup" => $markup, "markupConvert" => $markup, "typemarkup" => "$typeCurrency ($inlandPercentage%)", 'montoInlandT' => $monto, 'montoMarkupO' => $markup);
        } else {

            $markup = $inlandAmmount;
            $markup = number_format($markup, 2, '.', '');
            $monto += number_format($inlandMarkup, 2, '.', '');
            $arraymarkupI = array("markup" => $markup, "markupConvert" => $inlandMarkup, "typemarkup" => $markupInlandCurre, 'montoInlandT' => $monto, 'montoMarkupO' => $markup);

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

        $array = array("gpId" => $equipment[0], "count" => $equipment->count());

        return $array;

    }

    public function filtrarRate($arreglo, $equipmentForm, $gpId, $container)
    {
        
        $arreglo->where(function ($query) use ($container, $equipmentForm) {
            foreach ($container as $cont) {
                foreach ($equipmentForm as $val) {

                    $options = json_decode($cont->options);
                       

                    if ($val == $cont->id) {
         //               dd($options);

                        if ($options->field_rate != 'containers') {
                            $query->orwhere(@$options->field_rate, '!=', "0");
                        } else {                                                                     
                            $query->contain($cont->code);                            
                        }
                    }
                }
            }

        });

        return $arreglo;

    }

    public function divideCarriers($array){ 

        $carrier['carriers'] = array();
        $carrier['api'] = array();
        foreach($array as $element) {
            if(is_numeric($element)) {
                $carriers= array($element) ; 
               $carrier['carriers'] = array_merge($carrier['carriers'],$carriers);
            } else {
                $api[$element] = $element; 
                $carrier['api'] = array_merge($carrier['carriers'],$api);
            }
        }

        return $carrier;

    }


    public function contratoFuturo($date1,$date2){



        $date1 = new \DateTime($date1);
        $date2 = new \DateTime($date2);
        $diff = $date1->diff($date2);
        
        if($diff->invert  == "0")
            $contratoFuturo = true;
        else
            $contratoFuturo = false;


    return $contratoFuturo;

    }


    public function transitTime($date1,$date2){



        $date1 = new \DateTime($date1);
        $date2 = new \DateTime($date2);
        $diff = $date1->diff($date2);
        
        if($diff->invert  == "0")
            $contratoFuturo = true;
        else
            $contratoFuturo = false;


    return $contratoFuturo;

    }

}
