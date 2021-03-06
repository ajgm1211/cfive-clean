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
use EventIntercom;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Delegation;
use App\UserDelegation;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Storage;

class FclPdf
{
    use QuoteV2Trait;

    private $upload;

    public function __construct($upload = false)
    {
        $this->upload = $upload;
    }

    public function generate($quote)
    {
        $user = User::find($quote->user_id);

        $containers = Container::all();

        $equipmentHides = $this->hideContainerV2($quote->equipment, 'BD', $containers);

        $freight_charges = AutomaticRate::GetCharge(3)->GetQuote($quote->id)->with('charge')->get();

        $inlands = $quote->load('inland');

        $inlands = $this->processInland($inlands->inland, $containers);

        $origin_charges = $this->localCharges($quote, 1);

        $destination_charges = $this->localCharges($quote, 2);

        $freight_charges = $this->freightCharges($freight_charges, $quote, $containers);

        $freight_charges_detailed = $this->freightChargesDetailed($freight_charges, $quote, $containers);

        $quote_totals = $this->quoteTotals($quote, $containers);
 
        $delegation = $this->getDelegation($quote);


        $data = ['quote' => $quote, 'delegation' => $delegation, 'inlands' => $inlands, 'user' => $user, 'freight_charges' => $freight_charges, 'freight_charges_detailed' => $freight_charges_detailed, 'equipmentHides' => $equipmentHides, 'containers' => $containers, 'origin_charges' => $origin_charges, 'destination_charges' => $destination_charges, 'totals' => $quote_totals];

        //$view = \View::make('quote.pdf.index', ['quote' => $quote, 'delegation' => $delegation, 'inlands' => $inlands, 'user' => \Auth::user(), 'freight_charges' => $freight_charges, 'freight_charges_detailed' => $freight_charges_detailed, 'equipmentHides' => $equipmentHides, 'containers' => $containers, 'origin_charges' => $origin_charges, 'destination_charges' => $destination_charges, 'totals' => $quote_totals]);

        //$pdf = \App::make('dompdf.wrapper');

        
        $pdf = PDF::loadView('quote.pdf.index', $data);
        
        if ($this->upload) {
            Storage::disk('pdf')->put('quote_'.$quote->id.'.pdf', $pdf->output());
            $quote->addMedia(storage_path().'/app/public/pdf/quote_'.$quote->id.'.pdf')->toMediaCollection('document', 'pdfApiS3');
            return;
        }

        //Intercom
        $event = new EventIntercom();

        //Mixpanel
        $event->event_pdfFcl();

        return $pdf->stream('quote-' . $quote->id . '.pdf');
    }

    public function getDelegation($quote){

        //Option for disable delegation
        $company_user = CompanyUser::find($quote->company_user_id);
        $company_user_options =  $company_user['options'];
        $disabled_delegation = $company_user_options['disable_delegation_pdf'];

        if($disabled_delegation == true){
            $delegation = true ;

        }else{
            $delegation = $this->delegation($quote);
        }        
        return $delegation;
    }

    public function Delegation($quote)
    {
        $id_ud = UserDelegation::where('users_id', '=', $quote->user_id)->first();
        if ($id_ud == null)
            $delegation = '';
        else {
            $delegation = Delegation::where('id', '=', $id_ud->delegations_id)->first();
        }
        

        return $delegation;
    }
    public function localCharges($quote, $type)
    {
        $localcharges = LocalChargeQuote::Quote($quote->id)->Type($type)->get();

        //Checking if there are localcharges
        if (count($localcharges) > 0) {
            $localcharges = $localcharges->groupBy([

                function ($item) {
                    return $item['port']['name'] . ', ' . $item['port']['code'];
                },

            ]);

            //Relating inlands to localcharges
            foreach ($localcharges as $value) {
                $inlands = $this->InlandTotals($quote->id, $type, $value[0]['port_id']);
                foreach ($value as $charge) {
                    foreach ($inlands as $inland) {

                        if (isset($inland->inland_totals->pdf_options) && $inland->inland_totals->pdf_options['grouped']) {

                            if (isset($inland->inland_totals->pdf_options) && $inland->inland_totals->pdf_options['groupId'] == $charge->id) {
                                $grouping_array = [];
                                $inland_total = json_decode(json_decode($inland->total));
                                $inland_total = isset($inland->sum_total) ? $inland->sum_total:(array)$inland_total;
                                $inland_total = $this->convertToCurrency($inland->currency, $charge->currency, $inland_total);
                                foreach ($charge->total as $container => $value) {
                                    $grouping_array[$container] = intval($value) + intval($inland_total[$container]);
                                }
                                $charge->total = $grouping_array;
                            }
                        } else {
                            if (!$value->contains($inland)) {
                                $value->push($inland);
                            }
                        }
                    }
                }
            }

            //Setting up localcharges' ports ids
            $ports = array();

            foreach ($localcharges as $value) {
                foreach ($value as $charge) {
                    $ports[] = $charge->port_id;
                }
            }

            //Checking if exists inlands not associated to localcharges and adding to collection
            $inlands = $this->InlandExcludingPorts($quote->id, $type, $ports);

            $localcharges = $localcharges->union($inlands);
        } else {

            //Setting up inlands
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

        $inlands = AutomaticInland::select('id', 'quote_id', 'port_id', 'rate as total', 'markup as profit', 'charge', 'currency_id', 'inland_totals_id')
            ->ConditionalPort($port)->Quotation($quote)->Type($type)->get();

        //Adding address and modifying totals in Inlands
        $this->addAddressTotalToInland($inlands);
        
        return $inlands;
    }

    public function InlandExcludingPorts($quote, $type, $port)
    {
        if ($type == 1) {
            $type = 'Origin';
        } else {
            $type = 'Destination';
        }

        $inlands = AutomaticInland::select('id', 'quote_id', 'port_id', 'rate as total', 'markup as profit', 'charge', 'currency_id', 'inland_totals_id')
            ->whereNotIn('port_id', $port)->Quotation($quote)->Type($type)->get();

        //Adding address and modifying totals in Inlands
        $this->addAddressTotalToInland($inlands);

        $inlands = $inlands->groupBy([

            function ($item) {
                return $item['port']['name'] . ', ' . $item['port']['code'];
            },

        ]);

        return $inlands;
    }

    public function addAddressTotalToInland($inlands){
        foreach ($inlands as $inland) {
            $total = $this->processInlandTotal($inland);
            $address = $inland->getInlandAddress();
            $inland->address = $address;
            $inland->sum_total = $total;
        }
    }

    public function localChargeTotals($quote, $type, $port)
    {
        $total = LocalChargeQuoteTotal::Quotation($quote)->Port($port)->Type($type)->first();

        return $total;
    }


    public function processInlandTotal($item)
    {

        $sum = 'sum_';
        $total = 'total_';
        $amount = 'amount_';
        $markup = 'markup_';

        $containers = Container::all();

        foreach ($containers as $c) {
            ${$total . $amount . $c->code} = 0;
            ${$total . $amount . $markup . $c->code} = 0;
            ${$sum . $amount . $markup . $c->code} = 0;
        }

        $array_amounts = (array)json_decode(json_decode($item->total));
        $array_markups = (array)$item->profit;

        //$array_amounts = $this->processOldContainers($array_amounts, 'amounts');
        //$array_markups = $this->processOldContainers($array_markups, 'markups');
        
        $array = array();

        foreach ($containers as $c) {
            ${$sum . $c->code} = 0;
            ${$sum . $amount . $markup . $c->code} = $sum . $amount . $markup . $c->code;
            ${$total . $c->code} = 0;
            ${$total . $sum . $c->code} = $total . $sum . $c->code;
            ${'c' . $c->code} = 'c' . $c->code;

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

            $equipmentHides = $this->hideContainerV2($item->quote->equipment, 'BD', $containers);

            foreach($equipmentHides as $key => $hidden){
                if($c->code == $key && $hidden != "hidden"){
                    $array[${'c'. $c->code}]=$item->${$total . $sum . $c->code};
                }
            }
        }
        
        return $array;
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

            //Setting addresses in freight charges
            $origin_address = InlandAddress::where(['port_id' => $item->origin_port_id, 'quote_id' => $item->quote_id])
                ->select('address')->first();
            $destination_address = InlandAddress::where(['port_id' => $item->destination_port_id, 'quote_id' => $item->quote_id])
                ->select('address')->first();

            if ($origin_address) {
                $freight_charges_grouped->hasOriginAddress = 1;
                $item->origin_address = $origin_address->address;
            }

            if ($destination_address) {
                $freight_charges_grouped->hasDestinationAddress = 1;
                $item->destination_address = $destination_address->address;
            }

            foreach ($item->charge as $amounts) {
                if ($amounts->type_id == 3) {

                    $typeCurrency = $item->currency->alphacode;

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

    public function quoteTotals($quote, $containers)
    {

        $freightTotals = AutomaticRateTotal::GetQuote($quote->id)->get();

        $totalsArrayOutput = array();

        $routePrefix = 'route_';
        $routeId = 1;
        foreach ($freightTotals as $frTotal) {
            $totalsArrayOutput[$routePrefix . $routeId]['POL'] = $frTotal->rate()->first() ? $frTotal->rate()->first()->origin_port()->first()->display_name : "--";
            $totalsArrayOutput[$routePrefix . $routeId]['POD'] = $frTotal->rate()->first() ? $frTotal->rate()->first()->destination_port()->first()->display_name : "--";
            $totalsArrayOutput[$routePrefix . $routeId]['carrier'] = $frTotal->carrier()->first()->name ?? "--";
            $totalsArrayOutput[$routePrefix . $routeId]['currency'] = $quote->pdf_options['totalsCurrency']['alphacode'] ?? "--";
            $totalsArrayOutput[$routePrefix . $routeId]['freightAdded'] = false;
            $routeId++;
        }

        $inlandTotals = AutomaticInlandTotal::Quotation($quote->id)->get();

        $localChargeTotals = LocalChargeQuoteTotal::Quotation($quote->id)->get();

        $totals = $freightTotals->concat($inlandTotals)->concat($localChargeTotals);

        foreach ($totals as $total) {
            $isFreight = false;

            if (is_a($total, 'App\AutomaticRateTotal')) {
                $totalsArrayInput = json_decode($total->totals, true);
                $portArray['origin'] = $total->origin_port() ? $total->origin_port()->first()->display_name : "--";
                $portArray['destination'] = $total->destination_port() ? $total->destination_port()->first()->display_name : "--";
                $portArray['carrier'] = $total->carrier()->first()->name;
                $isFreight = true;
            } else if (is_a($total, 'App\AutomaticInlandTotal')) {
                $totalsArrayInput = json_decode($total->totals, true);
                $portArray['carrier'] = 'local';
                if ($total->type == 'Origin') {
                    $portArray['origin'] = $total->get_port()->first()->display_name;
                    $portArray['destination'] = null;
                } else if ($total->type == 'Destination') {
                    $portArray['origin'] = null;
                    $portArray['destination'] = $total->get_port()->first()->display_name;
                }
            } else if (is_a($total, 'App\LocalChargeQuoteTotal')) {
                $totalsArrayInput = $total->total;
                $portArray['carrier'] = 'local';
                if ($total->get_type()->first()->description == 'origin') {
                    $portArray['origin'] = $total->get_port()->first()->display_name;
                    $portArray['destination'] = null;
                } else if ($total->get_type()->first()->description == 'destiny') {
                    $portArray['origin'] = null;
                    $portArray['destination'] = $total->get_port()->first()->display_name;
                }
            }

            $totalsArrayInput = $this->processOldContainers($totalsArrayInput, 'amounts');

            $totalsCurrencyInput = Currency::where('id', $total->currency_id)->first();

            if ($totalsArrayInput) {
                $totalsArrayInput = $this->convertToCurrencyPDF($totalsCurrencyInput, $totalsArrayInput, $quote);
            }

            foreach ($totalsArrayOutput as $key => $route) {

                if($isFreight && $route['freightAdded']){
                    continue;
                }
                
                if (($route['POL'] == $portArray['origin'] && $route['POD'] == $portArray['destination'] && $portArray['carrier'] == $route['carrier']) ||
                    ($portArray['carrier'] == 'local' && ($route['POL'] == $portArray['origin'] || $route['POD'] == $portArray['destination']))
                ) {
                    
                    foreach ($containers as $c) {
                        if (isset($totalsArrayInput['c' . $c->code])) {
                            $dmCalc = isDecimal($totalsArrayInput['c' . $c->code], true);
                            if (isset($totalsArrayOutput[$key]['c' . $c->code])) {
                                $totalsArrayOutput[$key]['c' . $c->code] += $dmCalc;
                            } else {
                                $totalsArrayOutput[$key]['c' . $c->code] = $dmCalc;
                            }
                        }
                    }
                    
                    if($isFreight) {
                        $totalsArrayOutput[$key]['freightAdded'] = true;
                        break;
                    }
                }

            }
        }
        return $totalsArrayOutput;
    }
}
