<?php

namespace App;

use App\AutomaticRate;
use App\Container;
use App\Http\Traits\UtilTrait;
use App\LocalChargeQuote;
use App\LocalChargeQuoteTotal;
use App\QuoteV2;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LclPdf
{

    use UtilTrait;

    public function generate($quote)
    {

        $freight_charges = AutomaticRate::GetChargeLcl(3)->GetQuote($quote->id)->with('charge_lcl_air')->get();

        $service = $freight_charges->contains('transit_time', '!=', '') ? true : false;

        $freight_charges_detailed = $this->freightChargesDetailed($freight_charges);

        $freight_charges = $this->freightCharges($freight_charges);

        $origin_charges = $this->localCharges($quote, 1);

        $destination_charges = $this->localCharges($quote, 2);

        $view = \View::make('quote.pdf.index_lcl', ['quote' => $quote, 'user' => \Auth::user(), 'freight_charges' => $freight_charges, 'freight_charges_detailed' => $freight_charges_detailed, 'service' => $service, 'origin_charges' => $origin_charges, 'destination_charges' => $destination_charges]);

        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($view)->save('pdf/temp_' . $quote->id . '.pdf');

        return $pdf->stream('quote-' . $quote->id . '.pdf');
    }

    public function localCharges($quote, $type)
    {
        $localcharges = LocalChargeQuoteLcl::Quote($quote->id)->Type($type)->get();

        $localcharges = $localcharges->groupBy([

            function ($item) {
                return $item['port']['name'] . ', ' . $item['port']['code'];
            },

        ]);

        foreach ($localcharges as $value) {
            $value['total'] = $this->localChargeTotals($quote->id, $type, $value[0]['port_id']);
        }

        /*if (count($localcharges) > 0) {
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
        }*/

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
}
