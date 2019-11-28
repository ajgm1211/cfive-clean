<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Exports\QuotesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\AutomaticRate;
use App\AutomaticInland;
use App\AutomaticInlandLclAir;
use App\CalculationType;
use App\CalculationTypeLcl;
use App\Charge;
use App\Company;
use App\CompanyUser;
use App\Contact;
use App\Country;
use App\Currency;
use App\EmailTemplate;
use App\Harbor;
use App\Incoterm;
use App\Price;
use App\Inland;
use App\Quote;
use App\Carrier;
use App\QuoteV2;
use App\Surcharge;
use App\User;
use App\PdfOption;
use EventIntercom;
use App\Jobs\SendQuotes;
use App\SendQuote;
use App\Contract;
use App\Rate;
use App\LocalCharge;
use App\LocalCharCarrier;
use App\LocalCharPort;
use App\GlobalCharge;
use App\GlobalCharPort;
use App\GlobalCharCarrier;
use App\PackageLoad;
use App\ChargeLclAir;
use Illuminate\Support\Facades\Input;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as Collection;
use App\Repositories\Schedules;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use App\PackageLoadV2;
use App\Airline;
use App\TermsPort;
use App\TermsAndCondition;
use App\TermAndConditionV2;
use App\ScheduleType;
use App\EmailSetting;
use App\SaleTermV2;
use App\ViewQuoteV2;
use App\SaleTermV2Charge;
use App\Http\Traits\QuoteV2Trait;

class PdfV2Controller extends Controller
{

    use QuoteV2Trait;

    /**
   * Generate PDF FCL
   * @param Request $request 
   * @param integer $id      
   * @return type
   */
    public function pdf(Request $request,$id)
    {
        $id = obtenerRouteKey($id);
        $equipmentHides = '';

        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $currency_cfg = Currency::find($company_user->currency_id);
        }
        $quote = QuoteV2::findOrFail($id);
        $rates = AutomaticRate::where('quote_id',$quote->id)->with('charge')->get();

        /* Sale terms */

        $sale_terms_origin = SaleTermV2::where('quote_id',$quote->id)->where('type','Origin')->with('charge')->get();
        $sale_terms_destination = SaleTermV2::where('quote_id',$quote->id)->where('type','Destination')->with('charge')->get();
        $sale_terms_origin_grouped = SaleTermV2::where('quote_id',$quote->id)->where('type','Origin')->with('charge')->get();
        $sale_terms_destination_grouped = SaleTermV2::where('quote_id',$quote->id)->where('type','Destination')->with('charge')->get();

        foreach($sale_terms_origin_grouped as $origin_sale){
            foreach($origin_sale->charge as $origin_charge){
                if($origin_charge->currency_id!=''){
                    if($quote->pdf_option->grouped_total_currency==1){
                        $typeCurrency =  $quote->pdf_option->total_in_currency;
                    }else{
                        $typeCurrency =  $currency_cfg->alphacode;
                    }
                    $currency_rate=$this->ratesCurrency($origin_charge->currency_id,$typeCurrency);
                    $origin_charge->sum20 += $origin_charge->c20/$currency_rate;
                    $origin_charge->sum40 += $origin_charge->c40/$currency_rate;
                    $origin_charge->sum40hc += $origin_charge->c40hc/$currency_rate;
                    $origin_charge->sum40nor += $origin_charge->c40nor/$currency_rate;
                    $origin_charge->sum45 += $origin_charge->c45/$currency_rate;
                }
            }
        }

        foreach($sale_terms_destination_grouped as $value){
            foreach($value->charge as $item){
                if($item->currency_id!=''){
                    if($quote->pdf_option->grouped_total_currency==1){
                        $typeCurrency =  $quote->pdf_option->total_in_currency;
                    }else{
                        $typeCurrency =  $currency_cfg->alphacode;
                    }
                    $currency_rate=$this->ratesCurrency($item->currency_id,$typeCurrency);
                    $item->sum20 += $item->c20/$currency_rate;
                    $item->sum40 += $item->c40/$currency_rate;
                    $item->sum40hc += $item->c40hc/$currency_rate;
                    $item->sum40nor += $item->c40nor/$currency_rate;
                    $item->sum45 += $item->c45/$currency_rate;
                }
            }
        }

        $sale_terms_origin = collect($sale_terms_origin);

        $sale_terms_origin = $sale_terms_origin->groupBy([   
            function ($item) {
                return $item['port']['name'].', '.$item['port']['code'];
            },     
        ], $preserveKeys = true);

        foreach($sale_terms_origin as $value){
            foreach($value as $origin_sale){
                foreach($origin_sale->charge as $origin_charge){

                    if($origin_charge->currency_id!=''){
                        if($quote->pdf_option->grouped_origin_charges==1){
                            $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                        }else{
                            $typeCurrency =  $currency_cfg->alphacode;
                        }
                        $currency_rate=$this->ratesCurrency($origin_charge->currency_id,$typeCurrency);
                        $origin_charge->sum20 += $origin_charge->c20/$currency_rate;
                        $origin_charge->sum40 += $origin_charge->c40/$currency_rate;
                        $origin_charge->sum40hc += $origin_charge->c40hc/$currency_rate;
                        $origin_charge->sum40nor += $origin_charge->c40nor/$currency_rate;
                        $origin_charge->sum45 += $origin_charge->c45/$currency_rate;
                    }
                }
            }
        }

        $sale_terms_destination = collect($sale_terms_destination);

        $sale_terms_destination = $sale_terms_destination->groupBy([   
            function ($item) {
                return $item['port']['name'].', '.$item['port']['code'];
            },     
        ], $preserveKeys = true);

        foreach($sale_terms_destination as $destination_sale){
            foreach($destination_sale as $value){
                foreach($value->charge as $item){
                    if($item->currency_id!=''){
                        if($quote->pdf_option->grouped_destination_charges==1){
                            $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                        }else{
                            $typeCurrency =  $currency_cfg->alphacode;
                        }
                        $currency_rate=$this->ratesCurrency($item->currency_id,$typeCurrency);
                        $item->sum20 += $item->c20/$currency_rate;
                        $item->sum40 += $item->c40/$currency_rate;
                        $item->sum40hc += $item->c40hc/$currency_rate;
                        $item->sum40nor += $item->c40nor/$currency_rate;
                        $item->sum45 += $item->c45/$currency_rate;
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

        $origin_charges = AutomaticRate::whereNotIn('origin_port_id',$origin_ports)->where('quote_id',$quote->id)
            ->Charge(1,'Origin')->with('charge')->get();

        $destination_charges = AutomaticRate::whereNotIn('destination_port_id',$destination_ports)->where('quote_id',$quote->id)
            ->Charge(2,'Destination')->with('charge')->get();

        $freight_charges = AutomaticRate::whereHas('charge', function ($query) {
            $query->where('type_id', 3);
        })->with('charge')->where('quote_id',$quote->id)->get();

        /* Fin consulta de charges */

        $contact_email = Contact::find($quote->contact_id);
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $user = User::where('id',\Auth::id())->with('companyUser')->first();
        if($quote->equipment!=''){
            $equipmentHides = $this->hideContainer($quote->equipment,'BD');
        }

        /** Rates **/

        $rates = $this->processGlobalRates($rates, $quote, $currency_cfg);

        /* Se manipula la colección de rates para añadir los valores de saleterms */
        $rates = $rates->map(function ($item, $key) use($origin_ports, $destination_ports,$sale_terms_origin_grouped, $sale_terms_destination_grouped){
            if(in_array($item->origin_port_id,$origin_ports)){
                if(!$item->charge->whereIn('type_id',1)->isEmpty()){
                    $item->charge->map(function ($value, $key) use($sale_terms_origin_grouped,$item){
                        if($value->type_id==1){
                            //Seteamos valores de los charges originales a 0
                            $value->total_20=0;
                            $value->total_40=0;
                            $value->total_40hc=0;
                            $value->total_40nor=0;
                            $value->total_45=0;
                            $value->total_markup20=0;
                            $value->total_markup40=0;
                            $value->total_markup40hc=0;
                            $value->total_markup40nor=0;
                            $value->total_markup45=0;

                        }
                    });
                    //Añadimos los saleterms a la colección de Rates
                    $sale_terms_origin_grouped->map(function ($a) use($item) {
                        $a->charge->map(function ($x) use($item) {
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
                }else{
                    //Añadimos los saleterms a la colección de Rates si esta vacío la relación con Charges
                    $sale_terms_origin_grouped->map(function ($a) use($item) {
                        $a->charge->map(function ($x) use($item) {
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
            if(in_array($item->destination_port_id,$destination_ports)){
                if(!$item->charge->whereIn('type_id',2)->isEmpty()){
                    $item->charge->map(function ($value, $key) use($sale_terms_destination_grouped,$item){
                        if($value->type_id==2){
                            //Seteamos valores de los charges originales a 0
                            $value->total_20=0;
                            $value->total_40=0;
                            $value->total_40hc=0;
                            $value->total_40nor=0;
                            $value->total_45=0;
                            $value->total_markup20=0;
                            $value->total_markup40=0;
                            $value->total_markup40hc=0;
                            $value->total_markup40nor=0;
                            $value->total_markup45=0;

                        }
                    });
                    //Añadimos los saleterms a la colección de Rates
                    $sale_terms_destination_grouped->map(function ($a) use($item) {
                        $a->charge->map(function ($x) use($item) {
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
                }else{
                    //Añadimos los saleterms a la colección de Rates si esta vacío la relación con Charges
                    $sale_terms_destination_grouped->map(function ($a) use($item) {
                        $a->charge->map(function ($x) use($item) {
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

        /** Origin Charges **/

        $origin_charges_grouped=$this->processOriginGrouped($origin_charges, $quote, $currency_cfg);

        $origin_charges_detailed=$this->processOriginDetailed($origin_charges, $quote, $currency_cfg);

        /** Destination Charges **/

        $destination_charges_grouped=$this->processDestinationGrouped($destination_charges, $quote, $currency_cfg);

        $destination_charges=$this->processDestinationDetailed($destination_charges, $quote, $currency_cfg);

        /** Freight Charges **/

        $freight_charges_grouped = $this->processFreightCharges($freight_charges, $quote, $currency_cfg);

        $view = \View::make('quotesv2.pdf.index', ['quote'=>$quote,'rates'=>$rates,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'user'=>$user,'currency_cfg'=>$currency_cfg, 'equipmentHides'=>$equipmentHides,'freight_charges_grouped'=>$freight_charges_grouped,'destination_charges'=>$destination_charges,'origin_charges_grouped'=>$origin_charges_grouped,'origin_charges_detailed'=>$origin_charges_detailed,'destination_charges_grouped'=>$destination_charges_grouped,'sale_terms_origin'=>$sale_terms_origin,'sale_terms_destination'=>$sale_terms_destination,'sale_terms_origin_grouped'=>$sale_terms_origin_grouped,'sale_terms_destination_grouped'=>$sale_terms_destination_grouped,'origin_charges'=>$origin_charges,'destination_charges'=>$destination_charges,'freight_charges'=>$freight_charges]);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->save('pdf/temp_'.$quote->id.'.pdf');

        return $pdf->stream('quote-'.$quote->quote_id.'-'.date('Ymd').'.pdf');
    }

    /**
   * Generate PDF to LCL/AIR
   * @param Request $request 
   * @param integer $id 
   * @return type
   */
    public function pdfLclAir(Request $request,$id)
    {
        $id = obtenerRouteKey($id);
        $equipmentHides = null;
        $quote = QuoteV2::findOrFail($id);
        $rates_lcl_air = AutomaticRate::where('quote_id',$quote->id)->with('charge_lcl_air')->get();
        $sale_terms = SaleTermV2::where('quote_id',$quote->id)->with('charge')->select('port_id');
        $sale_terms_origin = SaleTermV2::where('quote_id',$quote->id)->where('type','Origin')->with('charge')->get();
        $sale_terms_destination = SaleTermV2::where('quote_id',$quote->id)->where('type','Destination')->with('charge')->get();
        $sale_terms_origin_grouped = SaleTermV2::where('quote_id',$quote->id)->where('type','Origin')->with('charge')->get();
        $sale_terms_destination_grouped = SaleTermV2::where('quote_id',$quote->id)->where('type','Destination')->with('charge')->get();

        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $type=$company_user->type_pdf;
            $ammounts_type=$company_user->pdf_ammounts;
            $currency_cfg = Currency::find($company_user->currency_id);
        }

        foreach($sale_terms_origin_grouped as $sale_origin){
            foreach($sale_origin->charge as $sale_origin_charge){
                if($sale_origin_charge->currency_id!=''){
                    if($quote->pdf_option->grouped_total_currency==1){
                        $typeCurrency =  $quote->pdf_option->total_in_currency;
                    }else{
                        $typeCurrency =  $currency_cfg->alphacode;
                    }

                    $currency_rate=$this->ratesCurrency($sale_origin_charge->currency_id,$typeCurrency);
                    $sale_origin_charge->total_sale_origin=number_format($sale_origin_charge->total/$currency_rate, 2, '.', '');
                }
            }
        }

        foreach($sale_terms_destination_grouped as $sale_destination){
            foreach($sale_destination->charge as $sale_destination_charge){

                if($sale_destination_charge->currency_id!=''){
                    if($quote->pdf_option->grouped_total_currency==1){
                        $typeCurrency =  $quote->pdf_option->total_in_currency;
                    }else{
                        $typeCurrency =  $currency_cfg->alphacode;
                    }
                    $currency_rate=$this->ratesCurrency($sale_destination_charge->currency_id,$typeCurrency);

                    $sale_destination_charge->total_sale_destination=number_format($sale_destination_charge->total/$currency_rate, 2, '.', '');
                }
            }
        }

        $sale_terms_origin = collect($sale_terms_origin);

        $sale_terms_origin = $sale_terms_origin->groupBy([   
            function ($item) {
                return $item['port']['name'].', '.$item['port']['code'];
            },     
        ], $preserveKeys = true);

        foreach($sale_terms_origin as $value){
            foreach($value as $origin_sale){
                foreach($origin_sale->charge as $origin_charge){

                    if($origin_charge->currency_id!=''){
                        if($quote->pdf_option->grouped_origin_charges==1){
                            $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                        }else{
                            $typeCurrency =  $currency_cfg->alphacode;
                        }
                        $currency_rate=$this->ratesCurrency($origin_charge->currency_id,$typeCurrency);

                        $origin_charge->total_sale_origin=number_format($origin_charge->total/$currency_rate, 2, '.', '');
                    }
                }
            }
        }

        $sale_terms_destination = collect($sale_terms_destination);

        $sale_terms_destination = $sale_terms_destination->groupBy([   
            function ($item) {
                return $item['port']['name'].', '.$item['port']['code'];
            },     
        ], $preserveKeys = true);

        foreach($sale_terms_destination as $destination_sale){
            foreach($destination_sale as $value){
                foreach($value->charge as $item){
                    if($item->currency_id!=''){
                        if($quote->pdf_option->grouped_destination_charges==1){
                            $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                        }else{
                            $typeCurrency =  $currency_cfg->alphacode;
                        }
                        $currency_rate=$this->ratesCurrency($item->currency_id,$typeCurrency);
                        $item->total_sale_destination=number_format($item->total/$currency_rate, 2, '.', '');
                    }
                }
            }
        }

        /* Arrays de puertos incluidos en los Saleterms */

        $origin_ports = $this->getPortsInArray($sale_terms_origin_grouped);

        $destination_ports = $this->getPortsInArray($sale_terms_destination_grouped);

        /* Fin arrays */

        $freight_charges = AutomaticRate::whereHas('charge_lcl_air', function ($query) {
            $query->where('type_id', 3);
        })->where('quote_id',$quote->id)->get();

        $origin_charges = AutomaticRate::whereNotIn('destination_port_id',$origin_ports)->where('quote_id',$quote->id)
            ->ChargeLclAir(1,'Origin')->get();

        $destination_charges = AutomaticRate::whereNotIn('destination_port_id',$destination_ports)->where('quote_id',$quote->id)
            ->ChargeLclAir(2,'Destination')->get();

        $contact_email = Contact::find($quote->contact_id);
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $user = User::where('id',\Auth::id())->with('companyUser')->first();
        $package_loads = PackageLoadV2::where('quote_id',$quote->id)->get();
        if($quote->equipment!=''){
            $equipmentHides = $this->hideContainer($quote->equipment,'BD');
        }

        foreach ($rates_lcl_air as $item) {

            foreach ($item->charge_lcl_air as $value) {

                if($quote->pdf_option->grouped_total_currency==1){
                    $typeCurrency = $quote->pdf_option->total_in_currency;
                }else{
                    $typeCurrency =  $currency_cfg->alphacode;
                }

                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);

                if($value->type_id==3){
                    if($value->units>0){
                        $value->total_freight=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');

                    }
                }elseif($value->type_id==1){
                    if($value->units>0){
                        $value->total_origin=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');

                    }
                }else{
                    if($value->units>0){
                        $value->total_destination=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                    }
                }
            }
            if(!$item->automaticInlandLclAir->isEmpty()){
                foreach($item->automaticInlandLclAir as $inland){
                    if($quote->pdf_option->grouped_origin_charges==1){
                        $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                    }else{
                        $typeCurrency =  $currency_cfg->alphacode;
                    }
                    $currency_rate=$this->ratesCurrency($inland->currency_id,$typeCurrency);
                    if($inland->units>0){
                        $inland->total_inland=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$currency_rate, 2, '.', '');
                    }
                }
            }
            foreach ($item->inland as $inland) {
                $currency_charge = Currency::find($inland->currency_id);
                $inland->currency_usd = $currency_charge->rates;
                $inland->currency_eur = $currency_charge->rates_eur;
            }


        }

        $rates_lcl_air = $rates_lcl_air->map(function ($item, $key) use($origin_ports, $destination_ports, $sale_terms_origin_grouped, $sale_terms_destination_grouped){
            if(in_array($item->origin_port_id,$origin_ports)){
                if(!$item->charge->whereIn('type_id',1)->isEmpty()){
                    $item->charge_lcl_air->map(function ($value, $key) use($sale_terms_origin_grouped,$item){
                        if($value->type_id==1){
                            $value->total_origin=0;
                        }
                    });
                    //Añadimos los saleterms a la colección de Rates
                    $sale_terms_origin_grouped->map(function ($a) use($item) {
                        $a->charge->map(function ($x) use($item) {
                            $charge = new ChargeLclAir();
                            $charge->type_id = 1;
                            $charge->total_origin = $x->total_sale_origin;
                            $charge->currency_id = $x->currency_id;
                            $item->charge_lcl_air->push($charge);
                        });
                    });
                }else{

                    //Añadimos los saleterms a la colección de Rates si esta vacío la relación con Charges
                    $sale_terms_origin_grouped->map(function ($a) use($item) {
                        $a->charge->map(function ($x) use($item) {
                            $charge = new ChargeLclAir();
                            $charge->type_id = 1;
                            $charge->total_origin = $x->total_sale_origin;
                            $charge->currency_id = $x->currency_id;
                            $item->charge_lcl_air->push($charge);
                        });
                    });
                }
            }
            if(in_array($item->destination_port_id,$destination_ports)){
                $item->charge_lcl_air->map(function ($value, $key) use($sale_terms_destination_grouped,$item){
                    if($value->type_id==2){
                        $value->total_destination=0;
                    }
                });
                //Añadimos los saleterms a la colección de Rates
                $sale_terms_destination_grouped->map(function ($a) use($item) {
                    $a->charge->map(function ($x) use($item) {
                        $charge = new ChargeLclAir();
                        $charge->type_id = 2;
                        $charge->total_destination = $x->total_sale_destination;
                        $charge->currency_id = $x->currency_id;
                        $item->charge_lcl_air->push($charge);
                    });
                });
            }else{
                //Añadimos los saleterms a la colección de Rates si esta vacío la relación con Charges
                $sale_terms_destination_grouped->map(function ($a) use($item) {
                    $a->charge->map(function ($x) use($item) {
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

        $origin_charges_grouped = collect($origin_charges);

        $origin_charges_grouped = $origin_charges_grouped->groupBy([

            function ($item) {
                return $item['origin_port']['name'].', '.$item['origin_port']['code'];
            },
            function ($item) {
                return $item['carrier']['name'];
            },      
            function ($item) {
                return $item['destination_port']['name'];
            },
        ], $preserveKeys = true);

        foreach($origin_charges_grouped as $origin=>$detail){
            foreach($detail as $item){
                foreach($item as $v){
                    foreach($v as $rate){
                        foreach($rate->charge_lcl_air as $v_origin){

                            if($v_origin->type_id==1){
                                if($quote->pdf_option->grouped_origin_charges==1){
                                    $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }

                                $currency_rate=$this->ratesCurrency($v_origin->currency_id,$typeCurrency);
                                if($v_origin->units>0){
                                    $v_origin->rate=number_format((($v_origin->units*$v_origin->price_per_unit)+$v_origin->markup)/$v_origin->units, 2, '.', '');
                                }else{
                                    $v_origin->rate=0;
                                }
                                $v_origin->total_origin=number_format((($v_origin->units*$v_origin->price_per_unit)+$v_origin->markup)/$currency_rate, 2, '.', '');
                            }
                        }

                        if(!$rate->automaticInlandLclAir->isEmpty()){
                            foreach($rate->automaticInlandLclAir as $inland){
                                if($inland->type=='Origin'){
                                    if($quote->pdf_option->grouped_origin_charges==1){
                                        $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                    }else{
                                        $typeCurrency =  $currency_cfg->alphacode;
                                    }
                                    $currency_rate=$this->ratesCurrency($inland->currency_id,$typeCurrency);
                                    if($inland->units>0){
                                        $inland->rate_amount=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$inland->units, 2, '.', '');
                                    }else{
                                        $inland->rate_amount=0;
                                    }
                                    $inland->total_inland_origin=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$currency_rate, 2, '.', '');

                                }
                            }
                        }
                    }
                }
            }
        }

        /*** DESTINATION CHARGES ***/

        $destination_charges_grouped = collect($destination_charges);

        $destination_charges_grouped = $destination_charges_grouped->groupBy([

            function ($item) {
                return $item['destination_port']['name'].', '.$item['destination_port']['code'];
            },
            function ($item) {
                return $item['carrier']['name'];
            },
            function ($item) {
                return $item['origin_port']['name'];
            },

        ], $preserveKeys = true);
        foreach($destination_charges_grouped as $origin=>$detail){
            foreach($detail as $item){
                foreach($item as $v){
                    foreach($v as $rate){
                        foreach($rate->charge_lcl_air as $v_destination){

                            if($v_destination->type_id==2){

                                if($quote->pdf_option->grouped_destination_charges==1){
                                    $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }
                                $currency_rate=$this->ratesCurrency($v_destination->currency_id,$typeCurrency);
                                if($v_destination->units>0){
                                    $v_destination->rate=number_format((($v_destination->units*$v_destination->price_per_unit)+$v_destination->markup)/$v_destination->units, 2, '.', '');
                                }else{
                                    $v_destination->rate=0;
                                }
                                $v_destination->total_destination=number_format((($v_destination->units*$v_destination->price_per_unit)+$v_destination->markup)/$currency_rate, 2, '.', '');
                            }
                        }
                        if(!$rate->automaticInlandLclAir->isEmpty()){
                            foreach($rate->automaticInlandLclAir as $v_destination_inland){
                                if($v_destination_inland->type=='Destination'){
                                    if($quote->pdf_option->grouped_origin_charges==1){
                                        $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                    }else{
                                        $typeCurrency =  $currency_cfg->alphacode;
                                    }
                                    $currency_rate=$this->ratesCurrency($v_destination_inland->currency_id,$typeCurrency);
                                    if($v_destination_inland->units>0){
                                        $v_destination_inland->rate_amount=number_format((($v_destination_inland->units*$v_destination_inland->price_per_unit)+$v_destination_inland->markup)/$v_destination_inland->units, 2, '.', '');
                                    }else{
                                        $v_destination_inland->rate_amount=0;
                                    }
                                    $v_destination_inland->total_inland_destination=number_format((($v_destination_inland->units*$v_destination_inland->price_per_unit)+$v_destination_inland->markup)/$currency_rate, 2, '.', '');
                                }
                            }
                        }
                    }
                }
            }
        }

        /** FREIGHT CHARGES **/

        $freight_charges_detailed = collect($freight_charges);

        $freight_charges_detailed = $freight_charges_detailed->groupBy([   
            function ($item) {
                return $item['origin_port']['name'].', '.$item['origin_port']['code'];
            },
            function ($item) {
                return $item['destination_port']['name'].', '.$item['destination_port']['code'];
            },
            function ($item) {
                return $item['carrier']['name'];
            },      
        ], $preserveKeys = true);

        foreach($freight_charges_detailed as $origin=>$item){
            foreach($item as $destination=>$items){
                foreach($items as $carrier=>$itemsDetail){
                    foreach ($itemsDetail as $value) {     
                        foreach ($value->charge as $amounts) {
                            if($amounts->type_id==3){
                                $sum_freight_20=0;
                                $sum_freight_40=0;
                                $sum_freight_40hc=0;
                                $sum_freight_40nor=0;
                                $sum_freight_45=0;
                                $total_freight_40=0;
                                $total_freight_20=0;
                                $total_freight_40hc=0;
                                $total_freight_40nor=0;
                                $total_freight_45=0;
                                //dd($quote->pdf_option->destination_charges_currency);
                                if($quote->pdf_option->grouped_freight_charges==1){
                                    $typeCurrency =  $quote->pdf_option->freight_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }
                                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                                $array_amounts = json_decode($amounts->amount,true);
                                $array_markups = json_decode($amounts->markups,true);
                                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                    $sum_freight_20=$array_amounts['c20']+$array_markups['m20'];
                                    $total_freight_20=$sum_freight_20/$currency_rate;
                                }
                                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                    $sum_freight_40=$array_amounts['c40']+$array_markups['m40'];
                                    $total_freight_40=$sum_freight_40/$currency_rate;
                                }
                                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                    $sum_freight_40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                                    $total_freight_40hc=$sum_freight_40hc/$currency_rate;
                                }
                                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                    $sum_freight_40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                                    $total_freight_40nor=$sum_freight_40nor/$currency_rate;
                                }
                                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                    $sum_freight_45=$array_amounts['c45']+$array_markups['m45'];
                                    $total_freight_45=$sum_freight_45/$currency_rate;
                                }            

                                $amounts->total_20 = number_format($total_freight_20, 2, '.', '');
                                $amounts->total_40 = number_format($total_freight_40, 2, '.', '');
                                $amounts->total_40hc = number_format($total_freight_40hc, 2, '.', '');
                                $amounts->total_40nor = number_format($total_freight_40nor, 2, '.', '');
                                $amounts->total_45 = number_format($total_freight_45, 2, '.', '');
                            }
                        }
                    }
                } 
            }
        }

        $freight_charges_grouped = collect($freight_charges);

        $freight_charges_grouped = $freight_charges_grouped->groupBy([

            function ($item) {
                return $item['origin_port']['name'].', '.$item['origin_port']['code'];
            },
            function ($item) {
                return $item['destination_port']['name'].', '.$item['destination_port']['code'];
            },
            function ($item) {
                return $item['carrier']['name'];
            },

        ], $preserveKeys = true);

        foreach($freight_charges_grouped as $freight){
            foreach($freight as $detail){
                foreach($detail as $item){
                    foreach($item as $rate){
                        foreach ($rate->charge_lcl_air as $v_freight) {
                            if($v_freight->type_id==3){
                                if($freight_charges_grouped->count()>1){
                                    $typeCurrency = $currency_cfg->alphacode;
                                }else{
                                    if($quote->pdf_option->grouped_freight_charges==1){
                                        $typeCurrency = $quote->pdf_option->freight_charges_currency;
                                    }else{
                                        $typeCurrency = $currency_cfg->alphacode;
                                    }
                                }
                                $currency_rate=$this->ratesCurrency($v_freight->currency_id,$typeCurrency);

                                //$value->price_per_unit=number_format(($value->price_per_unit/$currency_rate), 2, '.', '');
                                //$value->markup=number_format(($value->markup/$currency_rate), 2, '.', '');
                                if($v_freight->units>0){
                                    $v_freight->rate=number_format((($v_freight->units*$v_freight->price_per_unit)+$v_freight->markup)/$v_freight->units, 2, '.', '');
                                }else{
                                    $v_freight->rate=0;
                                }
                                $v_freight->total_freight=number_format((($v_freight->units*$v_freight->price_per_unit)+$v_freight->markup)/$currency_rate, 2, '.', '');

                            }
                        }
                    }
                }
            }
        }

        $view = \View::make('quotesv2.pdf.index_lcl_air', ['quote'=>$quote,'rates'=>$rates_lcl_air,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'user'=>$user,'currency_cfg'=>$currency_cfg,'charges_type'=>$type,'equipmentHides'=>$equipmentHides,'freight_charges_grouped'=>$freight_charges_grouped,'destination_charges'=>$destination_charges,'origin_charges_grouped'=>$origin_charges_grouped,'destination_charges_grouped'=>$destination_charges_grouped,'freight_charges_detailed'=>$freight_charges_detailed,'package_loads'=>$package_loads,'sale_terms_origin'=>$sale_terms_origin,'sale_terms_destination'=>$sale_terms_destination,'sale_terms_origin_grouped'=>$sale_terms_origin_grouped,'sale_terms_destination_grouped'=>$sale_terms_destination_grouped]);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->save('pdf/temp_'.$quote->id.'.pdf');

        return $pdf->stream('quote-'.$quote->quote_id.'-'.date('Ymd').'.pdf');
    }

    /**
   * Generate PDF to LCL/AIR
   * @param Request $request 
   * @param integer $id 
   * @return type
   */
    public function pdfAir(Request $request,$id)
    {
        try{
            $id = obtenerRouteKey($id);
            $equipmentHides = '';
            $quote = QuoteV2::findOrFail($id);
            $rates_lcl_air = AutomaticRate::where('quote_id',$quote->id)->with('charge_lcl_air')->get();
            $sale_terms = SaleTermV2::where('quote_id',$quote->id)->with('charge')->select('port_id');
            $sale_terms_origin = SaleTermV2::where('quote_id',$quote->id)->where('type','Origin')->with('charge')->get();
            $sale_terms_destination = SaleTermV2::where('quote_id',$quote->id)->where('type','Destination')->with('charge')->get();
            $sale_terms_origin_grouped = SaleTermV2::where('quote_id',$quote->id)->where('type','Origin')->with('charge')->get();
            $sale_terms_destination_grouped = SaleTermV2::where('quote_id',$quote->id)->where('type','Destination')->with('charge')->get();

            if(\Auth::user()->company_user_id){
                $company_user=CompanyUser::find(\Auth::user()->company_user_id);
                $type=$company_user->type_pdf;
                $ammounts_type=$company_user->pdf_ammounts;
                $currency_cfg = Currency::find($company_user->currency_id);
            }

            foreach($sale_terms_origin_grouped as $sale_origin){
                foreach($sale_origin->charge as $sale_origin_charge){
                    if($sale_origin_charge->currency_id!=''){
                        if($quote->pdf_option->grouped_total_currency==1){
                            $typeCurrency =  $quote->pdf_option->total_in_currency;
                        }else{
                            $typeCurrency =  $currency_cfg->alphacode;
                        }

                        $currency_rate=$this->ratesCurrency($sale_origin_charge->currency_id,$typeCurrency);
                        $sale_origin_charge->total_sale_origin=number_format($sale_origin_charge->total/$currency_rate, 2, '.', '');
                    }
                }
            }

            foreach($sale_terms_destination_grouped as $sale_destination){
                foreach($sale_destination->charge as $sale_destination_charge){

                    if($sale_destination_charge->currency_id!=''){
                        if($quote->pdf_option->grouped_total_currency==1){
                            $typeCurrency =  $quote->pdf_option->total_in_currency;
                        }else{
                            $typeCurrency =  $currency_cfg->alphacode;
                        }
                        $currency_rate=$this->ratesCurrency($sale_destination_charge->currency_id,$typeCurrency);

                        $sale_destination_charge->total_sale_destination=number_format($sale_destination_charge->total/$currency_rate, 2, '.', '');
                    }
                }
            }

            $sale_terms_origin = collect($sale_terms_origin);

            $sale_terms_origin = $sale_terms_origin->groupBy([   
                function ($item) {
                    return $item['airport']['name'].', '.$item['airport']['code'];
                },     
            ], $preserveKeys = true);

            foreach($sale_terms_origin as $value){
                foreach($value as $origin_sale){
                    foreach($origin_sale->charge as $origin_charge){

                        if($origin_charge->currency_id!=''){
                            if($quote->pdf_option->grouped_origin_charges==1){
                                $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                            }else{
                                $typeCurrency =  $currency_cfg->alphacode;
                            }
                            $currency_rate=$this->ratesCurrency($origin_charge->currency_id,$typeCurrency);

                            $origin_charge->total_sale_origin=number_format($origin_charge->total/$currency_rate, 2, '.', '');
                        }
                    }
                }
            }

            $sale_terms_destination = collect($sale_terms_destination);

            $sale_terms_destination = $sale_terms_destination->groupBy([   
                function ($item) {
                    return $item['airport']['name'].', '.$item['airport']['code'];
                },     
            ], $preserveKeys = true);

            foreach($sale_terms_destination as $destination_sale){
                foreach($destination_sale as $value){
                    foreach($value->charge as $item){
                        if($item->currency_id!=''){
                            if($quote->pdf_option->grouped_destination_charges==1){
                                $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                            }else{
                                $typeCurrency =  $currency_cfg->alphacode;
                            }
                            $currency_rate=$this->ratesCurrency($item->currency_id,$typeCurrency);
                            $item->total_sale_destination=number_format($item->total/$currency_rate, 2, '.', '');
                        }
                    }
                }
            }

            /* Arrays de puertos incluidos en los Saleterms */

            $origin_airports = $this->getAirportsInArray($sale_terms_origin_grouped);

            $destination_airports = $this->getAirportsInArray($sale_terms_destination_grouped);

            /* Fin arrays */

            $freight_charges = AutomaticRate::whereHas('charge_lcl_air', function ($query) {
                $query->where('type_id', 3);
            })->where('quote_id',$quote->id)->get();

            $origin_charges = AutomaticRate::whereNotIn('origin_airport_id',$origin_airports)->where('quote_id',$quote->id)
                ->ChargeLclAir(1,'Origin')->get();

            $destination_charges = AutomaticRate::whereNotIn('destination_airport_id',$destination_airports)->where('quote_id',$quote->id)
                ->ChargeLclAir(2,'Destination')->get();

            $contact_email = Contact::find($quote->contact_id);
            $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
            $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
            $user = User::where('id',\Auth::id())->with('companyUser')->first();
            $package_loads = PackageLoadV2::where('quote_id',$quote->id)->get();
            if($quote->equipment!=''){
                $equipmentHides = $this->hideContainer($quote->equipment,'BD');
            }

            foreach ($rates_lcl_air as $item) {

                if($quote->pdf_option->grouped_total_currency==1){
                    $typeCurrency = $quote->pdf_option->total_in_currency;
                }else{
                    $typeCurrency =  $currency_cfg->alphacode;
                }

                $currency_rate=$this->ratesCurrency($item->currency_id,$typeCurrency);

                foreach ($item->charge_lcl_air as $value) {

                    $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);

                    if($value->type_id==3){
                        $value->total_freight=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');          
                    }elseif($value->type_id==1){
                        $value->total_origin=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                    }else{
                        $value->total_destination=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                    }
                }
                if(!$item->automaticInlandLclAir->isEmpty()){
                    foreach($item->automaticInlandLclAir as $inland){
                        if($quote->pdf_option->grouped_origin_charges==1){
                            $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                        }else{
                            $typeCurrency =  $currency_cfg->alphacode;
                        }
                        $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                        if($inland->units>0){
                            $inland->total_inland=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$currency_rate, 2, '.', '');
                        }
                    }
                }            
                foreach ($item->inland as $inland) {
                    $currency_charge = Currency::find($inland->currency_id);
                    $inland->currency_usd = $currency_charge->rates;
                    $inland->currency_eur = $currency_charge->rates_eur;
                }
            }

            $rates_lcl_air = $rates_lcl_air->map(function ($item, $key) use($origin_airports, $destination_airports, $sale_terms_origin_grouped, $sale_terms_destination_grouped){
                if(in_array($item->origin_airport_id,$origin_airports)){
                    if(!$item->charge->whereIn('type_id',1)->isEmpty()){
                        $item->charge_lcl_air->map(function ($value, $key) use($sale_terms_origin_grouped,$item){
                            if($value->type_id==1){
                                $value->total_origin=0;
                            }
                        });
                        //Añadimos los saleterms a la colección de Rates
                        $sale_terms_origin_grouped->map(function ($a) use($item) {
                            $a->charge->map(function ($x) use($item) {
                                $charge = new ChargeLclAir();
                                $charge->type_id = 1;
                                $charge->total_origin = $x->total_sale_origin;
                                $charge->currency_id = $x->currency_id;
                                $item->charge_lcl_air->push($charge);
                            });
                        });
                    }else{

                        //Añadimos los saleterms a la colección de Rates si esta vacío la relación con Charges
                        $sale_terms_origin_grouped->map(function ($a) use($item) {
                            $a->charge->map(function ($x) use($item) {
                                $charge = new ChargeLclAir();
                                $charge->type_id = 1;
                                $charge->total_origin = $x->total_sale_origin;
                                $charge->currency_id = $x->currency_id;
                                $item->charge_lcl_air->push($charge);
                            });
                        });
                    }
                }
                if(in_array($item->destination_airport_id,$destination_airports)){
                    $item->charge_lcl_air->map(function ($value, $key) use($sale_terms_destination_grouped,$item){
                        if($value->type_id==2){
                            $value->total_destination=0;
                        }
                    });
                    //Añadimos los saleterms a la colección de Rates
                    $sale_terms_destination_grouped->map(function ($a) use($item) {
                        $a->charge->map(function ($x) use($item) {
                            $charge = new ChargeLclAir();
                            $charge->type_id = 2;
                            $charge->total_destination = $x->total_sale_destination;
                            $charge->currency_id = $x->currency_id;
                            $item->charge_lcl_air->push($charge);
                        });
                    });
                }else{
                    //Añadimos los saleterms a la colección de Rates si esta vacío la relación con Charges
                    $sale_terms_destination_grouped->map(function ($a) use($item) {
                        $a->charge->map(function ($x) use($item) {
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

            $origin_charges_grouped = collect($origin_charges);

            $origin_charges_grouped = $origin_charges_grouped->groupBy([

                function ($item) {
                    return $item['origin_airport']['name'].', '.$item['origin_airport']['code'];
                },
                function ($item) {
                    return $item['airline']['name'];
                },      
                function ($item) {
                    return $item['destination_airport']['name'];
                },
            ], $preserveKeys = true);
            foreach($origin_charges_grouped as $origin=>$detail){
                foreach($detail as $item){
                    foreach($item as $v){
                        foreach($v as $rate){
                            foreach($rate->charge_lcl_air as $v_origin_g){

                                if($v_origin_g->type_id==1){
                                    if($quote->pdf_option->grouped_origin_charges==1){
                                        $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                    }else{
                                        $typeCurrency =  $currency_cfg->alphacode;
                                    }

                                    $currency_rate=$this->ratesCurrency($v_origin_g->currency_id,$typeCurrency);
                                    if($v_origin_g->units>0){
                                        $v_origin_g->rate=number_format((($v_origin_g->units*$v_origin_g->price_per_unit)+$v_origin_g->markup)/$value->units, 2, '.', '');
                                    }else{
                                        $v_origin_g->rate=0;
                                    }
                                    $v_origin_g->total_origin=number_format((($v_origin_g->units*$v_origin_g->price_per_unit)+$v_origin_g->markup)/$currency_rate, 2, '.', '');

                                }
                            }
                            if(!$rate->automaticInlandLclAir->isEmpty()){
                                foreach($rate->automaticInlandLclAir as $inland){
                                    if($inland->type=='Origin'){
                                        if($quote->pdf_option->grouped_origin_charges==1){
                                            $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                        }else{
                                            $typeCurrency =  $currency_cfg->alphacode;
                                        }
                                        $currency_rate=$this->ratesCurrency($inland->currency_id,$typeCurrency);
                                        if($inland->units>0){
                                            $inland->rate_amount=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$inland->units, 2, '.', '');
                                        }else{
                                            $inland->rate_amount=0;
                                        }
                                        $inland->total_inland_origin=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$currency_rate, 2, '.', '');

                                    }
                                }
                            }
                        }
                    }
                }
            }

            /*** DESTINATION CHARGES ***/

            $destination_charges_grouped = collect($destination_charges);

            $destination_charges_grouped = $destination_charges_grouped->groupBy([

                function ($item) {
                    return $item['destination_airport']['name'].', '.$item['destination_airport']['code'];
                },
                function ($item) {
                    return $item['airline']['name'];
                },
                function ($item) {
                    return $item['origin_port']['name'];
                },

            ], $preserveKeys = true);
            foreach($destination_charges_grouped as $origin=>$detail){
                foreach($detail as $item){
                    foreach($item as $v){
                        foreach($v as $rate){
                            foreach($rate->charge_lcl_air as $v_destination_g){

                                if($v_destination_g->type_id==2){

                                    if($quote->pdf_option->grouped_destination_charges==1){
                                        $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                                    }else{
                                        $typeCurrency =  $currency_cfg->alphacode;
                                    }
                                    $currency_rate=$this->ratesCurrency($v_destination_g->currency_id,$typeCurrency);
                                    if($v_destination_g->units>0){
                                        $v_destination_g->rate=number_format((($v_destination_g->units*$v_destination_g->price_per_unit)+$v_destination_g->markup)/$value->units, 2, '.', '');
                                    }else{
                                        $v_destination_g->rates=0;
                                    }
                                    $v_destination_g->total_destination=number_format((($v_destination_g->units*$v_destination_g->price_per_unit)+$v_destination_g->markup)/$currency_rate, 2, '.', '');
                                }
                            }
                            if(!$rate->automaticInlandLclAir->isEmpty()){
                                foreach($rate->automaticInlandLclAir as $inland){
                                    if($inland->type=='Destination'){
                                        if($quote->pdf_option->grouped_origin_charges==1){
                                            $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                        }else{
                                            $typeCurrency =  $currency_cfg->alphacode;
                                        }
                                        $currency_rate=$this->ratesCurrency($inland->currency_id,$typeCurrency);
                                        if($inland->units>0){
                                            $inland->rate_amount=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$inland->units, 2, '.', '');
                                        }else{
                                            $inland->rate_amount=0;
                                        }
                                        $inland->total_inland_origin=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$currency_rate, 2, '.', '');

                                    }
                                }
                            }
                        }
                    }
                }
            }    


            /** FREIGHT CHARGES **/

            $freight_charges_detailed = collect($freight_charges);

            $freight_charges_detailed = $freight_charges_detailed->groupBy([   
                function ($item) {
                    return $item['origin_airport']['name'].', '.$item['origin_airport']['code'];
                },
                function ($item) {
                    return $item['destination_airport']['name'].', '.$item['destination_airport']['code'];
                },
                function ($item) {
                    return $item['airline']['name'];
                },      
            ], $preserveKeys = true);

            foreach($freight_charges_detailed as $origin=>$item){
                foreach($item as $destination=>$items){
                    foreach($items as $carrier=>$itemsDetail){
                        foreach ($itemsDetail as $value) {     
                            foreach ($value->charge as $amounts) {
                                if($amounts->type_id==3){
                                    $sum_freight_20=0;
                                    $sum_freight_40=0;
                                    $sum_freight_40hc=0;
                                    $sum_freight_40nor=0;
                                    $sum_freight_45=0;
                                    $total_freight_40=0;
                                    $total_freight_20=0;
                                    $total_freight_40hc=0;
                                    $total_freight_40nor=0;
                                    $total_freight_45=0;
                                    //dd($quote->pdf_option->destination_charges_currency);
                                    if($quote->pdf_option->grouped_freight_charges==1){
                                        $typeCurrency =  $quote->pdf_option->freight_charges_currency;
                                    }else{
                                        $typeCurrency =  $currency_cfg->alphacode;
                                    }
                                    $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                                    $array_amounts = json_decode($amounts->amount,true);
                                    $array_markups = json_decode($amounts->markups,true);
                                    if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                        $sum_freight_20=$array_amounts['c20']+$array_markups['m20'];
                                        $total_freight_20=$sum_freight_20/$currency_rate;
                                    }
                                    if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                        $sum_freight_40=$array_amounts['c40']+$array_markups['m40'];
                                        $total_freight_40=$sum_freight_40/$currency_rate;
                                    }
                                    if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                        $sum_freight_40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                                        $total_freight_40hc=$sum_freight_40hc/$currency_rate;
                                    }
                                    if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                        $sum_freight_40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                                        $total_freight_40nor=$sum_freight_40nor/$currency_rate;
                                    }
                                    if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                        $sum_freight_45=$array_amounts['c45']+$array_markups['m45'];
                                        $total_freight_45=$sum_freight_45/$currency_rate;
                                    }            

                                    $amounts->total_20 = number_format($total_freight_20, 2, '.', '');
                                    $amounts->total_40 = number_format($total_freight_40, 2, '.', '');
                                    $amounts->total_40hc = number_format($total_freight_40hc, 2, '.', '');
                                    $amounts->total_40nor = number_format($total_freight_40nor, 2, '.', '');
                                    $amounts->total_45 = number_format($total_freight_45, 2, '.', '');
                                }
                            }
                        }
                    } 
                }
            }

            $freight_charges_grouped = collect($freight_charges);

            $freight_charges_grouped = $freight_charges_grouped->groupBy([

                function ($item) {
                    return $item['origin_airport']['name'].', '.$item['origin_airport']['code'];
                },
                function ($item) {
                    return $item['destination_airport']['name'].', '.$item['destination_airport']['code'];
                },
                function ($item) {
                    return $item['airline']['name'];
                },

            ], $preserveKeys = true);

            foreach($freight_charges_grouped as $freight){
                foreach($freight as $detail){
                    foreach($detail as $item){
                        foreach($item as $rate){
                            foreach ($rate->charge_lcl_air as $v_freight_g) {
                                if($v_freight_g->type_id==3){
                                    if($freight_charges_grouped->count()>1){
                                        $typeCurrency = $currency_cfg->alphacode;
                                    }else{
                                        if($quote->pdf_option->grouped_freight_charges==1){
                                            $typeCurrency = $quote->pdf_option->freight_charges_currency;
                                        }else{
                                            $typeCurrency = $currency_cfg->alphacode;
                                        }
                                    }
                                    $currency_rate=$this->ratesCurrency($v_freight_g->currency_id,$typeCurrency);
                                    if($v_freight_g->units>0){
                                        $v_freight_g->rate=number_format((($v_freight_g->units*$v_freight_g->price_per_unit)+$v_freight_g->markup)/$v_freight_g->units, 2, '.', '');
                                    }else{
                                        $v_freight_g->rate=0;
                                    }
                                    $v_freight_g->total_freight=number_format((($v_freight_g->units*$v_freight_g->price_per_unit)+$v_freight_g->markup)/$currency_rate, 2, '.', '');

                                }
                            }
                        }
                    }
                }
            }

            $view = \View::make('quotesv2.pdf.index_lcl_air', ['quote'=>$quote,'rates'=>$rates_lcl_air,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'user'=>$user,'currency_cfg'=>$currency_cfg,'charges_type'=>$type,'equipmentHides'=>$equipmentHides,'freight_charges_grouped'=>$freight_charges_grouped,'destination_charges'=>$destination_charges,'origin_charges_grouped'=>$origin_charges_grouped,'destination_charges_grouped'=>$destination_charges_grouped,'freight_charges_detailed'=>$freight_charges_detailed,'package_loads'=>$package_loads,'sale_terms_origin'=>$sale_terms_origin,'sale_terms_destination'=>$sale_terms_destination]);

            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->save('pdf/temp_'.$quote->id.'.pdf');

            return $pdf->stream('quote-'.$quote->quote_id.'-'.date('Ymd').'.pdf');
        } catch (Exception $e) {
            dd($e->getMessage());

            return false;

        }
    }

    //Actualiza opciones del PDF
    public function updatePdfFeature(Request $request){
        $name=$request->name;
        $pdf = PdfOption::where('quote_id',$request->id)->first();
        $pdf->$name=$request->value;
        $pdf->update();
        return response()->json(['message'=>'Ok']);
    }

    /**
   * Enviar cotizaciones vía email
   * @param Request $request 
   * @return Json
   */
  public function send_pdf_quote(Request $request)
  {

    $sign = null;
    $sign_type = null;
    $equipmentHides = null;

    $quote = QuoteV2::findOrFail($request->id);
    $rates = AutomaticRate::where('quote_id',$quote->id)->with('charge')->get();
    $origin_charges = AutomaticRate::whereHas('charge', function ($query) {
      $query->where('type_id', 1);
    })->where('quote_id',$quote->id)->get();
    $freight_charges = AutomaticRate::whereHas('charge', function ($query) {
      $query->where('type_id', 3);
    })->where('quote_id',$quote->id)->get();
    $destination_charges = AutomaticRate::whereHas('charge', function ($query) {
      $query->where('type_id', 2);
    })->where('quote_id',$quote->id)->get();
    $contact_email = Contact::find($quote->contact_id);
    $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
    $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
    $user = User::where('id',\Auth::id())->with('companyUser')->first();
    $email_from = \Auth::user()->email;

    if($quote->equipment!=''){
      $equipmentHides = $this->hideContainer($quote->equipment,'BD');
    }

    if(\Auth::user()->company_user_id){
      $company_user=CompanyUser::find(\Auth::user()->company_user_id);
      $currency_cfg = Currency::find($company_user->currency_id);
      $email_settings = EmailSetting::where('company_user_id',$company_user->id)->first();
      if($email_settings){
        if($email_settings->email_signature_type=='text'){
          $sign = $email_settings->email_signature_text;
          $sign_type = 'text';
        }else{
          $sign = $email_settings->email_signature_image;
          $sign_type = 'image';
        }
        if($email_settings->email_from!=''){
          $email_from = $email_settings->email_from;   
        }else{
          $email_from = \Auth::user()->email;
        }
      }
    }

    /* Sale terms */

        $sale_terms_origin = SaleTermV2::where('quote_id',$quote->id)->where('type','Origin')->with('charge')->get();
        $sale_terms_destination = SaleTermV2::where('quote_id',$quote->id)->where('type','Destination')->with('charge')->get();
        $sale_terms_origin_grouped = SaleTermV2::where('quote_id',$quote->id)->where('type','Origin')->with('charge')->get();
        $sale_terms_destination_grouped = SaleTermV2::where('quote_id',$quote->id)->where('type','Destination')->with('charge')->get();

        foreach($sale_terms_origin_grouped as $origin_sale){
            foreach($origin_sale->charge as $origin_charge){
                if($origin_charge->currency_id!=''){
                    if($quote->pdf_option->grouped_total_currency==1){
                        $typeCurrency =  $quote->pdf_option->total_in_currency;
                    }else{
                        $typeCurrency =  $currency_cfg->alphacode;
                    }
                    $currency_rate=$this->ratesCurrency($origin_charge->currency_id,$typeCurrency);
                    $origin_charge->sum20 += $origin_charge->c20/$currency_rate;
                    $origin_charge->sum40 += $origin_charge->c40/$currency_rate;
                    $origin_charge->sum40hc += $origin_charge->c40hc/$currency_rate;
                    $origin_charge->sum40nor += $origin_charge->c40nor/$currency_rate;
                    $origin_charge->sum45 += $origin_charge->c45/$currency_rate;
                }
            }
        }

        foreach($sale_terms_destination_grouped as $value){
            foreach($value->charge as $item){
                if($item->currency_id!=''){
                    if($quote->pdf_option->grouped_total_currency==1){
                        $typeCurrency =  $quote->pdf_option->total_in_currency;
                    }else{
                        $typeCurrency =  $currency_cfg->alphacode;
                    }
                    $currency_rate=$this->ratesCurrency($item->currency_id,$typeCurrency);
                    $item->sum20 += $item->c20/$currency_rate;
                    $item->sum40 += $item->c40/$currency_rate;
                    $item->sum40hc += $item->c40hc/$currency_rate;
                    $item->sum40nor += $item->c40nor/$currency_rate;
                    $item->sum45 += $item->c45/$currency_rate;
                }
            }
        }

        $sale_terms_origin = collect($sale_terms_origin);

        $sale_terms_origin = $sale_terms_origin->groupBy([   
            function ($item) {
                return $item['port']['name'].', '.$item['port']['code'];
            },     
        ], $preserveKeys = true);

        foreach($sale_terms_origin as $value){
            foreach($value as $origin_sale){
                foreach($origin_sale->charge as $origin_charge){

                    if($origin_charge->currency_id!=''){
                        if($quote->pdf_option->grouped_origin_charges==1){
                            $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                        }else{
                            $typeCurrency =  $currency_cfg->alphacode;
                        }
                        $currency_rate=$this->ratesCurrency($origin_charge->currency_id,$typeCurrency);
                        $origin_charge->sum20 += $origin_charge->c20/$currency_rate;
                        $origin_charge->sum40 += $origin_charge->c40/$currency_rate;
                        $origin_charge->sum40hc += $origin_charge->c40hc/$currency_rate;
                        $origin_charge->sum40nor += $origin_charge->c40nor/$currency_rate;
                        $origin_charge->sum45 += $origin_charge->c45/$currency_rate;
                    }
                }
            }
        }

        $sale_terms_destination = collect($sale_terms_destination);

        $sale_terms_destination = $sale_terms_destination->groupBy([   
            function ($item) {
                return $item['port']['name'].', '.$item['port']['code'];
            },     
        ], $preserveKeys = true);

        foreach($sale_terms_destination as $destination_sale){
            foreach($destination_sale as $value){
                foreach($value->charge as $item){
                    if($item->currency_id!=''){
                        if($quote->pdf_option->grouped_destination_charges==1){
                            $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                        }else{
                            $typeCurrency =  $currency_cfg->alphacode;
                        }
                        $currency_rate=$this->ratesCurrency($item->currency_id,$typeCurrency);
                        $item->sum20 += $item->c20/$currency_rate;
                        $item->sum40 += $item->c40/$currency_rate;
                        $item->sum40hc += $item->c40hc/$currency_rate;
                        $item->sum40nor += $item->c40nor/$currency_rate;
                        $item->sum45 += $item->c45/$currency_rate;
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

        $origin_charges = AutomaticRate::whereNotIn('origin_port_id',$origin_ports)->where('quote_id',$quote->id)
            ->Charge(1,'Origin')->with('charge')->get();

        $destination_charges = AutomaticRate::whereNotIn('destination_port_id',$destination_ports)->where('quote_id',$quote->id)
            ->Charge(2,'Destination')->with('charge')->get();

        $freight_charges = AutomaticRate::whereHas('charge', function ($query) {
            $query->where('type_id', 3);
        })->with('charge')->where('quote_id',$quote->id)->get();

        /* Fin consulta de charges */

        /** Rates **/

        $rates = $this->processGlobalRates($rates, $quote, $currency_cfg);

        /* Se manipula la colección de rates para añadir los valores de saleterms */
        $rates = $this->addSaleTermToRate($rates, $origin_ports, $destination_ports, $sale_terms_origin_grouped, $sale_terms_destination_grouped);

        /** Origin Charges **/

        $origin_charges_grouped=$this->processOriginGrouped($origin_charges, $quote, $currency_cfg);

        $origin_charges_detailed=$this->processOriginDetailed($origin_charges, $quote, $currency_cfg);

        /** Destination Charges **/

        $destination_charges_grouped=$this->processDestinationGrouped($destination_charges, $quote, $currency_cfg);

        $destination_charges=$this->processDestinationDetailed($destination_charges, $quote, $currency_cfg);

        /** Freight Charges **/

        $freight_charges_grouped = $this->processFreightCharges($freight_charges, $quote, $currency_cfg);

    $view = \View::make('quotesv2.pdf.index', ['quote'=>$quote,'rates'=>$rates,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'user'=>$user,'currency_cfg'=>$currency_cfg,'equipmentHides'=>$equipmentHides,'freight_charges_grouped'=>$freight_charges_grouped,'destination_charges'=>$destination_charges,'origin_charges_grouped'=>$origin_charges_grouped,'origin_charges_detailed'=>$origin_charges_detailed,'destination_charges_grouped'=>$destination_charges_grouped,'sale_terms_origin'=>$sale_terms_origin,'sale_terms_destination'=>$sale_terms_destination,'sale_terms_origin_grouped'=>$sale_terms_origin_grouped,'sale_terms_destination_grouped'=>$sale_terms_destination_grouped]);

    // EVENTO INTERCOM 
    //$event = new  EventIntercom();
    //$event->event_quoteEmail();

    $pdf = \App::make('dompdf.wrapper');
    $pdf->loadHTML($view)->save('pdf/temp_'.$quote->id.'.pdf');

    $subject = $request->subject;
    $body = $request->body;
    $to = $request->to;

    $this->saveEmailNotification($to, $email_from, $subject, $body, $quote, $sign_type, $sign, $contact_email);
    
    //SendQuotes::dispatch($subject,$body,$to,$quote,$contact_email->email);

    $quote->status='Sent';
    $quote->update();

    return response()->json(['message' => 'Ok']);
  }

  /**
   * Enviar cotizaciones vía email
   * @param Request $request 
   * @return Json
   */
  public function send_pdf_quote_lcl_air(Request $request)
  {
    $sign = null;
    $sign_type = null;
    $equipmentHides = null;

    $quote = QuoteV2::findOrFail($request->id);
    $rates_lcl_air = AutomaticRate::where('quote_id',$quote->id)->with('charge')->get();
    $origin_charges = AutomaticRate::whereHas('charge', function ($query) {
      $query->where('type_id', 1);
    })->where('quote_id',$quote->id)->get();
    $freight_charges = AutomaticRate::whereHas('charge', function ($query) {
      $query->where('type_id', 3);
    })->where('quote_id',$quote->id)->get();
    $destination_charges = AutomaticRate::whereHas('charge', function ($query) {
      $query->where('type_id', 2);
    })->where('quote_id',$quote->id)->get();
    $contact_email = Contact::find($quote->contact_id);
    $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
    $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
    $user = User::where('id',\Auth::id())->with('companyUser')->first();
    $package_loads = PackageLoadV2::where('quote_id',$quote->id)->get();
    $email_from = \Auth::user()->email;
    $sale_terms = SaleTermV2::where('quote_id',$quote->id)->with('charge')->select('port_id');
    $sale_terms_origin = SaleTermV2::where('quote_id',$quote->id)->where('type','Origin')->with('charge')->get();
    $sale_terms_destination = SaleTermV2::where('quote_id',$quote->id)->where('type','Destination')->with('charge')->get();
    $sale_terms_origin_grouped = SaleTermV2::where('quote_id',$quote->id)->where('type','Origin')->with('charge')->get();
    $sale_terms_destination_grouped = SaleTermV2::where('quote_id',$quote->id)->where('type','Destination')->with('charge')->get();

    if($quote->equipment!=''){
      $equipmentHides = $this->hideContainer($quote->equipment,'BD');
    }

    if(\Auth::user()->company_user_id){
      $company_user=CompanyUser::find(\Auth::user()->company_user_id);
      $email_settings = EmailSetting::where('company_user_id',$company_user->id)->first();
      if($email_settings){
        if($email_settings->email_signature_type=='text'){
          $sign = $email_settings->email_signature_text;
        }else{
          $sign = $email_settings->email_signature_image;
        }
        if($email_settings->email_from!=''){
          $email_from = $email_settings->email_from;   
        }else{
          $email_from = \Auth::user()->email;
        }
      }
      $type=$company_user->type_pdf;
      $ammounts_type=$company_user->pdf_ammounts;
      $currency_cfg = Currency::find($company_user->currency_id);
    }

        foreach($sale_terms_origin_grouped as $sale_origin){
            foreach($sale_origin->charge as $sale_origin_charge){
                if($sale_origin_charge->currency_id!=''){
                    if($quote->pdf_option->grouped_total_currency==1){
                        $typeCurrency =  $quote->pdf_option->total_in_currency;
                    }else{
                        $typeCurrency =  $currency_cfg->alphacode;
                    }

                    $currency_rate=$this->ratesCurrency($sale_origin_charge->currency_id,$typeCurrency);
                    $sale_origin_charge->total_sale_origin=number_format($sale_origin_charge->total/$currency_rate, 2, '.', '');
                }
            }
        }

        foreach($sale_terms_destination_grouped as $sale_destination){
            foreach($sale_destination->charge as $sale_destination_charge){

                if($sale_destination_charge->currency_id!=''){
                    if($quote->pdf_option->grouped_total_currency==1){
                        $typeCurrency =  $quote->pdf_option->total_in_currency;
                    }else{
                        $typeCurrency =  $currency_cfg->alphacode;
                    }
                    $currency_rate=$this->ratesCurrency($sale_destination_charge->currency_id,$typeCurrency);

                    $sale_destination_charge->total_sale_destination=number_format($sale_destination_charge->total/$currency_rate, 2, '.', '');
                }
            }
        }

        $sale_terms_origin = collect($sale_terms_origin);

        $sale_terms_origin = $sale_terms_origin->groupBy([   
            function ($item) {
                return $item['port']['name'].', '.$item['port']['code'];
            },     
        ], $preserveKeys = true);

        foreach($sale_terms_origin as $value){
            foreach($value as $origin_sale){
                foreach($origin_sale->charge as $origin_charge){

                    if($origin_charge->currency_id!=''){
                        if($quote->pdf_option->grouped_origin_charges==1){
                            $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                        }else{
                            $typeCurrency =  $currency_cfg->alphacode;
                        }
                        $currency_rate=$this->ratesCurrency($origin_charge->currency_id,$typeCurrency);

                        $origin_charge->total_sale_origin=number_format($origin_charge->total/$currency_rate, 2, '.', '');
                    }
                }
            }
        }

        $sale_terms_destination = collect($sale_terms_destination);

        $sale_terms_destination = $sale_terms_destination->groupBy([   
            function ($item) {
                return $item['port']['name'].', '.$item['port']['code'];
            },     
        ], $preserveKeys = true);

        foreach($sale_terms_destination as $destination_sale){
            foreach($destination_sale as $value){
                foreach($value->charge as $item){
                    if($item->currency_id!=''){
                        if($quote->pdf_option->grouped_destination_charges==1){
                            $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                        }else{
                            $typeCurrency =  $currency_cfg->alphacode;
                        }
                        $currency_rate=$this->ratesCurrency($item->currency_id,$typeCurrency);
                        $item->total_sale_destination=number_format($item->total/$currency_rate, 2, '.', '');
                    }
                }
            }
        }

        /* Arrays de puertos incluidos en los Saleterms */

        $origin_ports = $this->getPortsInArray($sale_terms_origin_grouped);

        $destination_ports = $this->getPortsInArray($sale_terms_destination_grouped);

        /* Fin arrays */

        $freight_charges = AutomaticRate::whereHas('charge_lcl_air', function ($query) {
            $query->where('type_id', 3);
        })->where('quote_id',$quote->id)->get();

        $origin_charges = AutomaticRate::whereNotIn('destination_port_id',$origin_ports)->where('quote_id',$quote->id)
            ->ChargeLclAir(1,'Origin')->get();

        $destination_charges = AutomaticRate::whereNotIn('destination_port_id',$destination_ports)->where('quote_id',$quote->id)
            ->ChargeLclAir(2,'Destination')->get();

        foreach ($rates_lcl_air as $item) {

            foreach ($item->charge_lcl_air as $value) {

                if($quote->pdf_option->grouped_total_currency==1){
                    $typeCurrency = $quote->pdf_option->total_in_currency;
                }else{
                    $typeCurrency =  $currency_cfg->alphacode;
                }

                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);

                if($value->type_id==3){
                    if($value->units>0){
                        $value->total_freight=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');

                    }
                }elseif($value->type_id==1){
                    if($value->units>0){
                        $value->total_origin=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');

                    }
                }else{
                    if($value->units>0){
                        $value->total_destination=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');
                    }
                }
            }
            if(!$item->automaticInlandLclAir->isEmpty()){
                foreach($item->automaticInlandLclAir as $inland){
                    if($quote->pdf_option->grouped_origin_charges==1){
                        $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                    }else{
                        $typeCurrency =  $currency_cfg->alphacode;
                    }
                    $currency_rate=$this->ratesCurrency($inland->currency_id,$typeCurrency);
                    if($inland->units>0){
                        $inland->total_inland=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$currency_rate, 2, '.', '');
                    }
                }
            }
            foreach ($item->inland as $inland) {
                $currency_charge = Currency::find($inland->currency_id);
                $inland->currency_usd = $currency_charge->rates;
                $inland->currency_eur = $currency_charge->rates_eur;
            }


        }

        $rates_lcl_air = $this->addSaleTermToRateLcl($rates_lcl_air, $origin_ports, $destination_ports,$sale_terms_origin_grouped, $sale_terms_destination_grouped);

        $origin_charges_grouped = collect($origin_charges);

        $origin_charges_grouped = $origin_charges_grouped->groupBy([

            function ($item) {
                return $item['origin_port']['name'].', '.$item['origin_port']['code'];
            },
            function ($item) {
                return $item['carrier']['name'];
            },      
            function ($item) {
                return $item['destination_port']['name'];
            },
        ], $preserveKeys = true);

        foreach($origin_charges_grouped as $origin=>$detail){
            foreach($detail as $item){
                foreach($item as $v){
                    foreach($v as $rate){
                        foreach($rate->charge_lcl_air as $v_origin){

                            if($v_origin->type_id==1){
                                if($quote->pdf_option->grouped_origin_charges==1){
                                    $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }

                                $currency_rate=$this->ratesCurrency($v_origin->currency_id,$typeCurrency);
                                if($v_origin->units>0){
                                    $v_origin->rate=number_format((($v_origin->units*$v_origin->price_per_unit)+$v_origin->markup)/$v_origin->units, 2, '.', '');
                                }else{
                                    $v_origin->rate=0;
                                }
                                $v_origin->total_origin=number_format((($v_origin->units*$v_origin->price_per_unit)+$v_origin->markup)/$currency_rate, 2, '.', '');
                            }
                        }

                        if(!$rate->automaticInlandLclAir->isEmpty()){
                            foreach($rate->automaticInlandLclAir as $inland){
                                if($inland->type=='Origin'){
                                    if($quote->pdf_option->grouped_origin_charges==1){
                                        $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                    }else{
                                        $typeCurrency =  $currency_cfg->alphacode;
                                    }
                                    $currency_rate=$this->ratesCurrency($inland->currency_id,$typeCurrency);
                                    if($inland->units>0){
                                        $inland->rate_amount=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$inland->units, 2, '.', '');
                                    }else{
                                        $inland->rate_amount=0;
                                    }
                                    $inland->total_inland_origin=number_format((($inland->units*$inland->price_per_unit)+$inland->markup)/$currency_rate, 2, '.', '');

                                }
                            }
                        }
                    }
                }
            }
        }

        /*** DESTINATION CHARGES ***/

        $destination_charges_grouped = collect($destination_charges);

        $destination_charges_grouped = $destination_charges_grouped->groupBy([

            function ($item) {
                return $item['destination_port']['name'].', '.$item['destination_port']['code'];
            },
            function ($item) {
                return $item['carrier']['name'];
            },
            function ($item) {
                return $item['origin_port']['name'];
            },

        ], $preserveKeys = true);
        foreach($destination_charges_grouped as $origin=>$detail){
            foreach($detail as $item){
                foreach($item as $v){
                    foreach($v as $rate){
                        foreach($rate->charge_lcl_air as $v_destination){

                            if($v_destination->type_id==2){

                                if($quote->pdf_option->grouped_destination_charges==1){
                                    $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }
                                $currency_rate=$this->ratesCurrency($v_destination->currency_id,$typeCurrency);
                                if($v_destination->units>0){
                                    $v_destination->rate=number_format((($v_destination->units*$v_destination->price_per_unit)+$v_destination->markup)/$v_destination->units, 2, '.', '');
                                }else{
                                    $v_destination->rate=0;
                                }
                                $v_destination->total_destination=number_format((($v_destination->units*$v_destination->price_per_unit)+$v_destination->markup)/$currency_rate, 2, '.', '');
                            }
                        }
                        if(!$rate->automaticInlandLclAir->isEmpty()){
                            foreach($rate->automaticInlandLclAir as $v_destination_inland){
                                if($v_destination_inland->type=='Destination'){
                                    if($quote->pdf_option->grouped_origin_charges==1){
                                        $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                                    }else{
                                        $typeCurrency =  $currency_cfg->alphacode;
                                    }
                                    $currency_rate=$this->ratesCurrency($v_destination_inland->currency_id,$typeCurrency);
                                    if($v_destination_inland->units>0){
                                        $v_destination_inland->rate_amount=number_format((($v_destination_inland->units*$v_destination_inland->price_per_unit)+$v_destination_inland->markup)/$v_destination_inland->units, 2, '.', '');
                                    }else{
                                        $v_destination_inland->rate_amount=0;
                                    }
                                    $v_destination_inland->total_inland_destination=number_format((($v_destination_inland->units*$v_destination_inland->price_per_unit)+$v_destination_inland->markup)/$currency_rate, 2, '.', '');
                                }
                            }
                        }
                    }
                }
            }
        }

        /** FREIGHT CHARGES **/

        $freight_charges_detailed = collect($freight_charges);

        $freight_charges_detailed = $freight_charges_detailed->groupBy([   
            function ($item) {
                return $item['origin_port']['name'].', '.$item['origin_port']['code'];
            },
            function ($item) {
                return $item['destination_port']['name'].', '.$item['destination_port']['code'];
            },
            function ($item) {
                return $item['carrier']['name'];
            },      
        ], $preserveKeys = true);

        foreach($freight_charges_detailed as $origin=>$item){
            foreach($item as $destination=>$items){
                foreach($items as $carrier=>$itemsDetail){
                    foreach ($itemsDetail as $value) {     
                        foreach ($value->charge as $amounts) {
                            if($amounts->type_id==3){
                                $sum_freight_20=0;
                                $sum_freight_40=0;
                                $sum_freight_40hc=0;
                                $sum_freight_40nor=0;
                                $sum_freight_45=0;
                                $total_freight_40=0;
                                $total_freight_20=0;
                                $total_freight_40hc=0;
                                $total_freight_40nor=0;
                                $total_freight_45=0;
                                //dd($quote->pdf_option->destination_charges_currency);
                                if($quote->pdf_option->grouped_freight_charges==1){
                                    $typeCurrency =  $quote->pdf_option->freight_charges_currency;
                                }else{
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }
                                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                                $array_amounts = json_decode($amounts->amount,true);
                                $array_markups = json_decode($amounts->markups,true);
                                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                                    $sum_freight_20=$array_amounts['c20']+$array_markups['m20'];
                                    $total_freight_20=$sum_freight_20/$currency_rate;
                                }
                                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                                    $sum_freight_40=$array_amounts['c40']+$array_markups['m40'];
                                    $total_freight_40=$sum_freight_40/$currency_rate;
                                }
                                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                                    $sum_freight_40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                                    $total_freight_40hc=$sum_freight_40hc/$currency_rate;
                                }
                                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                                    $sum_freight_40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                                    $total_freight_40nor=$sum_freight_40nor/$currency_rate;
                                }
                                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                                    $sum_freight_45=$array_amounts['c45']+$array_markups['m45'];
                                    $total_freight_45=$sum_freight_45/$currency_rate;
                                }            

                                $amounts->total_20 = number_format($total_freight_20, 2, '.', '');
                                $amounts->total_40 = number_format($total_freight_40, 2, '.', '');
                                $amounts->total_40hc = number_format($total_freight_40hc, 2, '.', '');
                                $amounts->total_40nor = number_format($total_freight_40nor, 2, '.', '');
                                $amounts->total_45 = number_format($total_freight_45, 2, '.', '');
                            }
                        }
                    }
                } 
            }
        }

        $freight_charges_grouped = collect($freight_charges);

        $freight_charges_grouped = $freight_charges_grouped->groupBy([

            function ($item) {
                return $item['origin_port']['name'].', '.$item['origin_port']['code'];
            },
            function ($item) {
                return $item['destination_port']['name'].', '.$item['destination_port']['code'];
            },
            function ($item) {
                return $item['carrier']['name'];
            },

        ], $preserveKeys = true);

        foreach($freight_charges_grouped as $freight){
            foreach($freight as $detail){
                foreach($detail as $item){
                    foreach($item as $rate){
                        foreach ($rate->charge_lcl_air as $v_freight) {
                            if($v_freight->type_id==3){
                                if($freight_charges_grouped->count()>1){
                                    $typeCurrency = $currency_cfg->alphacode;
                                }else{
                                    if($quote->pdf_option->grouped_freight_charges==1){
                                        $typeCurrency = $quote->pdf_option->freight_charges_currency;
                                    }else{
                                        $typeCurrency = $currency_cfg->alphacode;
                                    }
                                }
                                $currency_rate=$this->ratesCurrency($v_freight->currency_id,$typeCurrency);

                                //$value->price_per_unit=number_format(($value->price_per_unit/$currency_rate), 2, '.', '');
                                //$value->markup=number_format(($value->markup/$currency_rate), 2, '.', '');
                                if($v_freight->units>0){
                                    $v_freight->rate=number_format((($v_freight->units*$v_freight->price_per_unit)+$v_freight->markup)/$v_freight->units, 2, '.', '');
                                }else{
                                    $v_freight->rate=0;
                                }
                                $v_freight->total_freight=number_format((($v_freight->units*$v_freight->price_per_unit)+$v_freight->markup)/$currency_rate, 2, '.', '');

                            }
                        }
                    }
                }
            }
        }

        $view = \View::make('quotesv2.pdf.index_lcl_air', ['quote'=>$quote,'rates'=>$rates_lcl_air,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'user'=>$user,'currency_cfg'=>$currency_cfg,'charges_type'=>$type,'equipmentHides'=>$equipmentHides,'freight_charges_grouped'=>$freight_charges_grouped,'destination_charges'=>$destination_charges,'origin_charges_grouped'=>$origin_charges_grouped,'destination_charges_grouped'=>$destination_charges_grouped,'freight_charges_detailed'=>$freight_charges_detailed,'package_loads'=>$package_loads,'sale_terms_origin'=>$sale_terms_origin,'sale_terms_destination'=>$sale_terms_destination,'sale_terms_origin_grouped'=>$sale_terms_origin_grouped,'sale_terms_destination_grouped'=>$sale_terms_destination_grouped]);

    // EVENTO INTERCOM 
    //$event = new  EventIntercom();
    //$event->event_quoteEmail();

    $pdf = \App::make('dompdf.wrapper');
    $pdf->loadHTML($view)->save('pdf/temp_'.$quote->id.'.pdf');

    $subject = $request->subject;
    $body = $request->body;
    $to = $request->to;

    $this->saveEmailNotification($to, $email_from, $subject, $body, $quote, $sign_type, $sign, $contact_email);
    //SendQuotes::dispatch($subject,$body,$to,$quote,$contact_email->email);

    $quote->status='Sent';
    $quote->update();
    return response()->json(['message' => 'Ok']);
  }

}
