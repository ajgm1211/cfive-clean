<?php

namespace App\Http\Traits;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\QuoteV2;
use App\Currency;
use App\AutomaticRate;
use App\AutomaticInland;
use App\AutomaticInlandLclAir;
use App\Charge;
use App\ChargeLclAir;
use App\Jobs\SendQuotes;
use App\SendQuote;
use App\Container;
use App\SaleTermV2;
use App\Harbor;
use App\User;
use Illuminate\Support\Collection as Collection;

trait QuoteV2Trait
{
    public function generatepdf($id, $company_user, $currency_cfg, $user_id)
    {
        $quote = QuoteV2::findOrFail($id);
        $rates = AutomaticRate::where('quote_id', $quote->id)->with('charge')->get();
        $containers = Container::all();

        /* Sale terms */

        $sale_terms_origin = SaleTermV2::where('quote_id', $quote->id)->where('type', 'Origin')->with('charge')->get();
        $sale_terms_destination = SaleTermV2::where('quote_id', $quote->id)->where('type', 'Destination')->with('charge')->get();
        $sale_terms_origin_grouped = SaleTermV2::where('quote_id', $quote->id)->where('type', 'Origin')->with('charge')->get();
        $sale_terms_destination_grouped = SaleTermV2::where('quote_id', $quote->id)->where('type', 'Destination')->with('charge')->get();

        $sum = 'sum_';
        $total = 'total_';

        foreach ($containers as $container) {
            ${$sum . $container} = $sum . $container->code;
            ${$total . $container->code} = $total . $container->code;
        }

        foreach ($sale_terms_origin_grouped as $origin_sale) {
            foreach ($origin_sale->charge as $origin_charge) {
                $sale_rates = json_decode($origin_charge->rate, true);
                if ($origin_charge->currency_id != '') {
                    if ($quote->pdf_option->grouped_total_currency == 1) {
                        $typeCurrency =  $quote->pdf_option->total_in_currency;
                    } else {
                        $typeCurrency =  $company_user->currency->alphacode;
                    }
                    $currency_rate = $this->ratesCurrency($origin_charge->currency_id, $typeCurrency);
                    foreach ($containers as $container) {
                        $origin_charge->${$sum . $container} += @$sale_rates['c' . $container->code] / $currency_rate;
                    }
                }
            }
        }

        foreach ($sale_terms_destination_grouped as $destination_sale) {
            foreach ($destination_sale->charge as $destination_charge) {
                $sale_rates = json_decode($destination_charge->rate, true);
                if ($destination_charge->currency_id != '') {
                    if ($quote->pdf_option->grouped_total_currency == 1) {
                        $typeCurrency =  $quote->pdf_option->total_in_currency;
                    } else {
                        $typeCurrency =  $company_user->currency->alphacode;
                    }
                    $currency_rate = $this->ratesCurrency($destination_charge->currency_id, $typeCurrency);
                    foreach ($containers as $container) {
                        $destination_charge->${$sum . $container} += @$sale_rates['c' . $container->code] / $currency_rate;
                    }
                }
            }
        }

        $sale_terms_origin = collect($sale_terms_origin);

        $sale_terms_origin = $sale_terms_origin->groupBy([
            function ($item) {
                return $item['port']['name'] . ', ' . $item['port']['code'];
            },
        ], $preserveKeys = true);

        foreach ($sale_terms_origin as $value) {
            foreach ($value as $origin_sale) {
                foreach ($origin_sale->charge as $origin_charge) {
                    $sale_rates = json_decode($origin_charge->rate, true);
                    if ($origin_charge->currency_id != '') {
                        if ($quote->pdf_option->grouped_origin_charges == 1) {
                            $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                        } else {
                            $typeCurrency =  $currency_cfg->alphacode;
                        }
                        $currency_rate = $this->ratesCurrency($origin_charge->currency_id, $typeCurrency);
                        foreach ($containers as $container) {
                            $origin_charge->${$sum . $container} += @$sale_rates['c' . $container->code] / $currency_rate;
                        }
                    }
                }
            }
        }

        $sale_terms_destination = collect($sale_terms_destination);

        $sale_terms_destination = $sale_terms_destination->groupBy([
            function ($item) {
                return $item['port']['name'] . ', ' . $item['port']['code'];
            },
        ], $preserveKeys = true);

        foreach ($sale_terms_destination as $destination_sale) {
            foreach ($destination_sale as $value) {
                foreach ($value->charge as $item) {
                    $sale_rates = json_decode($item->rate, true);
                    if ($item->currency_id != '') {
                        if ($quote->pdf_option->grouped_destination_charges == 1) {
                            $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                        } else {
                            $typeCurrency =  $currency_cfg->alphacode;
                        }
                        $currency_rate = $this->ratesCurrency($item->currency_id, $typeCurrency);
                        foreach ($containers as $container) {
                            $item->${$sum . $container} += @$sale_rates['c' . $container->code] / $currency_rate;
                        }
                    }
                }
            }
        }

        /* Fin Saleterms */

        /* Arrays de puertos incluidos en los Saleterms */

        $origin_ports = $this->getPortsInArray($sale_terms_origin_grouped);

        $destination_ports = $this->getPortsInArray($sale_terms_destination_grouped);

        /* Fin arrays */

        /* Consulta de charges relacionados al Rate */

        $origin_charges = AutomaticRate::whereNotIn('origin_port_id', $origin_ports)->where('quote_id', $quote->id)
            ->with(['charge' => function ($q) {
                $q->where('type_id', 1);
            }])->get();

        $destination_charges = AutomaticRate::whereNotIn('destination_port_id', $destination_ports)->where('quote_id', $quote->id)
            ->with(['charge' => function ($q) {
                $q->where('type_id', 2);
            }])->get();

        $freight_charges = AutomaticRate::whereHas('charge', function ($query) {
            $query->where('type_id', 3);
        })->with('charge')->where('quote_id', $quote->id)->get();

        /* Fin consulta de charges */

        $origin_harbor = Harbor::where('id', $quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id', $quote->destination_harbor_id)->first();
        $user = User::where('id', \Auth::id())->with('companyUser')->first();
        $equipmentHides = $this->hideContainerV2($quote->equipment, 'BD', $containers);

        /** Rates **/

        $rates = $this->processGlobalRates($rates, $quote, $company_user->currency->alphacode, $containers);

        /* Se manipula la colección de rates para añadir los valores de saleterms */
        $rates = $rates->map(function ($item, $key) use ($total, $sum, $containers, $origin_ports, $destination_ports, $sale_terms_origin_grouped, $sale_terms_destination_grouped) {
            if (in_array($item->origin_port_id, $origin_ports)) {
                if (!$item->charge->whereIn('type_id', 1)->isEmpty()) {
                    $item->charge->map(function ($value, $key) use ($total, $sale_terms_origin_grouped, $item, $containers) {
                        if ($value->type_id == 1) {
                            //Seteamos valores de los charges originales a 0
                            foreach ($containers as $container) {
                                ${$total . $container->code} = 'total_' . $container->code;
                                $value->${$total . $container->code} = 0;
                                $value->${$total . $container->code} = 0;
                            }
                        }
                    });
                    //Añadimos los saleterms a la colección de Rates
                    $sale_terms_origin_grouped->map(function ($a) use ($item, $total, $containers, $sum) {
                        $a->charge->map(function ($x) use ($item, $total, $containers, $sum) {
                            $charge = new Charge();
                            $charge->type_id = 1;
                            foreach ($containers as $container) {
                                ${$total . $container->code} = 'total_' . $container->code;
                                ${$sum . $container->code} = 'sum_' . $container->code;
                                $charge->${$total . $container->code} = $x->${$sum . $container->code};
                            }
                            $charge->currency_id = $x->currency_id;
                            $item->charge->push($charge);
                        });
                    });
                } else {
                    //Añadimos los saleterms a la colección de Rates si esta vacío la relación con Charges
                    $sale_terms_origin_grouped->map(function ($a) use ($item, $total, $containers, $sum) {
                        $a->charge->map(function ($x) use ($item, $total, $containers, $sum) {
                            $charge = new Charge();
                            $charge->type_id = 1;
                            foreach ($containers as $container) {
                                ${$total . $container->code} = 'total_' . $container->code;
                                ${$sum . $container->code} = 'sum_' . $container->code;
                                $charge->${$total . $container->code} = $x->${$sum . $container->code};
                            }
                            $charge->currency_id = $x->currency_id;
                            $item->charge->push($charge);
                        });
                    });
                }
            }
            if (in_array($item->destination_port_id, $destination_ports)) {
                if (!$item->charge->whereIn('type_id', 2)->isEmpty()) {
                    $item->charge->map(function ($value, $key) use ($sale_terms_destination_grouped, $item, $containers, $total) {
                        if ($value->type_id == 2) {
                            //Seteamos valores de los charges originales a 0
                            foreach ($containers as $container) {
                                ${$total . $container->code} = 'total_' . $container->code;
                                $value->${$total . $container->code} = 0;
                                $value->${$total . $container->code} = 0;
                            }
                        }
                    });
                    //Añadimos los saleterms a la colección de Rates
                    $sale_terms_destination_grouped->map(function ($a) use ($item, $containers, $total, $sum) {
                        $a->charge->map(function ($x) use ($item, $containers, $total, $sum) {
                            $charge = new Charge();
                            $charge->type_id = 2;
                            foreach ($containers as $container) {
                                ${$total . $container->code} = 'total_' . $container->code;
                                ${$sum . $container->code} = 'sum_' . $container->code;
                                $charge->${$total . $container->code} = $x->${$sum . $container->code};
                            }
                            $charge->currency_id = $x->currency_id;
                            $item->charge->push($charge);
                        });
                    });
                } else {
                    //Añadimos los saleterms a la colección de Rates si esta vacío la relación con Charges
                    $sale_terms_destination_grouped->map(function ($a) use ($item, $containers, $total, $sum) {
                        $a->charge->map(function ($x) use ($item, $containers, $total, $sum) {
                            $charge = new Charge();
                            $charge->type_id = 2;
                            foreach ($containers as $container) {
                                ${$total . $container->code} = 'total_' . $container->code;
                                ${$sum . $container->code} = 'sum_' . $container->code;
                                $charge->${$total . $container->code} = $x->${$sum . $container->code};
                            }
                            $charge->currency_id = $x->currency_id;
                            $item->charge->push($charge);
                        });
                    });
                }
            }

            return $item;
        });

        /** Origin Charges **/

        $origin_charges_grouped = $this->localChargesGrouped($origin_charges, 'origin', $quote, $company_user->currency->alphacode, $containers);

        $origin_charges_detailed = $this->localChargesDetailed($origin_charges, 'origin', $quote, $company_user->currency->alphacode, $containers);

        /** Destination Charges **/

        $destination_charges_grouped = $this->localChargesGrouped($destination_charges, 'destination', $quote, $company_user->currency->alphacode, $containers);

        $destination_charges_detailed = $this->localChargesDetailed($destination_charges, 'destination', $quote, $company_user->currency->alphacode, $containers);

        /** Freight Charges **/

        $freight_charges_grouped = $this->processFreightCharges($freight_charges, $quote, $company_user->currency->alphacode, $containers);

        $view = \View::make('quotesv2.pdf.index', ['quote' => $quote, 'containers' => $containers, 'rates' => $rates, 'origin_harbor' => $origin_harbor, 'destination_harbor' => $destination_harbor, 'user' => $user, 'currency_cfg' => $currency_cfg, 'equipmentHides' => $equipmentHides, 'freight_charges_grouped' => $freight_charges_grouped, 'destination_charges_detailed' => $destination_charges_detailed, 'origin_charges_grouped' => $origin_charges_grouped, 'origin_charges_detailed' => $origin_charges_detailed, 'destination_charges_grouped' => $destination_charges_grouped, 'sale_terms_origin' => $sale_terms_origin, 'sale_terms_destination' => $sale_terms_destination, 'sale_terms_origin_grouped' => $sale_terms_origin_grouped, 'sale_terms_destination_grouped' => $sale_terms_destination_grouped, 'origin_charges' => $origin_charges, 'destination_charges' => $destination_charges, 'freight_charges' => $freight_charges]);

        $pdf = \App::make('dompdf.wrapper');

        $pdfarray = array('pdf'=>$pdf,'view'=>$view,'idQuote'=>$quote->quote_id,'idQ'=>$quote->id);

        return $pdfarray;
    }

    public function processGlobalRates($rates, $quote, $currency_cfg, $containers)
    {
        $sum = 'sum';
        $markup = 'markup';
        $inland = 'inland';
        $amount = 'amount';
        $total = 'total';

        foreach ($rates as $item) {
            foreach ($containers as $c) {
                ${$sum . '_' . $c->code} = 0;
                ${$total . '_' . $c->code} = 0;
                ${$total . '_markup_' . $c->code} = 0;
            }

            $currency = Currency::find($item->currency_id);
            $item->currency_usd = $currency->rates;
            $item->currency_eur = $currency->rates_eur;

            //Charges
            $currency = Currency::find($item->currency_id);
            $item->currency_usd = $currency->rates;
            $item->currency_eur = $currency->rates_eur;

            $typeCurrency =  $currency_cfg;

            $currency_rate = $this->ratesCurrency($item->currency_id, $typeCurrency);

            //Charges
            foreach ($item->charge as $value) {

                if ($quote->pdf_option->grouped_total_currency == 1) {
                    $typeCurrency =  $quote->pdf_option->total_in_currency;
                } else {
                    $typeCurrency =  $currency_cfg;
                }

                $currency_rate = $this->ratesCurrency($value->currency_id, $typeCurrency);

                $array_amounts = json_decode($value->amount, true);
                $array_markups = json_decode($value->markups, true);
                $pre_c = 'total_c';
                $pre_m = 'total_m';

                foreach ($containers as $c) {
                    ${$pre_c . $c->code} = 'total_c' . $c->code;
                    ${$pre_m . $c->code} = 'total_m' . $c->code;
                    if (isset($array_amounts['c' . $c->code])) {
                        ${$amount . '_' . $c->code} = $array_amounts['c' . $c->code];
                        ${$amount . '_' . $total . '_' . $c->code} = ${$amount . '_' . $c->code} / $currency_rate;
                        ${$total . '_' . $c->code} = number_format(${$amount . '_' . $total . '_' . $c->code}, 2, '.', '');
                        $value->${$pre_c . $c->code} = ${$total . '_' . $c->code};
                    }

                    if (isset($array_markups['m' . $c->code])) {
                        ${$markup . '_' . $c->code} = $array_markups['m' . $c->code];
                        ${$total . '_markup_' . $c->code} = ${$markup . '_' . $c->code} / $currency_rate;
                        $value->${$pre_m . $c->code} = ${$total . '_markup_' . $c->code};
                    }
                }

                $currency_charge = Currency::find($value->currency_id);
                $value->currency_usd = $currency_charge->rates;
                $value->currency_eur = $currency_charge->rates_eur;
            }

            //Inland
            foreach ($item->inland as $item) {
                foreach ($containers as $c) {
                    ${$sum . '_' . $inland . '_' . $c->code} = 0;
                    ${$total . '_' . $inland . '_' . $markup . '_' . $c->code} = 0;
                }

                if ($quote->pdf_option->grouped_total_currency == 1) {
                    $typeCurrency =  $quote->pdf_option->total_in_currency;
                } else {
                    $typeCurrency =  $currency_cfg;
                }

                $currency_rate = $this->ratesCurrency($item->currency_id, $typeCurrency);

                $array_amounts = json_decode($item->rate, true);
                $array_markups = json_decode($item->markup, true);

                foreach ($containers as $c) {
                    ${$sum . '_' . $total . '_' . $inland . $c->code} = 0;
                    if (isset($array_amounts['c' . $c->code])) {
                        ${$amount . '_' . $inland . $c->code} = $array_amounts['c' . $c->code];
                        ${$total . '_' . $inland . $c->code} = ${$amount . '_' . $inland . $c->code} / $currency_rate;
                        ${$sum . '_' . $total . '_' . $inland . $c->code} = number_format(${$total . '_' . $inland . $c->code}, 2, '.', '');
                    }
                    if (isset($array_markups['m' . $c->code])) {
                        ${$markup . '_' . $inland . $c->code} = $array_markups['m' . $c->code];
                        ${$total . '_' . $inland . '_' . $markup . $c->code} = number_format(${$markup . '_' . $inland . $c->code} / $currency_rate, 2, '.', '');
                    }

                    $item->${$total . '_c' . $c->code} = number_format(@${$sum . '_' . $total . '_' . $inland . $c->code}, 2, '.', '');
                    $item->${$total . '_m' . $c->code} = number_format(@${$total . '_' . $inland . '_' . $markup . $c->code}, 2, '.', '');
                }

                $currency_charge = Currency::find($item->currency_id);
                $item->currency_usd = $currency_charge->rates;
                $item->currency_eur = $currency_charge->rates_eur;
            }
        }

        return $rates;
    }

    public function processGlobalRatesWithSales($rates, $quote, $currency_cfg, $origin_ports, $destination_ports, $containers)
    {

        $sum = 'sum';
        $markup = 'markup';
        $inland = 'inland';
        $amount = 'amount';
        $total = 'total';

        foreach ($rates as $item) {
            foreach ($containers as $c) {
                ${$sum . '_' . $c->code} = 0;
                ${$total . '_' . $c->code} = 0;
                ${$total . '_markup_' . $c->code} = 0;
            }

            $currency = Currency::find($item->currency_id);
            $item->currency_usd = $currency->rates;
            $item->currency_eur = $currency->rates_eur;

            //Charges
            foreach ($item->charge as $value) {

                if ($quote->pdf_option->grouped_total_currency == 1) {
                    $typeCurrency =  $quote->pdf_option->total_in_currency;
                } else {
                    $typeCurrency =  $currency_cfg;
                }
                $currency_rate = $this->ratesCurrency($value->currency_id, $typeCurrency);

                $array_amounts = json_decode($value->amount, true);
                $array_markups = json_decode($value->markups, true);

                foreach ($containers as $c) {
                    ${$pre_c . $c->code} = 'total_c' . $c->code;
                    ${$pre_m . $c->code} = 'total_m' . $c->code;
                    if (isset($array_amounts['c' . $c->code])) {
                        ${$amount . '_' . $c->code} = $array_amounts['c' . $c->code];
                        ${$amount . '_' . $total . '_' . $c->code} = ${$amount . '_' . $c->code} / $currency_rate;
                        ${$total . '_' . $c->code} = number_format(${$amount . '_' . $total . '_' . $c->code}, 2, '.', '');
                        $value->${$pre_c . $c->code} = ${$total . '_' . $c->code};
                    }

                    if (isset($array_markups['m' . $c->code])) {
                        ${$markup . '_' . $c->code} = $array_markups['m' . $c->code];
                        ${$total . '_markup_' . $c->code} = ${$markup . '_' . $c->code} / $currency_rate;
                        $value->${$pre_m . $c->code} = ${$total . '_markup_' . $c->code};
                    }
                }

                $currency_charge = Currency::find($value->currency_id);
                $value->currency_usd = $currency_charge->rates;
                $value->currency_eur = $currency_charge->rates_eur;
            }

            //Inland
            foreach ($item->inland as $item) {
                foreach ($containers as $c) {
                    ${$sum . '_' . $inland . '_' . $c->code} = 0;
                    ${$total . '_' . $inland . '_' . $markup . '_' . $c->code} = 0;
                }

                if ($quote->pdf_option->grouped_total_currency == 1) {
                    $typeCurrency =  $quote->pdf_option->total_in_currency;
                } else {
                    $typeCurrency =  $currency_cfg;
                }
                $currency_rate = $this->ratesCurrency($item->currency_id, $typeCurrency);

                $array_amounts = json_decode($item->rate, true);
                $array_markups = json_decode($item->markup, true);

                foreach ($containers as $c) {
                    ${$total . '_c' . $c->code} = 0;
                    if (isset($array_amounts['c' . $c->code])) {
                        ${$amount . '_' . $inland . $c->code} = $array_amounts['c' . $c->code];
                        ${$total . '_' . $inland . $c->code} = ${$amount . '_' . $inland . $c->code} / $currency_rate;
                        ${$sum . '_' . $total . '_' . $inland . $c->code} = number_format(${$total . '_' . $inland . $c->code}, 2, '.', '');
                    }
                    if (isset($array_markups['m' . $c->code])) {
                        ${$markup . '_' . $inland . $c->code} = $array_markups['m' . $c->code];
                        ${$total . '_' . $inland . '_' . $markup . $c->code} = number_format(${$markup . '_' . $inland . $c->code} / $currency_rate, 2, '.', '');
                    }

                    $item->${$total . '_c' . $c->code} = number_format(${$sum . '_' . $total . '_' . $inland . $c->code}, 2, '.', '');
                    $item->${$total . '_m' . $c->code} = number_format(${$total . '_' . $inland . '_' . $markup . $c->code}, 2, '.', '');
                }

                $currency_charge = Currency::find($item->currency_id);
                $item->currency_usd = $currency_charge->rates;
                $item->currency_eur = $currency_charge->rates_eur;
            }
        }

        return $rates;
    }

    /**
     * Process collections origins grouped rates
     * @param  collection $origin_charges
     * @param  collection $quote
     * @return collection
     */
    public function localChargesGrouped($charges_grouped, $type, $quote, $currency_cfg, $containers)
    {

        if ($type == 'origin') {
            $charges_grouped = $charges_grouped->groupBy([

                function ($item) {
                    return $item['origin_port']['name'] . ', ' . $item['origin_port']['code'];
                },
                function ($item) {
                    return $item['carrier']['name'];
                },

            ], $preserveKeys = true);
        } else {
            $charges_grouped = $charges_grouped->groupBy([

                function ($item) {
                    return $item['destination_port']['name'] . ', ' . $item['destination_port']['code'];
                },
                function ($item) {
                    return $item['carrier']['name'];
                },

            ], $preserveKeys = true);
        }

        $sum = 'sum_';
        $total = 'total_';
        $amount = 'amount_';
        $markup = 'markup_';
        $inland = 'inland_';

        foreach ($charges_grouped as $origin => $detail) {
            foreach ($detail as $item) {
                foreach ($item as $rate) {

                    foreach ($containers as $c) {
                        ${$sum . $c->code} = 0;
                        ${$total . $c->code} = 0;
                        ${$total . $markup . $c->code} = 0;
                    }

                    if (!$rate->charge->isEmpty()) {
                        foreach ($rate->charge as $value) {
                            if ($value->type_id == 1 || $value->type_id == 2) {

                                if ($quote->pdf_option->grouped_origin_charges == 1 || $quote->pdf_option->grouped_destination_charges == 1) {
                                    if ($value->type_id == 1) {
                                        $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                    } else {
                                        $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                                    }
                                } else {
                                    $typeCurrency =  $currency_cfg;
                                }

                                $currency_rate = $this->ratesCurrency($value->currency_id, $typeCurrency);

                                $array_amounts = json_decode($value->amount, true);
                                $array_markups = json_decode($value->markups, true);

                                foreach ($containers as $c) {
                                    ${$total . $c->code} = 0;
                                    ${$sum . $total . $c->code} = 'sum_total_' . $c->code;

                                    if (isset($array_amounts['c' . $c->code]) && isset($array_markups['m' . $c->code])) {
                                        ${$amount . $c->code} = $array_amounts['c' . $c->code];
                                        ${$markup . $c->code} = $array_markups['m' . $c->code];
                                        ${$total . $c->code} += number_format((${$amount . $c->code} + ${$markup . $c->code}) / $currency_rate, 2, '.', '');
                                    } else if (isset($array_amounts['c' . $c->code]) && !isset($array_markups['m' . $c->code])) {
                                        ${$amount . $c->code} = $array_amounts['c' . $c->code];
                                        ${$total . $c->code} += number_format(${$amount . $c->code} / $currency_rate, 2, '.', '');
                                    } else if (!isset($array_amounts['c' . $c->code]) && isset($array_markups['m' . $c->code])) {
                                        ${$markup . $c->code} = $array_markups['c' . $c->code];
                                        ${$total . $c->code} += number_format(${$markup . $c->code} / $currency_rate, 2, '.', '');
                                    }
                                    $value->${$sum . $total . $c->code} = number_format(${$total . $c->code}, 2, '.', '');
                                }
                            }
                        }
                    }
                    //Inlands
                    if (!$rate->inland->isEmpty()) {
                        foreach ($rate->inland as $value) {
                            foreach ($containers as $c) {
                                ${$inland . $c->code} = 0;
                            }

                            if ($quote->pdf_option->grouped_origin_charges == 1 || $quote->pdf_option->grouped_destination_charges == 1) {
                                if ($value->type == 'Origin') {
                                    $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                } else {
                                    $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                                }
                            } else {
                                $typeCurrency =  $currency_cfg;
                            }

                            $currency_rate = $this->ratesCurrency($value->currency_id, $typeCurrency);

                            $array_amounts = json_decode($value->rate, true);
                            $array_markups = json_decode($value->markup, true);

                            foreach ($containers as $c) {
                                ${$total . $c->code} = 0;
                                ${$sum . $total . $c->code} = 'sum_total_' . $c->code;

                                if (isset($array_amounts['c' . $c->code]) && isset($array_markups['m' . $c->code])) {
                                    ${$amount . $c->code} = $array_amounts['c' . $c->code];
                                    ${$markup . $c->code} = $array_markups['m' . $c->code];
                                    ${$total . $c->code} += number_format((${$amount . $c->code} + ${$markup . $c->code}) / $currency_rate, 2, '.', '');
                                } else if (isset($array_amounts['c' . $c->code]) && !isset($array_markups['m' . $c->code])) {
                                    ${$amount . $c->code} = $array_amounts['c' . $c->code];
                                    ${$total . $c->code} += number_format(${$amount . $c->code} / $currency_rate, 2, '.', '');
                                } else if (!isset($array_amounts['c' . $c->code]) && isset($array_markups['m' . $c->code])) {
                                    ${$markup . $c->code} = $array_markups['m' . $c->code];
                                    ${$total . $c->code} += number_format(${$markup . $c->code} / $currency_rate, 2, '.', '');
                                } else {
                                    ${$total . $c->code} = 0;
                                }

                                $value->${$sum . $total . $c->code} = round(${$total . $c->code});
                            }
                        }
                    }
                }
            }
        }

        return $charges_grouped;
    }


    /**
     * Process collections origins grouped rates
     * @param  collection $origin_charges
     * @param  collection $quote
     * @return collection
     */
    public function localChargesDetailed($charges_detailed, $type, $quote, $currency_cfg, $containers)
    {

        if ($type == 'origin') {
            $charges_detailed = $charges_detailed->groupBy([

                function ($item) {
                    return $item['carrier']['name'];
                },
                function ($item) {
                    return $item['origin_port']['name'] . ', ' . $item['origin_port']['code'];
                },
                function ($item) {
                    return $item['destination_port']['name'];
                },

            ]);
        } else {

            $charges_detailed = $charges_detailed->groupBy([

                function ($item) {
                    return $item['carrier']['name'];
                },
                function ($item) {
                    return $item['destination_port']['name'] . ', ' . $item['destination_port']['code'];
                },
                function ($item) {
                    return $item['origin_port']['name'];
                },

            ]);
        }

        $sum = 'sum';
        $markup = 'markup';
        $inland = 'inland';
        $amount = 'amount';
        $total = 'total';
        $pre_c = 'total_c';
        $pre_m = 'total_m';
        $charge_origin = 0;
        $charge_destination = 0;
        $inland_origin = 0;
        $inland_destination = 0;

        foreach ($charges_detailed as $origin => $item) {
            foreach ($item as $destination => $items) {
                foreach ($items as $carrier => $itemsDetail) {
                    foreach ($itemsDetail as $value) {
                        if (!$value->charge->isEmpty()) {
                            foreach ($value->charge as $amounts) {
                                foreach ($containers as $c) {
                                    ${$sum . '_' . $c->code} = 0;
                                    ${$total . '_' . $c->code} = 0;
                                    ${$total . '_markup_' . $c->code} = 0;
                                }

                                if ($amounts->type_id == 1 || $amounts->type_id == 2) {

                                    $typeCurrency =  $currency_cfg;

                                    $currency_rate = $this->ratesCurrency($amounts->currency_id, $typeCurrency);

                                    $array_amounts = json_decode($amounts->amount, true);
                                    $array_markups = json_decode($amounts->markups, true);

                                    foreach ($containers as $c) {
                                        ${$pre_c . $c->code} = 'total_c' . $c->code;
                                        if (isset($array_amounts['c' . $c->code]) && isset($array_markups['m' . $c->code])) {
                                            ${$sum . '_' . $c->code} = $array_amounts['c' . $c->code] + $array_markups['m' . $c->code];
                                            ${$total . '_' . $c->code} = ${$sum . '_' . $c->code} / $currency_rate;
                                        } else if (isset($array_amounts['c' . $c->code]) && !isset($array_markups['m' . $c->code])) {
                                            ${$sum . '_' . $c->code} = $array_amounts['c' . $c->code];
                                            ${$total . '_' . $c->code} = ${$sum . '_' . $c->code} / $currency_rate;
                                        } else if (!isset($array_amounts['c' . $c->code]) && isset($array_markups['m' . $c->code])) {
                                            ${$sum . '_' . $c->code} = $array_markups['m' . $c->code];
                                            ${$total . '_' . $c->code} = ${$sum . '_' . $c->code} / $currency_rate;
                                        }
                                        if ($amounts->type_id == 1) {
                                            $charge_origin++;
                                        }
                                        if ($amounts->type_id == 2) {
                                            $charge_destination++;
                                        }
                                        $amounts->${$pre_c . $c->code} = round(${$total . '_' . $c->code});
                                    }
                                }
                            }
                        }
                        if (!$value->inland->isEmpty()) {
                            foreach ($value->inland as $inland_value) {
                                foreach ($containers as $c) {
                                    ${$sum . '_' . $c->code} = 0;
                                    ${$total . '_' . $c->code} = 0;
                                    ${$total . '_markup_' . $c->code} = 0;
                                }
                                
                                $typeCurrency =  $currency_cfg;

                                $currency_rate = $this->ratesCurrency($inland_value->currency_id, $typeCurrency);

                                $array_amounts = json_decode($inland_value->rate, true);
                                $array_markups = json_decode($inland_value->markup, true);

                                foreach ($containers as $c) {
                                    ${$inland . '_' . $c->code} = 0;
                                    ${$total . '_' . $inland . $c->code} = 'total_inland' . $c->code;

                                    if (isset($array_amounts['c' . $c->code]) && isset($array_markups['m' . $c->code])) {
                                        ${$amount . '_' . $c->code} = $array_amounts['c' . $c->code];
                                        ${$markup . '_' . $c->code} = $array_markups['m' . $c->code];
                                        ${$total . '_' . $c->code} = number_format((${$amount . '_' . $c->code} + ${$markup . '_' . $c->code}) / $currency_rate, 2, '.', '');
                                    } else if (isset($array_amounts['c' . $c->code]) && !isset($array_markups['m' . $c->code])) {
                                        ${$amount . '_' . $c->code} = $array_amounts['c' . $c->code];
                                        ${$total . '_' . $c->code} = number_format(${$amount . '_' . $c->code} / $currency_rate, 2, '.', '');
                                    } else if (!isset($array_amounts['c' . $c->code]) && isset($array_markups['m' . $c->code])) {
                                        ${$markup . '_' . $c->code} = $array_markups['m' . $c->code];
                                        ${$total . '_' . $c->code} = number_format(${$markup . '_' . $c->code} / $currency_rate, 2, '.', '');
                                    }
                                    if ($inland_value->type == 'Origin') {
                                        $inland_origin++;
                                    }
                                    if ($inland_value->type == 'Destination') {
                                        $inland_destination++;
                                    }
                                    $inland_value->${$total . '_' . $inland . $c->code} = ${$total . '_' . $c->code};
                                }
                            }
                        }
                    }
                }
            }
            $item->charge_origin = $charge_origin;
            $item->charge_destination = $charge_destination;
            $item->inland_origin = $inland_origin;
            $item->inland_destination = $inland_destination;
        }

        return $charges_detailed;
    }

    /**
     * Process collections freight charges
     * @param  collection $freight_charges
     * @param  collection $quote
     * @return collection
     */
    public function processFreightCharges($freight_charges, $quote, $currency_cfg, $containers)
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

        foreach ($freight_charges_grouped as $freight) {
            foreach ($freight as $detail) {
                foreach ($detail as $item) {
                    foreach ($containers as $c) {
                        ${$total . $amount . $c->code} = 0;
                        ${$total . $amount . $markup . $c->code} = 0;
                    }

                    foreach ($item as $rate) {
                        foreach ($rate->charge as $amounts) {
                            if ($amounts->type_id == 3) {

                                if ($quote->pdf_option->grouped_freight_charges == 1) {
                                    $typeCurrency = $quote->pdf_option->freight_charges_currency;
                                } else {
                                    $typeCurrency = $currency_cfg;
                                }

                                $currency_rate = $this->ratesCurrency($amounts->currency_id, $typeCurrency);

                                $array_amounts = json_decode($amounts->amount, true);
                                $array_markups = json_decode($amounts->markups, true);

                                foreach ($containers as $c) {
                                    ${$sum . $c->code} = 0;
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
                                        $amounts->${$total . $sum . $c->code} = round(${$total . $c->code});
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $freight_charges_grouped;
    }

    public function processChargesLclAir($charges, $type, $type_2, $carrier)
    {
        $charges_grouped = collect($charges);

        $charges_grouped = $charges_grouped->groupBy([

            function ($item) {
                return $item[$type]['name'] . ', ' . $item[$type]['code'];
            },
            function ($item) {
                return $item[$carrier]['name'];
            },
            function ($item) {
                return $item[$type_2]['name'];
            },
        ], $preserveKeys = true);
        foreach ($charges as $origin => $detail) {
            foreach ($detail as $item) {
                foreach ($item as $v) {
                    foreach ($v as $rate) {
                        foreach ($rate->charge_lcl_air as $value) {

                            if ($value->type_id == 1) {
                                if ($quote->pdf_option->grouped_origin_charges == 1) {
                                    $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                } else {
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }

                                $currency_rate = $this->ratesCurrency($value->currency_id, $typeCurrency);
                                $value->rate = number_format((($value->units * $value->price_per_unit) + $value->markup) / $value->units, 2, '.', '');
                                $value->total_origin = number_format((($value->units * $value->price_per_unit) + $value->markup) / $currency_rate, 2, '.', '');
                            }
                        }
                    }
                }
            }
        }

        return $charges_grouped;
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

    /**
     * Mostrar/Ocultar contenedores en la vista
     * @param array $equipmentForm 
     * @param integer $tipo 
     * @return type
     */
    public function hideContainer($equipmentForm, $tipo)
    {
        $equipment = new Collection();
        $hidden20 = 'hidden';
        $hidden40 = 'hidden';
        $hidden40hc = 'hidden';
        $hidden40nor = 'hidden';
        $hidden45 = 'hidden';
        // Clases para reordenamiento de la tabla y ajuste
        $originClass = 'col-md-2';
        $destinyClass = 'col-md-1';
        $dataOrigDest = 'col-md-3';

        if ($tipo == 'BD') {
            $equipmentForm = json_decode($equipmentForm);
        }

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

        foreach ($equipmentForm as $val) {
            if ($val == '20') {
                $hidden20 = '';
            }
            if ($val == '40') {
                $hidden40 = '';
            }
            if ($val == '40HC') {
                $hidden40hc = '';
            }
            if ($val == '40NOR') {
                $hidden40nor = '';
            }
            if ($val == '45') {
                $hidden45 = '';
            }
        }
        $equipment->put('originClass', $originClass);
        $equipment->put('destinyClass', $destinyClass);
        $equipment->put('dataOrigDest', $dataOrigDest);
        $equipment->put('20', $hidden20);
        $equipment->put('40', $hidden40);
        $equipment->put('40hc', $hidden40hc);
        $equipment->put('40nor', $hidden40nor);
        $equipment->put('45', $hidden45);
        return ($equipment);
    }

    public function ratesCurrency($id, $typeCurrency)
    {
        $rates = Currency::where('id', '=', $id)->get();
        foreach ($rates as $rate) {
            if ($typeCurrency == "USD") {
                $rateC = $rate->rates;
            } else {
                $rateC = $rate->rates_eur;
            }
        }
        return $rateC;
    }

    public function getPortsInArray($collection)
    {
        $array = array();

        foreach ($collection as $value) {
            $array["port_id"] = $value->port_id;
        }

        return $array;
    }

    public function getAirportsInArray($collection)
    {
        $array = array();

        foreach ($collection as $value) {
            $array["airport_id"] = $value->airport_id;
        }

        return $array;
    }

    public function addSaleTermToRate($rates, $origin_ports, $destination_ports, $sale_terms_origin_grouped, $sale_terms_destination_grouped)
    {
        $rates = $rates->map(function ($item, $key) use ($origin_ports, $destination_ports, $sale_terms_origin_grouped, $sale_terms_destination_grouped) {
            if (in_array($item->origin_port_id, $origin_ports)) {
                if (!$item->charge->whereIn('type_id', 1)->isEmpty()) {
                    $item->charge->map(function ($value, $key) use ($sale_terms_origin_grouped, $item) {
                        if ($value->type_id == 1) {
                            //Seteamos valores de los charges originales a 0
                            $value->total_20 = 0;
                            $value->total_40 = 0;
                            $value->total_40hc = 0;
                            $value->total_40nor = 0;
                            $value->total_45 = 0;
                            $value->total_markup20 = 0;
                            $value->total_markup40 = 0;
                            $value->total_markup40hc = 0;
                            $value->total_markup40nor = 0;
                            $value->total_markup45 = 0;
                        }
                    });
                    //Añadimos los saleterms a la colección de Rates
                    $sale_terms_origin_grouped->map(function ($a) use ($item) {
                        $a->charge->map(function ($x) use ($item) {
                            $charge = new Charge();
                            $charge->type_id = 1;
                            $charge->total_20 = $x->sum20;
                            $charge->total_40 = $x->sum40;
                            $charge->total_40hc = $x->sum40hc;
                            $charge->total_40nor = $x->sum40nor;
                            $charge->total_45 = $x->sum45;
                            $charge->currency_id = $x->currency_id;
                            $item->charge->push($charge);
                        });
                    });
                } else {
                    //Añadimos los saleterms a la colección de Rates si esta vacío la relación con Charges
                    $sale_terms_origin_grouped->map(function ($a) use ($item) {
                        $a->charge->map(function ($x) use ($item) {
                            $charge = new Charge();
                            $charge->type_id = 1;
                            $charge->total_20 = $x->sum20;
                            $charge->total_40 = $x->sum40;
                            $charge->total_40hc = $x->sum40hc;
                            $charge->total_40nor = $x->sum40nor;
                            $charge->total_45 = $x->sum45;
                            $charge->currency_id = $x->currency_id;
                            $item->charge->push($charge);
                        });
                    });
                }
            }
            if (in_array($item->destination_port_id, $destination_ports)) {
                if (!$item->charge->whereIn('type_id', 2)->isEmpty()) {
                    $item->charge->map(function ($value, $key) use ($sale_terms_destination_grouped, $item) {
                        if ($value->type_id == 2) {
                            //Seteamos valores de los charges originales a 0
                            $value->total_20 = 0;
                            $value->total_40 = 0;
                            $value->total_40hc = 0;
                            $value->total_40nor = 0;
                            $value->total_45 = 0;
                            $value->total_markup20 = 0;
                            $value->total_markup40 = 0;
                            $value->total_markup40hc = 0;
                            $value->total_markup40nor = 0;
                            $value->total_markup45 = 0;
                        }
                    });
                    //Añadimos los saleterms a la colección de Rates
                    $sale_terms_destination_grouped->map(function ($a) use ($item) {
                        $a->charge->map(function ($x) use ($item) {
                            $charge = new Charge();
                            $charge->type_id = 2;
                            $charge->total_20 = $x->sum20;
                            $charge->total_40 = $x->sum40;
                            $charge->total_40hc = $x->sum40hc;
                            $charge->total_40nor = $x->sum40nor;
                            $charge->total_45 = $x->sum45;
                            $charge->currency_id = $x->currency_id;
                            $item->charge->push($charge);
                        });
                    });
                } else {
                    //Añadimos los saleterms a la colección de Rates si esta vacío la relación con Charges
                    $sale_terms_destination_grouped->map(function ($a) use ($item) {
                        $a->charge->map(function ($x) use ($item) {
                            $charge = new Charge();
                            $charge->type_id = 2;
                            $charge->total_20 = $x->sum20;
                            $charge->total_40 = $x->sum40;
                            $charge->total_40hc = $x->sum40hc;
                            $charge->total_40nor = $x->sum40nor;
                            $charge->total_45 = $x->sum45;
                            $charge->currency_id = $x->currency_id;
                            $item->charge->push($charge);
                        });
                    });
                }
            }

            return $item;
        });

        return $rates;
    }


    public function addSaleTermToRateLcl($rates, $origin_ports, $destination_ports, $sale_terms_origin_grouped, $sale_terms_destination_grouped)
    {
        $rates = $rates->map(function ($item, $key) use ($origin_ports, $destination_ports, $sale_terms_origin_grouped, $sale_terms_destination_grouped) {
            if (in_array($item->origin_port_id, $origin_ports)) {
                if (!$item->charge->whereIn('type_id', 1)->isEmpty()) {
                    $item->charge_lcl_air->map(function ($value, $key) use ($sale_terms_origin_grouped, $item) {
                        if ($value->type_id == 1) {
                            $value->total_origin = 0;
                        }
                    });
                    //Añadimos los saleterms a la colección de Rates
                    $sale_terms_origin_grouped->map(function ($a) use ($item) {
                        $a->charge->map(function ($x) use ($item) {
                            $charge = new ChargeLclAir();
                            $charge->type_id = 1;
                            $charge->total_origin = $x->total_sale_origin;
                            $charge->currency_id = $x->currency_id;
                            $item->charge_lcl_air->push($charge);
                        });
                    });
                } else {

                    //Añadimos los saleterms a la colección de Rates si esta vacío la relación con Charges
                    $sale_terms_origin_grouped->map(function ($a) use ($item) {
                        $a->charge->map(function ($x) use ($item) {
                            $charge = new ChargeLclAir();
                            $charge->type_id = 1;
                            $charge->total_origin = $x->total_sale_origin;
                            $charge->currency_id = $x->currency_id;
                            $item->charge_lcl_air->push($charge);
                        });
                    });
                }
            }
            if (in_array($item->destination_port_id, $destination_ports)) {
                $item->charge_lcl_air->map(function ($value, $key) use ($sale_terms_destination_grouped, $item) {
                    if ($value->type_id == 2) {
                        $value->total_destination = 0;
                    }
                });
                //Añadimos los saleterms a la colección de Rates
                $sale_terms_destination_grouped->map(function ($a) use ($item) {
                    $a->charge->map(function ($x) use ($item) {
                        $charge = new ChargeLclAir();
                        $charge->type_id = 2;
                        $charge->total_destination = $x->total_sale_destination;
                        $charge->currency_id = $x->currency_id;
                        $item->charge_lcl_air->push($charge);
                    });
                });
            } else {
                //Añadimos los saleterms a la colección de Rates si esta vacío la relación con Charges
                $sale_terms_destination_grouped->map(function ($a) use ($item) {
                    $a->charge->map(function ($x) use ($item) {
                        $charge = new ChargeLclAir();
                        $charge->type_id = 2;
                        $charge->total_destination = $x->total_sale_destination;
                        $charge->currency_id = $x->currency_id;
                        $item->charge_lcl_air->push($charge);
                    });
                });
            }

            return $item;
        });

        return $rates;
    }

    public function saveEmailNotification($to, $email_from, $subject, $body, $quote, $sign_type, $sign, $contact_email)
    {
        if ($to != '') {
            $explode = explode(';', $to);
            foreach ($explode as $item) {
                $send_quote = new SendQuote();
                $send_quote->to = trim($item);
                $send_quote->from = $email_from;
                $send_quote->subject = $subject;
                $send_quote->body = $body;
                $send_quote->quote_id = $quote->id;
                if ($sign_type != '') {
                    $send_quote->sign_type = $sign_type;
                }
                $send_quote->sign = $sign;
                $send_quote->status = 0;
                $send_quote->save();
            }
        } else {
            $send_quote = new SendQuote();
            $send_quote->to = $contact_email->email;
            $send_quote->from = $email_from;
            $send_quote->subject = $subject;
            $send_quote->body = $body;
            $send_quote->quote_id = $quote->id;
            if ($sign_type != '') {
                $send_quote->sign_type = $sign_type;
            }
            $send_quote->sign = $sign;
            $send_quote->status = 0;
            $send_quote->save();
        }
    }

    public function processShowQuoteRates($rates, $company_user, $containers)
    {
        foreach ($rates as $item) {
            $sum = 'sum';
            $markup = 'markup';
            $total = 'total';
            foreach ($containers as $c) {
                ${$sum . '_' . $c->code} = 0;
                ${$total . '_' . $c->code} = 0;
                ${$total . '_markup_' . $c->code} = 0;
            }

            $currency = Currency::find($item->currency_id);
            $item->currency_usd = $currency->rates;
            $item->currency_eur = $currency->rates_eur;

            $typeCurrency =  $company_user->currency->alphacode;

            $currency_rate = $this->ratesCurrency($item->currency_id, $typeCurrency);

            //Charges
            foreach ($item->charge as $value) {
                $currency_rate = $this->ratesCurrency($value->currency_id, $typeCurrency);

                $array_amounts = json_decode($value->amount, true);
                $array_markups = json_decode($value->markups, true);
                $amount = 'amount';
                $pre = 'c';

                foreach ($containers as $c) {
                    ${$pre . $c->code} = 'c' . $c->code;
                    ${$pre . $c->code . '_markup'} = 'c' . $c->code . '_markup';
                    if (isset($array_amounts['c' . $c->code])) {
                        ${$amount . '_' . $c->code} = $array_amounts['c' . $c->code];
                        ${$amount . '_' . $total . '_' . $c->code} = ${$amount . '_' . $c->code} / $currency_rate;
                        ${$total . '_' . $c->code} = number_format(${$amount . '_' . $total . '_' . $c->code}, 2, '.', '');
                    }

                    if (isset($array_markups['m' . $c->code])) {
                        ${$markup . '_' . $c->code} = $array_markups['m' . $c->code];
                        ${$total . '_markup_' . $c->code} = ${$markup . '_' . $c->code} / $currency_rate;
                    }

                    $value->${$pre . $c->code} = number_format(${$total . '_' . $c->code}, 2, '.', '');
                    $value->${$pre . $c->code . '_markup'} = number_format(${$total . '_markup_' . $c->code}, 2, '.', '');
                }

                $currency_charge = Currency::find($value->currency_id);
                $value->currency_usd = $currency_charge->rates;
                $value->currency_eur = $currency_charge->rates_eur;
            }

            //Charges LCL/AIR
            foreach ($item->charge_lcl_air as $value) {

                $currency_rate = $this->ratesCurrency($value->currency_id, $typeCurrency);

                if ($value->type_id == 3) {
                    $value->price_per_unit = number_format(($value->price_per_unit), 2, '.', '');
                    $value->markup = number_format(($value->markup), 2, '.', '');
                    $value->total_freight = number_format((($value->units * $value->price_per_unit) + $value->markup) / $currency_rate, 2, '.', '');
                    //$value->total_freight=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                } elseif ($value->type_id == 1) {
                    $value->price_per_unit = number_format(($value->price_per_unit), 2, '.', '');
                    $value->markup = number_format(($value->markup), 2, '.', '');
                    $value->total_origin = number_format((($value->units * $value->price_per_unit) + $value->markup) / $currency_rate, 2, '.', '');
                    //$value->total_origin=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                } else {
                    $value->price_per_unit = number_format(($value->price_per_unit), 2, '.', '');
                    $value->markup = number_format(($value->markup), 2, '.', '');
                    $value->total_destination = number_format((($value->units * $value->price_per_unit) + $value->markup) / $currency_rate, 2, '.', '');
                    //$value->total_destination=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                }
            }

            //Inland

            foreach ($containers as $c) {
                ${'sum_inland_' . $c->code} = 0;
            }

            foreach ($item->inland as $inland) {
                $typeCurrency =  $company_user->currency->alphacode;
                $currency_rate = $this->ratesCurrency($inland->currency_id, $typeCurrency);
                $array_amounts = json_decode($inland->rate, true);
                $array_markups = json_decode($inland->markup, true);

                foreach ($containers as $c) {
                    ${$pre . $c->code} = 'c' . $c->code;
                    ${$pre . $c->code . '_markup'} = 'c' . $c->code . '_markup';

                    if (isset($array_amounts['c' . $c->code]) && isset($array_markups['m' . $c->code])) {
                        ${$amount . '_inland_' . $c->code} = $array_amounts['c' . $c->code];
                        ${'markup_inland_' . $c->code} = $array_markups['m' . $c->code];
                        ${$total . '_inland_' . $c->code} = (${$amount . '_inland_' . $c->code} + ${'markup_inland_' . $c->code}) / $currency_rate;
                        ${'sum_inland_' . $c->code} = number_format(${$total . '_inland_' . $c->code}, 2, '.', '');
                    } else if (isset($array_amounts['c' . $c->code]) && !isset($array_markups['m' . $c->code])) {
                        ${$amount . '_inland_' . $c->code} = $array_amounts['c' . $c->code];
                        ${$total . '_inland_' . $c->code} = ${$amount . '_inland_' . $c->code} / $currency_rate;
                        ${'sum_inland_' . $c->code} = number_format(${$total . '_inland_' . $c->code}, 2, '.', '');
                    } else if (!isset($array_amounts['c' . $c->code]) && isset($array_markups['m' . $c->code])) {
                        ${'markup_inland_' . $c->code} = $array_markups['m' . $c->code];
                        ${$total . '_inland_' . $c->code} = ${'markup_inland_' . $c->code} / $currency_rate;
                        ${'sum_inland_' . $c->code} = number_format(${$total . '_inland_' . $c->code}, 2, '.', '');
                    }

                    $value->${'total_' . $c->code} = number_format(${'sum_inland_' . $c->code}, 2, '.', '');
                }

                $currency_charge = Currency::find($inland->currency_id);
                $inland->currency_usd = $currency_charge->rates;
                $inland->currency_eur = $currency_charge->rates_eur;
            }
        }
    }

    public function processSaleTerms($sale_terms, $quote, $company_user, $type)
    {
        foreach ($sale_terms as $value) {
            foreach ($value->charge as $item) {
                if ($item->currency_id != '') {
                    if ($type == 'origin') {
                        if ($quote->pdf_option->grouped_origin_charges == 1) {
                            $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                        } else {
                            $typeCurrency =  $company_user->currency->alphacode;
                        }
                    } else {
                        if ($quote->pdf_option->grouped_destination_charges == 1) {
                            $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                        } else {
                            $typeCurrency =  $company_user->currency->alphacode;
                        }
                    }
                    $currency_rate = $this->ratesCurrency($item->currency_id, $typeCurrency);
                    $item->sum20 += $item->c20 / $currency_rate;
                    $item->sum40 += $item->c40 / $currency_rate;
                    $item->sum40hc += $item->c40hc / $currency_rate;
                    $item->sum40nor += $item->c40nor / $currency_rate;
                    $item->sum45 += $item->c45 / $currency_rate;
                }
            }
        }

        return $sale_terms;
    }
}
