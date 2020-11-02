<?php

namespace App\Http\Traits;

use App\LocalChargeQuote;
use App\LocalChargeQuoteTotal;
use Illuminate\Support\Collection as Collection;

trait QuotationApiTrait
{
    public function localCharges($quote, $type)
    {
        $localcharges = LocalChargeQuote::select('id','price','profit','total','charge','currency_id','port_id','calculation_type_id')
        ->Quote($quote->id)->GetPort()->Type($type)->get();
        
        $localcharges = $this->mapLocalCharges($localcharges);
        
        $localcharges = $localcharges->groupBy([

            function ($item) {
                return $item['port']['display_name'];
            },

        ]);

        /*foreach ($localcharges as $value) {
            $value['total'] = $this->localChargeTotals($quote->id, $type, $value[0]['port_id']);
        }*/

        return $localcharges;
    }

    public function mapLocalCharges($collection){
        $collection = $collection->map(function ($value){
            $value['calculation_type_name'] = $value->calculation_type->name;
            $value['currency_code'] = $value->currency->alphacode;
            unset($value['port_id']);
            unset($value['calculation_type_id']);
            unset($value['calculation_type']);
            unset($value['currency']);
            unset($value['currency_id']);
            return $value;
        });

        return $collection;
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

                    if ($quote->pdf_option->grouped_freight_charges == 1) {
                        $typeCurrency = $quote->pdf_option->freight_charges_currency;
                    } else {
                        $typeCurrency = $item->currency->alphacode;
                    }

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

        $sum = 'sum_';
        $total = 'total_';
        $amount = 'amount_';
        $markup = 'markup_';
        $charge_freight = 0;

        foreach ($freight_charges as $item) {

            foreach ($containers as $c) {
                ${$total . $amount . $c->code} = 0;
                ${$total . $amount . $markup . $c->code} = 0;
                ${$sum . $amount . $markup . $c->code} = 0;
            }

            foreach ($item->charge as $amounts) {
                if ($amounts->type_id == 3) {

                    if ($quote->pdf_option->grouped_freight_charges == 1) {
                        $typeCurrency = $quote->pdf_option->freight_charges_currency;
                    } else {
                        $typeCurrency = $item->currency->alphacode;
                    }

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

        return $freight_charges;
    }
}
