<?php

namespace App;

use App\AutomaticRate;
use App\Container;
use App\Http\Traits\UtilTrait;
use App\Http\Traits\QuoteV2Trait;
use App\LocalChargeQuote;
use App\LocalChargeQuoteTotal;
use App\QuoteV2;
use EventIntercom;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Delegation;
use App\UserDelegation;

class LclPdf
{

    use QuoteV2Trait;

    public function generate($quote)
    {

        $freight_charges = AutomaticRate::GetChargeLcl(3)->GetQuote($quote->id)->with('charge_lcl_air')->get();

        $service = $freight_charges->contains('transit_time', '!=', '') ? true : false;

        $freight_charges_detailed = $this->freightChargesDetailed($freight_charges);

        $freight_charges = $this->freightCharges($freight_charges);

        $origin_charges = $this->localCharges($quote, 1);
        
        $destination_charges = $this->localCharges($quote, 2);
        
        $quote_totals = $this->quoteTotals($quote);
        
        $delegation= $this->delegation($quote);

        $view = \View::make('quote.pdf.index_lcl', ['quote' => $quote,'delegation'=>$delegation, 'user' => \Auth::user(), 'freight_charges' => $freight_charges, 'freight_charges_detailed' => $freight_charges_detailed, 'service' => $service, 'origin_charges' => $origin_charges, 'destination_charges' => $destination_charges, 'totals' => $quote_totals]);

        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view)->save('pdf/temp_' . $quote->id . '.pdf');

        // EVENTO INTERCOM
        $event = new EventIntercom();
        $event->event_pdfLcl();

        return $pdf->stream('quote-' . $quote->id . '.pdf');
    }
    public function Delegation($quote){
        
        $id_ud=UserDelegation::where('users_id','=',$quote->user_id)->first();
        if($id_ud==null)
            $delegation= '';
        else{
            $delegation= Delegation::where('id', '=', $id_ud->delegations_id)->first();
        }

        return $delegation;
    }

    public function localCharges($quote, $type)
    {
        $localcharges = LocalChargeQuoteLcl::Quote($quote->id)->Type($type)->get();

        if (count($localcharges) > 0) {
            $localcharges = $localcharges->groupBy([

                function ($item) {
                    return $item['port']['name'] . ', ' . $item['port']['code'];
                },

            ]);

            foreach ($localcharges as $value) {
                $inlands = $this->InlandTotals($quote->id, $type, $value[0]['port_id']);
                
                foreach ($inlands as $inland) {
                    if($inland->pdf_options['grouped']){
                        foreach($value as $charge){
                            if($inland->pdf_options['groupId'] == $charge->id){
                                $inland_total = json_decode($inland->totals, true);
                                $inland_total = isset($inland->sum_total) ? $inland->sum_total:$inland_total;
                                $inland_total = $this->convertToCurrency($inland->currency, $charge->currency, $inland_total);
                                $grouped_total = intval($charge->total) + intval($inland_total['lcl_totals']);
                                $charge->total = $grouped_total;
                            }
                        }
                    }else{
                        $value->push($inland);
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
            
            //Combining collections
            $localcharges = $localcharges->union($inlands);
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

        $inlands = AutomaticInlandLclAir::select('id', 'charge', 'inland_totals_id', 'quote_id', 'total', 'price_per_unit', 'units', 'port_id', 'markup as profit', 'currency_id')
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
 
        $inlands = AutomaticInlandLclAir::select('id', 'charge', 'inland_totals_id', 'quote_id', 'port_id', 'total', 'price_per_unit', 'units', 'markup as profit', 'currency_id')
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

    public function processInlandTotal($data){
        if($data->total != null){
            $total = $data->total + $data->profit;
            return $total;
        }
        $total = ($data->price_per_unit*$data->unit)+$data->profit;
        return $total;
    }

    public function localChargeTotals($quote, $type, $port)
    {
        $total = LocalChargeQuoteLclTotal::Quotation($quote)->Port($port)->Type($type)->first();

        return $total;
    }

    public function freightChargesDetailed($freight_charges)
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

        foreach ($freight_charges_grouped as $freight) {
            foreach ($freight as $detail) {
                foreach ($detail as $item) {
                    foreach ($item as $rate) {
                        foreach ($rate->charge_lcl_air as $v_freight) {
                            if ($v_freight->type_id == 3) {

                                $typeCurrency = @$rate->currency->alphacode;
                                $currency_rate = $this->ratesCurrency($v_freight->currency_id, $typeCurrency);

                                if ($v_freight->units > 0) {
                                    $v_freight->rate = number_format(($v_freight->units * $v_freight->price_per_unit), 2, '.', '');
                                } else {
                                    $v_freight->rate = 0;
                                }
                                $v_freight->total_freight = number_format((($v_freight->units * $v_freight->price_per_unit)) / $currency_rate, 2, '.', '');
                            }
                        }
                    }
                }
            }
        }

        return $freight_charges_grouped;
    }

    public function freightCharges($freight_charges)
    {
        $freight_charges_grouped = collect($freight_charges);

        foreach ($freight_charges_grouped as $rate) {

            foreach ($rate->charge_lcl_air as $v_freight) {
                if ($v_freight->type_id == 3) {

                    $typeCurrency = @$rate->currency->alphacode;
                    $currency_rate = $this->ratesCurrency($v_freight->currency_id, $typeCurrency);

                    if ($v_freight->units > 0) {
                        $v_freight->rate = number_format(($v_freight->units * $v_freight->price_per_unit), 2, '.', '');
                    } else {
                        $v_freight->rate = 0;
                    }
                    $v_freight->total_freight = number_format((($v_freight->units * $v_freight->price_per_unit)) / $currency_rate, 2, '.', '');
                }
            }
        }

        return $freight_charges_grouped;
    }

    public function quoteTotals($quote)
    {
        
        $freightTotals = $quote->automatic_rate_totals()->get();
        
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

        $inlandTotals = $quote->automatic_inland_totals()->get();

        $localChargeTotals = LocalChargeQuoteLclTotal::Quotation($quote->id)->get();

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
            }else if(is_a($total, 'App\LocalChargeQuoteLclTotal')){
                $totalsArrayInput = Array('total'=>$total->total);
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
                        
            if($totalsArrayInput){
                $totalsArrayInput = $this->convertToCurrencyPDF($totalsCurrencyInput,$totalsArrayInput,$quote);
            }

            foreach($totalsArrayOutput as $key=>$route){
                if($route['POL'] == $portArray['origin'] || $route['POD'] == $portArray['destination']){
                    if (isset($totalsArrayInput['total'])) {
                        $dmCalc = isDecimal($totalsArrayInput['total'], true);
                        if (isset($totalsArrayOutput[$key]['total'])) {
                            $totalsArrayOutput[$key]['total'] += $dmCalc;
                        }else{
                            $totalsArrayOutput[$key]['total'] = $dmCalc;
                        }
                    }else if (isset($totalsArrayInput['lcl_totals'])) {
                        $dmCalc = isDecimal($totalsArrayInput['lcl_totals'], true);
                        if (isset($totalsArrayOutput[$key]['total'])) {
                            $totalsArrayOutput[$key]['total'] += $dmCalc;
                        }else{
                            $totalsArrayOutput[$key]['total'] = $dmCalc;
                        }
                    }
                }
            }
        }

        return $totalsArrayOutput;
    }
}
