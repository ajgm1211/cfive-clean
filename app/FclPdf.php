<?php

namespace App;

use App\AutomaticRate;
use App\Container;
use App\Http\Traits\QuoteV2Trait;
use App\LocalChargeQuote;
use App\LocalChargeQuoteTotal;
use App\AutomaticRateTotal;
use App\AutomaticInlandTotal;
use App\QuoteV2;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class FclPdf
{
    use QuoteV2Trait;

    public function generate($quote)
    {
        $containers = Container::all();

        $equipmentHides = $this->hideContainerV2($quote->equipment, 'BD', $containers);

        $freight_charges = AutomaticRate::GetCharge(3)->GetQuote($quote->id)->with('charge')->get();

        $inlands = $quote->load('inland');

        $inlands = $this->processInland($inlands->inland, $containers);

        $origin_charges = $this->localCharges($quote, 1);

        $destination_charges = $this->localCharges($quote, 2);

        $freight_charges = $this->freightCharges($freight_charges, $quote, $containers);

        $freight_charges_detailed = $this->freightChargesDetailed($freight_charges, $quote, $containers);

        $quote_totals = $this->quoteTotals($quote,$containers);

        $view = \View::make('quote.pdf.index', ['quote' => $quote, 'inlands' => $inlands, 'user' => \Auth::user(), 'freight_charges' => $freight_charges, 'freight_charges_detailed' => $freight_charges_detailed, 'equipmentHides' => $equipmentHides, 'containers' => $containers, 'origin_charges' => $origin_charges, 'destination_charges' => $destination_charges, 'totals' => $quote_totals]);

        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view)->save('pdf/temp_' . $quote->id . '.pdf');

        return $pdf->stream('quote-' . $quote->id . '.pdf');
    }

    public function localCharges($quote, $type)
    {
        $localcharges = LocalChargeQuote::Quote($quote->id)->Type($type)->get();

        /*$localcharges = $localcharges->groupBy([

            function ($item) {
                return $item['port']['name'] . ', ' . $item['port']['code'];
            },

        ]);

        foreach ($localcharges as $value) {
            $value['total'] = $this->localChargeTotals($quote->id, $type, $value[0]['port_id']);
        }*/

        if (count($localcharges) > 0) {
            $localcharges = $localcharges->groupBy([

                function ($item) {
                    return $item['port']['name'] . ', ' . $item['port']['code'];
                },

            ]);

            foreach ($localcharges as $value) {
                $inlands = $this->InlandTotals($quote->id, $type, $value[0]['port_id']);

                foreach ($inlands as $inland) {
                    $value->push($inland);
                }
            }
        } else {

            $inlands = $this->InlandTotals($quote->id, $type, null);

            if (count($inlands) > 0) {

                $inlands = $inlands->groupBy([

                    function ($item) {
                        return $item['port']['name'] . ', ' . $item['port']['code'];
                    },

                ]);

                $localcharges = $inlands;
            }
        }

        return $localcharges;
    }

    public function InlandTotals($quote, $type, $port)
    {
        if ($type == 1) {
            $type = 'Origin';
        } else {
            $type = 'Destination';
        }

        $inlands = AutomaticInlandTotal::select('id', 'quote_id', 'port_id', 'totals as total', 'markups as profit', 'currency_id', 'inland_address_id')
            ->ConditionalPort($port)->Quotation($quote)->Type($type)->get();

        return $inlands;
    }

    public function localChargeTotals($quote, $type, $port)
    {
        $total = LocalChargeQuoteTotal::Quotation($quote)->Port($port)->Type($type)->first();

        return $total;
    }

    public function processInland($values, $containers)
    {

        $inlands = collect($values);

        $inlands = $inlands->groupBy([

            function ($item) {
                return $item['type'];
            },
            function ($item) {
                return $item['port']['name'] . ', ' . $item['port']['code'];
            },

        ], $preserveKeys = true);

        $sum = 'sum_';
        $total = 'total_';
        $amount = 'amount_';
        $markup = 'markup_';

        foreach ($inlands as $inland) {
            foreach ($inland as $values) {
                foreach ($values as $item) {

                    foreach ($containers as $c) {
                        ${$total . $amount . $c->code} = 0;
                        ${$total . $amount . $markup . $c->code} = 0;
                        ${$sum . $amount . $markup . $c->code} = 0;
                    }

                    $array_amounts = json_decode($item->rate, true);
                    $array_markups = json_decode($item->markup, true);

                    $array_amounts = $this->processOldContainers($array_amounts, 'amounts');
                    $array_markups = $this->processOldContainers($array_markups, 'markups');

                    foreach ($containers as $c) {
                        ${$sum . $c->code} = 0;
                        ${$sum . $amount . $markup . $c->code} = $sum . $amount . $markup . $c->code;
                        ${$total . $c->code} = 0;
                        ${$total . $sum . $c->code} = $total . $sum . $c->code;

                        if (isset($array_amounts['c' . $c->code]) && isset($array_markups['m' . $c->code])) {
                            ${$sum . $c->code} = $array_amounts['c' . $c->code] + $array_markups['m' . $c->code];
                            ${$total . $c->code} = ${$sum . $c->code};
                        } else if (isset($array_amounts['c' . $c->code]) && !isset($array_markups['m' . $c->code])) {
                            ${$sum . $c->code} = $array_amounts['c' . $c->code];
                            ${$total . $c->code} = ${$sum . $c->code};
                        } else if (!isset($array_amounts['c' . $c->code]) && isset($array_markups['m' . $c->code])) {
                            ${$sum . $c->code} = $array_markups['m' . $c->code];
                            ${$total . $c->code} = ${$sum . $c->code};
                        }

                        if (isset($array_amounts['c' . $c->code]) || isset($array_markups['m' . $c->code])) {
                            $item->${$total . $sum . $c->code} = isDecimal(${$total . $c->code}, true);
                        }
                    }
                }
            }
        }

        return $inlands;
    }

    public function freightCharges($freight_charges, $quote, $containers)
    {

        $freight_charges_grouped = collect($freight_charges);

        $sum = 'sum_';
        $total = 'total_';
        $amount = 'amount_';
        $markup = 'markup_';
        $charge_freight = 0;

        foreach ($freight_charges_grouped as $item) {

            foreach ($containers as $c) {
                ${$total . $amount . $c->code} = 0;
                ${$total . $amount . $markup . $c->code} = 0;
                ${$sum . $amount . $markup . $c->code} = 0;
            }

            foreach ($item->charge as $amounts) {
                if ($amounts->type_id == 3) {

                    $typeCurrency = $item->currency->alphacode;

                    $currency_rate = $this->ratesCurrency($amounts->currency_id, $typeCurrency);

                    $array_amounts = json_decode($amounts->amount, true);
                    $array_markups = json_decode($amounts->markups, true);

                    $array_amounts = $this->processOldContainers($array_amounts, 'amounts');
                    $array_markups = $this->processOldContainers($array_markups, 'markups');
                    
                    $currencyInput = $item->currency;

                    $currencyOutput = Currency::where('id',$quote->pdf_options['totalsCurrency']['id'])->first();
                    
                    foreach ($containers as $c) {
                        ${$sum . $c->code} = 0;
                        ${$sum . $amount . $markup . $c->code} = $sum . $amount . $markup . $c->code;
                        ${$total . $c->code} = 0;
                        ${$total . $sum . $c->code} = $total . $sum . $c->code;

                        if (isset($array_amounts['c' . $c->code]) && isset($array_markups['m' . $c->code])) {
                            ${$sum . $c->code} = $array_amounts['c' . $c->code] + $array_markups['m' . $c->code];
                            ${$total . $c->code} = ${$sum . $c->code} / $currency_rate;
                        } else if (isset($array_amounts['c' . $c->code]) && !isset($array_markups['m' . $c->code])) {
                            ${$sum . $c->code} = $array_amounts['c' . $c->code];
                            ${$total . $c->code} = ${$sum . $c->code} / $currency_rate;
                        } else if (!isset($array_amounts['c' . $c->code]) && isset($array_markups['m' . $c->code])) {
                            ${$sum . $c->code} = $array_markups['m' . $c->code];
                            ${$total . $c->code} = ${$sum . $c->code} / $currency_rate;
                        }

                        if (isset($array_amounts['c' . $c->code]) || isset($array_markups['m' . $c->code])) {
                            $charge_freight++;
                            $amounts->${$total . $sum . $c->code} = isDecimal(${$total . $c->code}, true);
                            $amounts->${$sum . $amount . $markup . $c->code} = isDecimal(${$sum . $c->code}, true);
                        }
                    }
                }
            }
        }
        
        return $freight_charges_grouped;
    }

    public function freightChargesDetailed($freight_charges, $quote, $containers)
    {

        $freight_charges_grouped = collect($freight_charges);

        $freight_charges_grouped = $freight_charges_grouped->groupBy([

            function ($item) {
                return $item['origin_port']['name'] . ', ' . $item['origin_port']['code'];
            },
            function ($item) {
                return $item['destination_port']['name'] . ', ' . $item['destination_port']['code'];
            },
            function ($item) {
                return $item['carrier']['name'];
            },

        ], $preserveKeys = true);

        $sum = 'sum_';
        $total = 'total_';
        $amount = 'amount_';
        $markup = 'markup_';
        $charge_freight = 0;

        foreach ($freight_charges_grouped as $freight) {
            foreach ($freight as $detail) {
                foreach ($detail as $item) {
                    foreach ($containers as $c) {
                        ${$total . $amount . $c->code} = 0;
                        ${$total . $amount . $markup . $c->code} = 0;
                        ${$sum . $amount . $markup . $c->code} = 0;
                    }

                    foreach ($item as $rate) {
                        foreach ($rate->charge as $amounts) {
                            if ($amounts->type_id == 3) {

                                /*if ($quote->pdf_option->grouped_freight_charges == 1) {
                                    $typeCurrency = $quote->pdf_option->freight_charges_currency;
                                } else {
                                    $typeCurrency = $rate->currency->alphacode;
                                }*/

                                $typeCurrency = $rate->currency->alphacode;

                                $currency_rate = $this->ratesCurrency($amounts->currency_id, $typeCurrency);

                                $array_amounts = json_decode($amounts->amount, true);
                                $array_markups = json_decode($amounts->markups, true);

                                $array_amounts = $this->processOldContainers($array_amounts, 'amounts');
                                $array_markups = $this->processOldContainers($array_markups, 'markups');

                                foreach ($containers as $c) {
                                    ${$sum . $c->code} = 0;
                                    ${$sum . $amount . $markup . $c->code} = $sum . $amount . $markup . $c->code;
                                    ${$total . $c->code} = 0;
                                    ${$total . $sum . $c->code} = $total . $sum . $c->code;

                                    if (isset($array_amounts['c' . $c->code]) && isset($array_markups['m' . $c->code])) {
                                        ${$sum . $c->code} = $array_amounts['c' . $c->code] + $array_markups['m' . $c->code];
                                        ${$total . $c->code} = ${$sum . $c->code} / $currency_rate;
                                    } else if (isset($array_amounts['c' . $c->code]) && !isset($array_markups['m' . $c->code])) {
                                        ${$sum . $c->code} = $array_amounts['c' . $c->code];
                                        ${$total . $c->code} = ${$sum . $c->code} / $currency_rate;
                                    } else if (!isset($array_amounts['c' . $c->code]) && isset($array_markups['m' . $c->code])) {
                                        ${$sum . $c->code} = $array_markups['m' . $c->code];
                                        ${$total . $c->code} = ${$sum . $c->code} / $currency_rate;
                                    }

                                    if (isset($array_amounts['c' . $c->code]) || isset($array_markups['m' . $c->code])) {
                                        $charge_freight++;
                                        $amounts->${$total . $sum . $c->code} = isDecimal(${$total . $c->code}, true);
                                        $amounts->${$sum . $amount . $markup . $c->code} = isDecimal(${$sum . $c->code}, true);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $freight->charge_freight = $charge_freight;
        }

        return $freight_charges_grouped;
    }


    /**
     * Mostrar/Ocultar contenedores en la vista
     * @param array $equipmentForm 
     * @param integer $tipo 
     * @return type
     */
    public function hideContainerV2($equipmentForm, $tipo, $container)
    {

        $equipment = new Collection();

        if ($tipo == 'BD') {
            $equipmentForm = json_decode($equipmentForm);
        }

        foreach ($container as $cont) {
            $hidden = 'hidden' . $cont->code;
            $$hidden = 'hidden';
            foreach ($equipmentForm as $val) {
                if ($val == '20') {
                    $val = 1;
                } elseif ($val == '40') {
                    $val = 2;
                } elseif ($val == '40HC') {
                    $val = 3;
                } elseif ($val == '45HC') {
                    $val = 4;
                } elseif ($val == '40NOR') {
                    $val = 5;
                }
                if ($val == $cont->id) {

                    $$hidden = '';
                }
            }
            $equipment->put($cont->code, $$hidden);
        }

        // Clases para reordenamiento de la tabla y ajuste
        $originClass = 'col-md-2';
        $destinyClass = 'col-md-1';
        $dataOrigDest = 'col-md-3';

        $countEquipment = count($equipmentForm);
        $countEquipment = 5 - $countEquipment;
        if ($countEquipment == 1) {
            $originClass = 'col-md-3';
            $destinyClass = 'col-md-1';
            $dataOrigDest = 'col-md-4';
        }
        if ($countEquipment == 2) {
            $originClass = 'col-md-3';
            $destinyClass = 'col-md-2';
            $dataOrigDest = 'col-md-5';
        }
        if ($countEquipment == 3) {
            $originClass = 'col-md-4';
            $destinyClass = 'col-md-2';
            $dataOrigDest = 'col-md-6';
        }
        if ($countEquipment == 4) {
            $originClass = 'col-md-5';
            $destinyClass = 'col-md-2';
            $dataOrigDest = 'col-md-7';
        }

        $equipment->put('originClass', $originClass);
        $equipment->put('destinyClass', $destinyClass);
        $equipment->put('dataOrigDest', $dataOrigDest);
        return ($equipment);
    }

    public function quoteTotals($quote,$containers)
    {
        
        $freightTotals = AutomaticRateTotal::GetQuote($quote->id)->get();
        
        $totalsArrayOutput = Array();

        $routePrefix = 'route_';
        $routeId = 1;
        foreach($freightTotals as $frTotal){
            $totalsArrayOutput[$routePrefix . $routeId]['POL'] = $frTotal->rate()->first()->origin_port()->first()->display_name ?? "--";
            $totalsArrayOutput[$routePrefix . $routeId]['POD'] = $frTotal->rate()->first()->destination_port()->first()->display_name ?? "--";
            $totalsArrayOutput[$routePrefix . $routeId]['carrier'] = $frTotal->rate()->first()->carrier()->first()->name ?? "--";
            $totalsArrayOutput[$routePrefix . $routeId]['currency'] = $quote->pdf_options['totalsCurrency']['alphacode'] ?? "--";
            $routeId++;
        }

        $inlandTotals = AutomaticInlandTotal::Quotation($quote->id)->get();

        $localChargeTotals = LocalChargeQuoteTotal::Quotation($quote->id)->get();

        $totals = $freightTotals->concat($inlandTotals)->concat($localChargeTotals);


        foreach ($totals as $total){

            if(is_a($total, 'App\AutomaticRateTotal')){
                $totalsArrayInput = json_decode($total->totals,true);
                $portArray['origin'] = $total->origin_port()->first()->display_name;
                $portArray['destination'] = $total->destination_port()->first()->display_name;
            }else if(is_a($total, 'App\AutomaticInlandTotal')){
                $totalsArrayInput = json_decode($total->totals,true);
                if($total->type == 'Origin'){
                    $portArray['origin'] = $total->get_port()->first()->display_name;
                    $portArray['destination'] = null;
                }else if($total->type == 'Destination'){
                    $portArray['origin'] = null;
                    $portArray['destination'] = $total->get_port()->first()->display_name;
                }
            }else if(is_a($total, 'App\LocalChargeQuoteTotal')){
                $totalsArrayInput = $total->total;
                if($total->get_type()->first()->description == 'origin'){
                    $portArray['origin'] = $total->get_port()->first()->display_name;
                    $portArray['destination'] = null;
                }else if($total->get_type()->first()->description == 'destiny'){
                    $portArray['origin'] = null;
                    $portArray['destination'] = $total->get_port()->first()->display_name;
                }
            }

            $totalsArrayInput = $this->processOldContainers($totalsArrayInput, 'amounts');
            
            $totalsCurrencyInput = Currency::where('id',$total->currency_id)->first();

            $totalsCurrencyOutput = Currency::where('id',$quote->pdf_options['totalsCurrency']['id'])->first();

            if($totalsArrayInput){
                $totalsArrayInput = $this->convertToCurrency($totalsCurrencyInput,$totalsCurrencyOutput,$totalsArrayInput);
            }

            foreach($totalsArrayOutput as $key=>$route){
                if($route['POL'] == $portArray['origin'] || $route['POD'] == $portArray['destination']){
                    foreach ($containers as $c) {
                        if (isset($totalsArrayInput['c' . $c->code])) {
                            $dmCalc = isDecimal($totalsArrayInput['c' . $c->code], true);
                            if (isset($totalsArrayOutput[$key]['c' . $c->code])) {
                                $totalsArrayOutput[$key]['c' . $c->code] += $dmCalc;
                            }else{
                                $totalsArrayOutput[$key]['c' . $c->code] = $dmCalc;
                            }
                        }
                    }
                }
            }
        }
        
        return $totalsArrayOutput;
    }
}