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
use App\SaleTermV2;
use App\SaleTermV2Charge;
use App\CalculationType;
use App\CalculationTypeLcl;
use App\Company;
use App\CompanyUser;
use App\Contact;
use App\Country;
use App\EmailTemplate;
use App\Harbor;
use App\Incoterm;
use App\Price;
use App\Inland;
use App\Quote;
use App\Carrier;
use App\User;
use App\PdfOption;

use App\Surcharge;
use Illuminate\Support\Collection as Collection;

trait QuoteV2Trait {

  public function processGlobalRates($rates, $quote, $currency_cfg){
    foreach ($rates as $item) {
      $sum_rate_20=0;
      $sum_rate_40=0;
      $sum_rate_40hc=0;
      $sum_rate_40nor=0;
      $sum_rate_45=0;

      $total_markup_rate_20=0;
      $total_markup_rate_40=0;
      $total_markup_rate_40hc=0;
      $total_markup_rate_40nor=0;
      $total_markup_rate_45=0;

      $total_lcl_air_freight=0;
      $total_lcl_air_origin=0;
      $total_lcl_air_destination=0;

      $currency = Currency::find($item->currency_id);
      $item->currency_usd = $currency->rates;
      $item->currency_eur = $currency->rates_eur;

      //Charges
      foreach ($item->charge as $value) {

        if($quote->pdf_option->grouped_total_currency==1){
          $typeCurrency =  $quote->pdf_option->total_in_currency;
        }else{
          $typeCurrency =  $currency_cfg->alphacode;
        }
        $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);

        $array_amounts = json_decode($value->amount,true);
        $array_markups = json_decode($value->markups,true);

        if(isset($array_amounts['c20'])){
          $amount20=$array_amounts['c20'];
          $total20=$amount20/$currency_rate;
          $sum20 = number_format($total20, 2, '.', '');
          $value->total_20=number_format($sum20, 2, '.', '');
        }

        if(isset($array_markups['m20'])){
          $markup20=$array_markups['m20'];
          $total_markup20=$markup20/$currency_rate;
          $value->total_markup20=number_format($total_markup20, 2, '.', '');
        }

        if(isset($array_amounts['c40'])){
          $amount40=$array_amounts['c40'];
          $total40=$amount40/$currency_rate;          
          $sum40 = number_format($total40, 2, '.', '');
          $value->total_40=number_format($sum40, 2, '.', '');
        }

        if(isset($array_markups['m40'])){
          $markup40=$array_markups['m40'];
          $total_markup40=$markup40/$currency_rate;
          $value->total_markup40=number_format($total_markup40, 2, '.', '');
        }

        if(isset($array_amounts['c40hc'])){
          $amount40hc=$array_amounts['c40hc'];
          $total40hc=$amount40hc/$currency_rate;          
          $sum40hc = number_format($total40hc, 2, '.', '');
          $value->total_40hc=number_format($sum40hc, 2, '.', '');
        }

        if(isset($array_markups['m40hc'])){
          $markup40hc=$array_markups['m40hc'];
          $total_markup40hc=$markup40hc/$currency_rate;
          $value->total_markup40hc=number_format($total_markup40hc, 2, '.', '');
        }

        if(isset($array_amounts['c40nor'])){
          $amount40nor=$array_amounts['c40nor'];
          $total40nor=$amount40nor/$currency_rate;
          $sum40nor = number_format($total40nor, 2, '.', '');
          $value->total_40nor=number_format($sum40nor, 2, '.', '');
        }

        if(isset($array_markups['m40nor'])){
          $markup40nor=$array_markups['m40nor'];
          $total_markup40nor=$markup40nor/$currency_rate;
          $value->total_markup40nor=number_format($total_markup40nor, 2, '.', '');
        }

        if(isset($array_amounts['c45'])){
          $amount45=$array_amounts['c45'];
          $total45=$amount45/$currency_rate;
          $sum45 = number_format($total45, 2, '.', '');
          $value->total_45=number_format($sum45, 2, '.', '');
        }

        if(isset($array_markups['m45'])){
          $markup45=$array_markups['m45'];
          $total_markup45=$markup45/$currency_rate;
          $value->total_markup45=number_format($total_markup45, 2, '.', '');
        }

        $currency_charge = Currency::find($value->currency_id);
        $value->currency_usd = $currency_charge->rates;
        $value->currency_eur = $currency_charge->rates_eur;
      }

      //Inland
      foreach ($item->inland as $inland) {
        $sum_inland20 = 0;
        $sum_inland40 = 0;
        $sum_inland40hc = 0;
        $sum_inland40nor = 0;
        $sum_inland45 = 0;

        $total_inland_markup20 = 0;
        $total_inland_markup40 = 0;
        $total_inland_markup40hc = 0;
        $total_inland_markup40nor = 0;
        $total_inland_markup45 = 0;
        if($quote->pdf_option->grouped_total_currency==1){
          $typeCurrency =  $quote->pdf_option->total_in_currency;
        }else{
          $typeCurrency =  $currency_cfg->alphacode;
        }
        $currency_rate=$this->ratesCurrency($inland->currency_id,$typeCurrency);
        $array_amounts = json_decode($inland->rate,true);
        $array_markups = json_decode($inland->markup,true);
        if(isset($array_amounts['c20'])){
          $amount_inland20=$array_amounts['c20'];
          $total_inland20=$amount_inland20/$currency_rate;
          $sum_inland20 = number_format($total_inland20, 2, '.', '');
        }

        if(isset($array_markups['m20'])){
          $markup_inland20=$array_markups['m20'];
          $total_inland_markup20=$markup_inland20/$currency_rate;
        }

        if(isset($array_amounts['c40'])){
          $amount_inland40=$array_amounts['c40'];
          $total_inland40=$amount_inland40/$currency_rate;          
          $sum_inland40 = number_format($total_inland40, 2, '.', '');
        }

        if(isset($array_markups['m40'])){
          $markup_inland40=$array_markups['m40'];
          $total_inland_markup40=$markup_inland40/$currency_rate;
        }

        if(isset($array_amounts['c40hc'])){
          $amount_inland40hc=$array_amounts['c40hc'];
          $total_inland40hc=$amount_inland40hc/$currency_rate;          
          $sum_inland40hc = number_format($total_inland40hc, 2, '.', '');
        }

        if(isset($array_markups['m40hc'])){
          $markup_inland40hc=$array_markups['m40hc'];
          $total_inland_markup40hc=$markup_inland40hc/$currency_rate;
        }

        if(isset($array_amounts['c40nor'])){
          $amount_inland40nor=$array_amounts['c40nor'];
          $total_inland40nor=$amount_inland40nor/$currency_rate;
          $sum_inland40nor = number_format($total_inland40nor, 2, '.', '');
        }

        if(isset($array_markups['m40nor'])){
          $markup_inland40nor=$array_markups['m40nor'];
          $total_inland_markup40nor=$markup_inland40nor/$currency_rate;
        }

        if(isset($array_amounts['c45'])){
          $amount_inland45=$array_amounts['c45'];
          $total_inland45=$amount_inland45/$currency_rate;
          $sum_inland45 = number_format($total_inland45, 2, '.', '');
        }

        if(isset($array_markups['m45'])){
          $markup_inland45=$array_markups['m45'];
          $total_inland_markup45=$markup_inland45/$currency_rate;
        }

        $inland->total_20=number_format($sum_inland20, 2, '.', '');
        $inland->total_40=number_format($sum_inland40, 2, '.', '');
        $inland->total_40hc=number_format($sum_inland40hc, 2, '.', '');
        $inland->total_40nor=number_format($sum_inland40nor, 2, '.', '');
        $inland->total_45=number_format($sum_inland45, 2, '.', '');

        $inland->total_m20=number_format($total_inland_markup20, 2, '.', '');
        $inland->total_m40=number_format($total_inland_markup40, 2, '.', '');
        $inland->total_m40hc=number_format($total_inland_markup40hc, 2, '.', '');
        $inland->total_m40nor=number_format($total_inland_markup40nor, 2, '.', '');
        $inland->total_m45=number_format($total_inland_markup45, 2, '.', '');

        $currency_charge = Currency::find($inland->currency_id);
        $inland->currency_usd = $currency_charge->rates;
        $inland->currency_eur = $currency_charge->rates_eur;
      }
    }

    return $rates;
  }

  public function processGlobalRatesWithSales($rates, $quote, $currency_cfg, $origin_ports, $destination_ports){

    foreach ($rates as $item) {
      $sum_rate_20=0;
      $sum_rate_40=0;
      $sum_rate_40hc=0;
      $sum_rate_40nor=0;
      $sum_rate_45=0;

      $total_markup_rate_20=0;
      $total_markup_rate_40=0;
      $total_markup_rate_40hc=0;
      $total_markup_rate_40nor=0;
      $total_markup_rate_45=0;

      $total_lcl_air_freight=0;
      $total_lcl_air_origin=0;
      $total_lcl_air_destination=0;

      $currency = Currency::find($item->currency_id);
      $item->currency_usd = $currency->rates;
      $item->currency_eur = $currency->rates_eur;

      //Charges
      foreach ($item->charge as $value) {

        if($quote->pdf_option->grouped_total_currency==1){
          $typeCurrency =  $quote->pdf_option->total_in_currency;
        }else{
          $typeCurrency =  $currency_cfg->alphacode;
        }
        $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);

        $array_amounts = json_decode($value->amount,true);
        $array_markups = json_decode($value->markups,true);

        if(isset($array_amounts['c20'])){
          $amount20=$array_amounts['c20'];
          $total20=$amount20/$currency_rate;
          $sum20 = number_format($total20, 2, '.', '');
          $value->total_20=number_format($sum20, 2, '.', '');
        }

        if(isset($array_markups['m20'])){
          $markup20=$array_markups['m20'];
          $total_markup20=$markup20/$currency_rate;
          $value->total_markup20=number_format($total_markup20, 2, '.', '');
        }

        if(isset($array_amounts['c40'])){
          $amount40=$array_amounts['c40'];
          $total40=$amount40/$currency_rate;          
          $sum40 = number_format($total40, 2, '.', '');
          $value->total_40=number_format($sum40, 2, '.', '');
        }

        if(isset($array_markups['m40'])){
          $markup40=$array_markups['m40'];
          $total_markup40=$markup40/$currency_rate;
          $value->total_markup40=number_format($total_markup40, 2, '.', '');
        }

        if(isset($array_amounts['c40hc'])){
          $amount40hc=$array_amounts['c40hc'];
          $total40hc=$amount40hc/$currency_rate;          
          $sum40hc = number_format($total40hc, 2, '.', '');
          $value->total_40hc=number_format($sum40hc, 2, '.', '');
        }

        if(isset($array_markups['m40hc'])){
          $markup40hc=$array_markups['m40hc'];
          $total_markup40hc=$markup40hc/$currency_rate;
          $value->total_markup40hc=number_format($total_markup40hc, 2, '.', '');
        }

        if(isset($array_amounts['c40nor'])){
          $amount40nor=$array_amounts['c40nor'];
          $total40nor=$amount40nor/$currency_rate;
          $sum40nor = number_format($total40nor, 2, '.', '');
          $value->total_40nor=number_format($sum40nor, 2, '.', '');
        }

        if(isset($array_markups['m40nor'])){
          $markup40nor=$array_markups['m40nor'];
          $total_markup40nor=$markup40nor/$currency_rate;
          $value->total_markup40nor=number_format($total_markup40nor, 2, '.', '');
        }

        if(isset($array_amounts['c45'])){
          $amount45=$array_amounts['c45'];
          $total45=$amount45/$currency_rate;
          $sum45 = number_format($total45, 2, '.', '');
          $value->total_45=number_format($sum45, 2, '.', '');
        }

        if(isset($array_markups['m45'])){
          $markup45=$array_markups['m45'];
          $total_markup45=$markup45/$currency_rate;
          $value->total_markup45=number_format($total_markup45, 2, '.', '');
        }

        $currency_charge = Currency::find($value->currency_id);
        $value->currency_usd = $currency_charge->rates;
        $value->currency_eur = $currency_charge->rates_eur;
      }

      //Inland
      foreach ($item->inland as $inland) {
        $sum_inland20 = 0;
        $sum_inland40 = 0;
        $sum_inland40hc = 0;
        $sum_inland40nor = 0;
        $sum_inland45 = 0;

        $total_inland_markup20 = 0;
        $total_inland_markup40 = 0;
        $total_inland_markup40hc = 0;
        $total_inland_markup40nor = 0;
        $total_inland_markup45 = 0;                
        if($quote->pdf_option->grouped_total_currency==1){
          $typeCurrency =  $quote->pdf_option->total_in_currency;
        }else{
          $typeCurrency =  $currency_cfg->alphacode;
        }
        $currency_rate=$this->ratesCurrency($inland->currency_id,$typeCurrency);
        $array_amounts = json_decode($inland->rate,true);
        $array_markups = json_decode($inland->markup,true);
        if(isset($array_amounts['c20'])){
          $amount_inland20=$array_amounts['c20'];
          $total_inland20=$amount_inland20/$currency_rate;
          $sum_inland20 = number_format($total_inland20, 2, '.', '');
        }

        if(isset($array_markups['m20'])){
          $markup_inland20=$array_markups['m20'];
          $total_markup20=$markup20/$currency_rate;
        }

        if(isset($array_amounts['c40'])){
          $amount_inland40=$array_amounts['c40'];
          $total_inland40=$amount_inland40/$currency_rate;          
          $sum_inland40 = number_format($total_inland40, 2, '.', '');
        }

        if(isset($array_markups['m40'])){
          $markup_inland40=$array_markups['m40'];
          $total_inland_markup40=$markup_inland40/$currency_rate;
        }

        if(isset($array_amounts['c40hc'])){
          $amount_inland40hc=$array_amounts['c40hc'];
          $total_inland40hc=$amount40hc/$currency_rate;          
          $sum_inland_40hc = number_format($total_inland40hc, 2, '.', '');
        }

        if(isset($array_markups['m40hc'])){
          $markup_inland40hc=$array_markups['m40hc'];
          $total_inland_markup40hc=$markup_inland40hc/$currency_rate;
        }

        if(isset($array_amounts['c40nor'])){
          $amount_inland40nor=$array_amounts['c40nor'];
          $total_inland40nor=$amount40nor/$currency_rate;
          $sum_inland40nor = number_format($total_inland40nor, 2, '.', '');
        }

        if(isset($array_markups['m40nor'])){
          $markup_inland40nor=$array_markups['m40nor'];
          $total_inland_markup40nor=$markup_inland40nor/$currency_rate;
        }

        if(isset($array_amounts['c45'])){
          $amount_inland45=$array_amounts['c45'];
          $total_inland45=$amount45/$currency_rate;
          $sum_inland45 = number_format($total_inland45, 2, '.', '');
        }

        if(isset($array_markups['m45'])){
          $markup_inland45=$array_markups['m45'];
          $total_inland_markup45=$markup_inland45/$currency_rate;
        }

        $inland->total_20=number_format($sum_inland20, 2, '.', '');
        $inland->total_40=number_format($sum_inland40, 2, '.', '');
        $inland->total_40hc=number_format($sum_inland40hc, 2, '.', '');
        $inland->total_40nor=number_format($sum_inland40nor, 2, '.', '');
        $inland->total_45=number_format($sum_inland45, 2, '.', '');

        $inland->total_m20=number_format($total_inland_markup20, 2, '.', '');
        $inland->total_m40=number_format($total_inland_markup40, 2, '.', '');
        $inland->total_m40hc=number_format($total_inland_markup40hc, 2, '.', '');
        $inland->total_m40nor=number_format($total_inland_markup40nor, 2, '.', '');
        $inland->total_m45=number_format($total_inland_markup45, 2, '.', '');

        $currency_charge = Currency::find($inland->currency_id);
        $inland->currency_usd = $currency_charge->rates;
        $inland->currency_eur = $currency_charge->rates_eur;
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
  public function processOriginDetailed($origin_charges, $quote, $currency_cfg){
    $origin_charges_detailed = collect($origin_charges);

    $origin_charges_detailed = $origin_charges_detailed->groupBy([

      function ($item) {
        return $item['carrier']['name'];
      },   
      function ($item) {
        return $item['origin_port']['name'].', '.$item['origin_port']['code'];
      },
      function ($item) {
        return $item['destination_port']['name'];
      },

    ], $preserveKeys = true);

    foreach($origin_charges_detailed as $origin=>$item){
      foreach($item as $destination=>$items){
        foreach($items as $carrier=>$itemsDetail){
          foreach ($itemsDetail as $value) {     
            foreach ($value->charge as $amounts) {
              $sum20=0;
              $sum40=0;
              $sum40hc=0;
              $sum40nor=0;
              $sum45=0;
              $total40=0;
              $total20=0;
              $total40hc=0;
              $total40nor=0;
              $total45=0;
              if($amounts->type_id==1){
                if($quote->pdf_option->grouped_origin_charges==1){
                  $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                $array_amounts = json_decode($amounts->amount,true);
                $array_markups = json_decode($amounts->markups,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20']+$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20'];
                  $total20=$sum20/$currency_rate;
                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }

                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40']+$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40'];
                  $total40=$sum40/$currency_rate;
                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }

                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }

                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }

                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45']+$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45'];
                  $total45=$sum45/$currency_rate;
                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }

                $amounts->total_20=number_format($total20, 2, '.', '');
                $amounts->total_40=number_format($total40, 2, '.', '');
                $amounts->total_40hc=number_format($total40hc, 2, '.', '');
                $amounts->total_40nor=number_format($total40nor, 2, '.', '');
                $amounts->total_45=number_format($total45, 2, '.', '');
              }
            }
            if(!$value->inland->isEmpty()){
              foreach($value->inland as $value){
                $inland20=0;
                $inland40=0;
                $inland40hc=0;
                $inland40nor=0;
                $inland45=0;

                if($quote->pdf_option->grouped_origin_charges==1){
                  $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                $array_amounts = json_decode($value->rate,true);
                $array_markups = json_decode($value->markup,true);

                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $amount20=$array_amounts['c20'];
                  $markup20=$array_markups['m20'];
                  $total20=($amount20+$markup20)/$currency_rate;
                  $inland20 = number_format($total20, 2, '.', '');
                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                  $amount20=$array_amounts['c20'];
                  $total20=$amount20/$currency_rate;
                  $inland20 = number_format($total20, 2, '.', '');
                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $markup20=$array_markups['m20'];
                  $total20=$markup20/$currency_rate;
                  $inland20 = number_format($total20, 2, '.', '');
                }

                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $amount40=$array_amounts['c40'];
                  $markup40=$array_markups['m40'];
                  $total40=($amount40+$markup40)/$currency_rate;
                  $inland40 = number_format($total40, 2, '.', '');
                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                  $amount40=$array_amounts['c40'];
                  $total40=$amount40/$currency_rate;
                  $inland40 = number_format($total40, 2, '.', '');
                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $markup40=$array_markups['m40'];
                  $total40=$markup40/$currency_rate;
                  $inland40 = number_format($total40, 2, '.', '');
                }

                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $amount40hc=$array_amounts['c40hc'];
                  $markup40hc=$array_markups['m40hc'];
                  $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                  $inland40hc = number_format($total40hc, 2, '.', '');
                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                  $amount40hc=$array_amounts['c40hc'];
                  $total40hc=$amount40hc/$currency_rate;
                  $inland40hc = number_format($total40hc, 2, '.', '');
                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $markup40hc=$array_markups['m40hc'];
                  $total40hc=$markup40hc/$currency_rate;
                  $inland40hc = number_format($total40hc, 2, '.', '');
                }

                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $amount40nor=$array_amounts['c40nor'];
                  $markup40nor=$array_markups['m40nor'];
                  $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                  $inland40nor = number_format($total40nor, 2, '.', '');
                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                  $amount40nor=$array_amounts['c40nor'];
                  $total40nor=$amount40nor/$currency_rate;
                  $inland40nor = number_format($total40nor, 2, '.', '');
                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $markup40nor=$array_markups['m40nor'];
                  $total40nor=$markup40nor/$currency_rate;
                  $inland40nor = number_format($total40nor, 2, '.', '');
                }

                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $amount45=$array_amounts['c45'];
                  $markup45=$array_markups['m45'];
                  $total45=($amount45+$markup45)/$currency_rate;
                  $inland45 = number_format($total45, 2, '.', '');
                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                  $amount45=$array_amounts['c45'];
                  $total45=$amount45/$currency_rate;
                  $inland45 = number_format($total45, 2, '.', '');
                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $markup45=$array_markups['m45'];
                  $total45=$markup45/$currency_rate;
                  $inland45 = number_format($total45, 2, '.', '');
                }

                $value->total_20=number_format($inland20, 2, '.', '');
                $value->total_40=number_format($inland40, 2, '.', '');
                $value->total_40hc=number_format($inland40hc, 2, '.', '');
                $value->total_40nor=number_format($inland40nor, 2, '.', '');
                $value->total_45=number_format($inland45, 2, '.', '');
              }
            }            
          }
        } 
      }
    }

    return $origin_charges_detailed;
  }    

  /**
     * Process collections origins grouped rates
     * @param  collection $origin_charges
     * @param  collection $quote
     * @return collection
     */
  public function processOriginGrouped($origin_charges, $quote, $currency_cfg){
    $origin_charges_grouped = collect($origin_charges);

    $origin_charges_grouped = $origin_charges_grouped->groupBy([

      function ($item) {
        return $item['origin_port']['name'].', '.$item['origin_port']['code'];
      },
      function ($item) {
        return $item['carrier']['name'];
      },

    ], $preserveKeys = true);

    foreach($origin_charges_grouped as $origin=>$detail){
      foreach($detail as $item){
        foreach($item as $rate){

          $sum20= 0;
          $sum40= 0;
          $sum40hc= 0;
          $sum40nor= 0;
          $sum45= 0;

          foreach($rate->charge as $value){

            if($value->type_id==1){
              if($quote->pdf_option->grouped_origin_charges==1){
                $typeCurrency =  $quote->pdf_option->origin_charges_currency;
              }else{
                $typeCurrency =  $currency_cfg->alphacode;
              }
              $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
              $array_amounts = json_decode($value->amount,true);
              $array_markups = json_decode($value->markups,true);
              if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $markup20=$array_markups['m20'];
                $total20=($amount20+$markup20)/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $total20=$amount20/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $markup20=$array_markups['m20'];
                $total20=$markup20/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }

              if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $markup40=$array_markups['m40'];
                $total40=($amount40+$markup40)/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $total40=$amount40/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $markup40=$array_markups['m40'];
                $total40=$markup40/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }

              if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $markup40hc=$array_markups['m40hc'];
                $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $total40hc=$amount40hc/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $markup40hc=$array_markups['m40hc'];
                $total40hc=$markup40hc/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }

              if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $markup40nor=$array_markups['m40nor'];
                $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $total40nor=$amount40nor/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $markup40nor=$array_markups['m40nor'];
                $total40nor=$markup40nor/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }

              if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $markup45=$array_markups['m45'];
                $total45=($amount45+$markup45)/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $total45=$amount45/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $markup45=$array_markups['m45'];
                $total45=$markup45/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }

              $value->total_20=number_format($sum20, 2, '.', '');
              $value->total_40=number_format($sum40, 2, '.', '');
              $value->total_40hc=number_format($sum40hc, 2, '.', '');
              $value->total_40nor=number_format($sum40nor, 2, '.', '');
              $value->total_45=number_format($sum45, 2, '.', '');
            }
          }
          if(!$rate->inland->isEmpty()){
            foreach($rate->inland as $value){
              $inland20=0;
              $inland40=0;
              $inland40hc=0;
              $inland40nor=0;
              $inland45=0;

              if($quote->pdf_option->grouped_destination_charges==1){
                $typeCurrency =  $quote->pdf_option->destination_charges_currency;
              }else{
                $typeCurrency =  $currency_cfg->alphacode;
              }
              $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
              $array_amounts = json_decode($value->rate,true);
              $array_markups = json_decode($value->markup,true);

              if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $markup20=$array_markups['m20'];
                $total20=($amount20+$markup20)/$currency_rate;
                $inland20 += number_format($total20, 2, '.', '');
              }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $total20=$amount20/$currency_rate;
                $inland20 += number_format($total20, 2, '.', ''); 
              }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $markup20=$array_markups['m20'];
                $total20=$markup20/$currency_rate;
                $inland20 += number_format($total20, 2, '.', '');
              }else{
                $inland20 = 0;
              }

              if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $markup40=$array_markups['m40'];
                $total40=($amount40+$markup40)/$currency_rate;
                $inland40 += number_format($total40, 2, '.', '');
              }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $total40=$amount40/$currency_rate;
                $inland40 += number_format($total40, 2, '.', ''); 
              }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $markup40=$array_markups['m40'];
                $total40=$markup40/$currency_rate;
                $inland40 += number_format($total40, 2, '.', '');
              }

              if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $markup40hc=$array_markups['m40hc'];
                $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                $inland40hc += number_format($total40hc, 2, '.', '');
              }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $total40hc=$amount40hc/$currency_rate;
                $inland40hc += number_format($total40hc, 2, '.', ''); 
              }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $markup40hc=$array_markups['m40hc'];
                $total40hc=$markup40hc/$currency_rate;
                $inland40hc += number_format($total40hc, 2, '.', '');
              }

              if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $markup40nor=$array_markups['m40nor'];
                $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                $inland40nor += number_format($total40nor, 2, '.', '');
              }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $total40nor=$amount40nor/$currency_rate;
                $inland40nor += number_format($total40nor, 2, '.', ''); 
              }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $markup40nor=$array_markups['m40nor'];
                $total40nor=$markup40nor/$currency_rate;
                $inland40nor += number_format($total40nor, 2, '.', '');
              }

              if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $markup45=$array_markups['m45'];
                $total45=($amount45+$markup45)/$currency_rate;
                $inland45 += number_format($total45, 2, '.', '');
              }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $total45=$amount45/$currency_rate;
                $inland45 += number_format($total45, 2, '.', ''); 
              }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $markup45=$array_markups['m45'];
                $total45=$markup45/$currency_rate;
                $inland45 += number_format($total45, 2, '.', '');
              }

              $value->total_20=number_format($inland20, 2, '.', '');
              $value->total_40=number_format($inland40, 2, '.', '');
              $value->total_40hc=number_format($inland40hc, 2, '.', '');
              $value->total_40nor=number_format($inland40nor, 2, '.', '');
              $value->total_45=number_format($inland45, 2, '.', '');
            }
          } 
        }
      }
    }

    return $origin_charges_grouped;
  }

  /**
     * Process collections destination grouped rates
     * @param  collection $destination_charges
     * @param  collection $quote
     * @return collection
     */
  public function processDestinationGrouped($destination_charges, $quote, $currency_cfg){
    $destination_charges_grouped = collect($destination_charges);

    $destination_charges_grouped = $destination_charges_grouped->groupBy([

      function ($item) {
        return $item['destination_port']['name'].', '.$item['destination_port']['code'];
      },
      function ($item) {
        return $item['carrier']['name'];
      },

    ], $preserveKeys = true);
    foreach($destination_charges_grouped as $origin=>$detail){
      foreach($detail as $item){
        foreach($item as $rate){

          $sum20= 0;
          $sum40= 0;
          $sum40hc= 0;
          $sum40nor= 0;
          $sum45= 0;

          foreach($rate->charge as $value){

            if($value->type_id==2){
              if($quote->pdf_option->grouped_destination_charges==1){
                $typeCurrency =  $quote->pdf_option->destination_charges_currency;
              }else{
                $typeCurrency =  $currency_cfg->alphacode;
              }
              $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
              $array_amounts = json_decode($value->amount,true);
              $array_markups = json_decode($value->markups,true);
              if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $markup20=$array_markups['m20'];
                $total20=($amount20+$markup20)/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $total20=$amount20/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $markup20=$array_markups['m20'];
                $total20=$markup20/$currency_rate;
                $sum20 += number_format($total20, 2, '.', '');
              }

              if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $markup40=$array_markups['m40'];
                $total40=($amount40+$markup40)/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $total40=$amount40/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $markup40=$array_markups['m40'];
                $total40=$markup40/$currency_rate;
                $sum40 += number_format($total40, 2, '.', '');
              }

              if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $markup40hc=$array_markups['m40hc'];
                $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $total40hc=$amount40hc/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $markup40hc=$array_markups['m40hc'];
                $total40hc= $markup40hc/$currency_rate;
                $sum40hc += number_format($total40hc, 2, '.', '');
              }

              if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $markup40nor=$array_markups['m40nor'];
                $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $total40nor=$amount40nor/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $markup40nor=$array_markups['m40nor'];
                $total40nor=$markup40nor/$currency_rate;
                $sum40nor += number_format($total40nor, 2, '.', '');
              }

              if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $markup45=$array_markups['m45'];
                $total45=($amount45+$markup45)/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $total45=$amount45/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $markup45=$array_markups['m45'];
                $total45=$markup45/$currency_rate;
                $sum45 += number_format($total45, 2, '.', '');
              }

              $value->total_20=number_format($sum20, 2, '.', '');
              $value->total_40=number_format($sum40, 2, '.', '');
              $value->total_40hc=number_format($sum40hc, 2, '.', '');
              $value->total_40nor=number_format($sum40nor, 2, '.', '');
              $value->total_45=number_format($sum45, 2, '.', '');
            }
          }
          if(!$rate->inland->isEmpty()){
            foreach($rate->inland as $value){
              $inland20=0;
              $inland40=0;
              $inland40hc=0;
              $inland40nor=0;
              $inland45=0;                            
              if($quote->pdf_option->grouped_destination_charges==1){
                $typeCurrency =  $quote->pdf_option->destination_charges_currency;
              }else{
                $typeCurrency =  $currency_cfg->alphacode;
              }
              $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
              $array_amounts = json_decode($value->rate,true);
              $array_markups = json_decode($value->markup,true);

              if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $markup20=$array_markups['m20'];
                $total20=($amount20+$markup20)/$currency_rate;
                $inland20 += number_format($total20, 2, '.', '');
              }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                $amount20=$array_amounts['c20'];
                $total20=$amount20/$currency_rate;
                $inland20 += number_format($total20, 2, '.', ''); 
              }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                $markup20=$array_markups['m20'];
                $total20=$markup20/$currency_rate;
                $inland20 += number_format($total20, 2, '.', '');
              }

              if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $markup40=$array_markups['m40'];
                $total40=($amount40+$markup40)/$currency_rate;
                $inland40 += number_format($total40, 2, '.', '');
              }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                $amount40=$array_amounts['c40'];
                $total40=$amount40/$currency_rate;
                $inland40 += number_format($total40, 2, '.', ''); 
              }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                $markup40=$array_markups['m40'];
                $total40=$markup40/$currency_rate;
                $inland40 += number_format($total40, 2, '.', '');
              }

              if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $markup40hc=$array_markups['m40hc'];
                $total40hc=($amount40hc+$markup40hc)/$currency_rate;
                $inland40hc += number_format($total40hc, 2, '.', '');
              }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                $amount40hc=$array_amounts['c40hc'];
                $total40hc=$amount40hc/$currency_rate;
                $inland40hc += number_format($total40hc, 2, '.', ''); 
              }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                $markup40hc=$array_markups['m40hc'];
                $total40hc=$markup40hc/$currency_rate;
                $inland40hc += number_format($total40hc, 2, '.', '');
              }

              if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $markup40nor=$array_markups['m40nor'];
                $total40nor=($amount40nor+$markup40nor)/$currency_rate;
                $inland40nor += number_format($total40nor, 2, '.', '');
              }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                $amount40nor=$array_amounts['c40nor'];
                $total40nor=$amount40nor/$currency_rate;
                $inland40nor += number_format($total40nor, 2, '.', ''); 
              }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                $markup40nor=$array_markups['m40nor'];
                $total40nor=$markup40nor/$currency_rate;
                $inland40nor += number_format($total40nor, 2, '.', '');
              }

              if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $markup45=$array_markups['m45'];
                $total45=($amount45+$markup45)/$currency_rate;
                $inland45 += number_format($total45, 2, '.', '');
              }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                $amount45=$array_amounts['c45'];
                $total45=$amount45/$currency_rate;
                $inland45 += number_format($total45, 2, '.', ''); 
              }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                $markup45=$array_markups['m45'];
                $total45=$markup45/$currency_rate;
                $inland45 += number_format($total45, 2, '.', '');
              }

              $value->total_20=number_format($inland20, 2, '.', '');
              $value->total_40=number_format($inland40, 2, '.', '');
              $value->total_40hc=number_format($inland40hc, 2, '.', '');
              $value->total_40nor=number_format($inland40nor, 2, '.', '');
              $value->total_45=number_format($inland45, 2, '.', '');
            }
          } 
        }
      }
    }

    return $destination_charges_grouped;
  }

  /**
     * Process collections destination detailed rates
     * @param  collection $destination_charges
     * @param  collection $quote
     * @return collection
     */
  public function processDestinationDetailed($destination_charges, $quote, $currency_cfg){
    $destination_charges = $destination_charges->groupBy([

      function ($item) {
        return $item['carrier']['name'];
      },   
      function ($item) {
        return $item['destination_port']['name'].', '.$item['destination_port']['code'];
      },
      function ($item) {
        return $item['origin_port']['name'];
      },

    ], $preserveKeys = true);

    foreach($destination_charges as $carrier=>$item){
      foreach($item as $destination=>$items){
        foreach($items as $origin=>$itemsDetail){
          foreach ($itemsDetail as $value) {     
            foreach ($value->charge as $amounts) {
              $sum20=0;
              $sum40=0;
              $sum40hc=0;
              $sum40nor=0;
              $sum45=0;
              $total40=0;
              $total20=0;
              $total40hc=0;
              $total40nor=0;
              $total45=0;        
              if($amounts->type_id==2){
                //dd($quote->pdf_option->destination_charges_currency);
                if($quote->pdf_option->grouped_destination_charges==1){
                  $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                $array_amounts = json_decode($amounts->amount,true);
                $array_markups = json_decode($amounts->markups,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20']+$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20'];
                  $total20=$sum20/$currency_rate;
                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }

                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40']+$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40'];
                  $total40=$sum40/$currency_rate;
                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }

                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }

                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }

                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45']+$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45'];
                  $total45=$sum45/$currency_rate;
                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }        

                $amounts->total_20=number_format($total20, 2, '.', '');
                $amounts->total_40=number_format($total40, 2, '.', '');
                $amounts->total_40hc=number_format($total40hc, 2, '.', '');
                $amounts->total_40nor=number_format($total40nor, 2, '.', '');
                $amounts->total_45=number_format($total45, 2, '.', '');
              }
            }
            if(!$value->inland->isEmpty()){
              foreach($value->inland as $value){
                $inland20=0;
                $inland40=0;
                $inland40hc=0;
                $inland40nor=0;
                $inland45=0;
                $total_inland20=0;
                $total_inland40=0;
                $total_inland40nor=0;
                $total_inland40hc=0;
                $total_inland45=0;
                if($quote->pdf_option->grouped_destination_charges==1){
                  $typeCurrency =  $quote->pdf_option->destination_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }
                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                $array_amounts = json_decode($value->rate,true);
                $array_markups = json_decode($value->markup,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $amount20=$array_amounts['c20'];
                  $markup20=$array_markups['m20'];
                  $total_inland20=($amount20+$markup20)/$currency_rate;
                  $inland20 = number_format($total_inland20, 2, '.', '');
                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                  $amount20=$array_amounts['c20'];
                  $total_inland20=$amount20/$currency_rate;
                  $inland20 = number_format($total_inland20, 2, '.', ''); 
                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $markup20=$array_markups['m20'];
                  $total_inland20=$markup20/$currency_rate;
                  $inland20 = number_format($total_inland20, 2, '.', '');
                }

                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $amount40=$array_amounts['c40'];
                  $markup40=$array_markups['m40'];
                  $total_inland40=($amount40+$markup40)/$currency_rate;
                  $inland40 = number_format($total_inland40, 2, '.', '');
                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                  $amount40=$array_amounts['c40'];
                  $total_inland40=$amount40/$currency_rate;
                  $inland40 = number_format($total_inland40, 2, '.', ''); 
                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $markup40=$array_markups['m40'];
                  $total_inland40=$markup40/$currency_rate;
                  $inland40 = number_format($total_inland40, 2, '.', '');
                }

                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $amount40hc=$array_amounts['c40hc'];
                  $markup40hc=$array_markups['m40hc'];
                  $total_inland40hc=($amount40hc+$markup40hc)/$currency_rate;
                  $inland40hc = number_format($total_inland40hc, 2, '.', '');
                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                  $amount40hc=$array_amounts['c40hc'];
                  $total_inland40hc=$amount40hc/$currency_rate;
                  $inland40hc = number_format($total_inland40hc, 2, '.', ''); 
                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $markup40hc=$array_markups['m40hc'];
                  $total_inland40hc=$markup40hc/$currency_rate;
                  $inland40hc = number_format($total_inland40hc, 2, '.', '');
                }

                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $amount40nor=$array_amounts['c40nor'];
                  $markup40nor=$array_markups['m40nor'];
                  $total_inland40nor=($amount40nor+$markup40nor)/$currency_rate;
                  $inland40nor = number_format($total_inland40nor, 2, '.', '');
                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                  $amount40nor=$array_amounts['c40nor'];
                  $total_inland40nor=$amount40nor/$currency_rate;
                  $inland40nor = number_format($total_inland40nor, 2, '.', ''); 
                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $markup40nor=$array_markups['m40nor'];
                  $total_inland40nor=$markup40nor/$currency_rate;
                  $inland40nor = number_format($total_inland40nor, 2, '.', '');
                }

                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $amount45=$array_amounts['c45'];
                  $markup45=$array_markups['m45'];
                  $total_inland45=($amount45+$markup45)/$currency_rate;
                  $inland45 = number_format($total_inland45, 2, '.', '');
                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                  $amount45=$array_amounts['c45'];
                  $total_inland45=$amount45/$currency_rate;
                  $inland45 = number_format($total_inland45, 2, '.', ''); 
                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $markup45=$array_markups['m45'];
                  $total_inland45=$markup45/$currency_rate;
                  $inland45 = number_format($total_inland45, 2, '.', '');
                }
                $value->total_20=number_format($inland20, 2, '.', '');
                $value->total_40=number_format($inland40, 2, '.', '');
                $value->total_40hc=number_format($inland40hc, 2, '.', '');
                $value->total_40nor=number_format($inland40nor, 2, '.', '');
                $value->total_45=number_format($inland45, 2, '.', '');
              }
            } 
          }
        } 
      }
    }
    return $destination_charges;
  }

  /**
     * Process collections freight charges
     * @param  collection $freight_charges
     * @param  collection $quote
     * @return collection
     */
  public function processFreightCharges($freight_charges, $quote, $currency_cfg){

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
          $total_rate20=0;
          $total_rate40=0;
          $total_rate40hc=0;
          $total_rate40nor=0;
          $total_rate45=0;

          $total_rate_markup20=0;
          $total_rate_markup40=0;
          $total_rate_markup40hc=0;
          $total_rate_markup40nor=0;
          $total_rate_markup45=0;

          foreach($item as $rate){
            $sum20=0;
            $sum40=0;
            $sum40hc=0;
            $sum40nor=0;
            $sum45=0;
            $total40=0;
            $total20=0;
            $total40hc=0;
            $total40nor=0;
            $total45=0;

            foreach ($rate->charge as $amounts) {
              if($amounts->type_id==3){
                /*if($freight_charges_grouped->count()>1){
                                    $typeCurrency =  $currency_cfg->alphacode;
                                }else{*/
                if($quote->pdf_option->grouped_freight_charges==1){
                  $typeCurrency = $quote->pdf_option->freight_charges_currency;
                }else{
                  $typeCurrency = $currency_cfg->alphacode;   
                }
                //}
                $currency_rate=$this->ratesCurrency($amounts->currency_id,$typeCurrency);
                $array_amounts = json_decode($amounts->amount,true);
                $array_markups = json_decode($amounts->markups,true);
                if(isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20']+$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }else if(isset($array_amounts['c20']) && !isset($array_markups['m20'])){
                  $sum20=$array_amounts['c20'];
                  $total20=$sum20/$currency_rate;
                }else if(!isset($array_amounts['c20']) && isset($array_markups['m20'])){
                  $sum20=$array_markups['m20'];
                  $total20=$sum20/$currency_rate;
                }

                if(isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40']+$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }else if(isset($array_amounts['c40']) && !isset($array_markups['m40'])){
                  $sum40=$array_amounts['c40'];
                  $total40=$sum40/$currency_rate;
                }else if(!isset($array_amounts['c40']) && isset($array_markups['m40'])){
                  $sum40=$array_markups['m40'];
                  $total40=$sum40/$currency_rate;
                }

                if(isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc']+$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(isset($array_amounts['c40hc']) && !isset($array_markups['m40hc'])){
                  $sum40hc=$array_amounts['c40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }else if(!isset($array_amounts['c40hc']) && isset($array_markups['m40hc'])){
                  $sum40hc=$array_markups['m40hc'];
                  $total40hc=$sum40hc/$currency_rate;
                }

                if(isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor']+$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(isset($array_amounts['c40nor']) && !isset($array_markups['m40nor'])){
                  $sum40nor=$array_amounts['c40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }else if(!isset($array_amounts['c40nor']) && isset($array_markups['m40nor'])){
                  $sum40nor=$array_markups['m40nor'];
                  $total40nor=$sum40nor/$currency_rate;
                }

                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45']+$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }else if(isset($array_amounts['c45']) && !isset($array_markups['m45'])){
                  $sum45=$array_amounts['c45'];
                  $total45=$sum45/$currency_rate;
                }else if(!isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $sum45=$array_markups['m45'];
                  $total45=$sum45/$currency_rate;
                }

                if(isset($array_amounts['c20']) || isset($array_markups['m20'])){
                  $amounts->total_20=number_format($total20, 2, '.', '');
                }
                if(isset($array_amounts['c40']) || isset($array_markups['m40'])){
                  $amounts->total_40=number_format($total40, 2, '.', '');
                }
                if(isset($array_amounts['c40hc']) || isset($array_markups['m40hc'])){
                  $amounts->total_40hc=number_format($total40hc, 2, '.', '');
                }
                if(isset($array_amounts['c40nor']) || isset($array_markups['m40nor'])){
                  $amounts->total_40nor=number_format($total40nor, 2, '.', '');
                }
                if(isset($array_amounts['c45']) && isset($array_markups['m45'])){
                  $amounts->total_45=number_format($total45, 2, '.', '');
                }
              }
            }
          }
        }
      }
    }

    return $freight_charges_grouped;
  }

  public function processChargesLclAir($charges,$type,$type_2,$carrier){
    $charges_grouped = collect(charges);

    $charges_grouped = $charges_grouped->groupBy([

      function ($item) {
        return $item[$type]['name'].', '.$item[$type]['code'];
      },
      function ($item) {
        return $item[$carrier]['name'];
      },      
      function ($item) {
        return $item[$type_2]['name'];
      },
    ], $preserveKeys = true);
    foreach($origin_charges_grouped as $origin=>$detail){
      foreach($detail as $item){
        foreach($item as $v){
          foreach($v as $rate){
            foreach($rate->charge_lcl_air as $value){

              if($value->type_id==1){
                if($quote->pdf_option->grouped_origin_charges==1){
                  $typeCurrency =  $quote->pdf_option->origin_charges_currency;
                }else{
                  $typeCurrency =  $currency_cfg->alphacode;
                }

                $currency_rate=$this->ratesCurrency($value->currency_id,$typeCurrency);
                $value->rate=number_format((($value->units*$value->price_per_unit)+$value->markup)/$value->units, 2, '.', '');
                $value->total_origin=number_format((($value->units*$value->price_per_unit)+$value->markup)/$currency_rate, 2, '.', '');

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
  public function hideContainer($equipmentForm,$tipo){
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

    if($tipo == 'BD'){
      $equipmentForm = json_decode($equipmentForm);
    }

    $countEquipment = count($equipmentForm);
    $countEquipment = 5 - $countEquipment;
    if($countEquipment == 1 ){
      $originClass = 'col-md-3';
      $destinyClass = 'col-md-1';
      $dataOrigDest = 'col-md-4';
    }
    if($countEquipment == 2 ){
      $originClass = 'col-md-3';
      $destinyClass = 'col-md-2';
      $dataOrigDest = 'col-md-5';
    }
    if($countEquipment == 3){
      $originClass = 'col-md-4';
      $destinyClass = 'col-md-2';
      $dataOrigDest = 'col-md-6';
    }
    if($countEquipment == 4){
      $originClass = 'col-md-5';
      $destinyClass = 'col-md-2';
      $dataOrigDest = 'col-md-7';
    }

    foreach($equipmentForm as $val){
      if($val == '20'){
        $hidden20 = '';
      }
      if($val == '40'){
        $hidden40 = '';
      }
      if($val == '40HC'){
        $hidden40hc = '';
      }
      if($val == '40NOR'){
        $hidden40nor = '';
      }
      if($val == '45'){
        $hidden45 = '';
      }
    }
    $equipment->put('originClass',$originClass);
    $equipment->put('destinyClass',$destinyClass);
    $equipment->put('dataOrigDest',$dataOrigDest);
    $equipment->put('20',$hidden20);
    $equipment->put('40',$hidden40);
    $equipment->put('40hc',$hidden40hc);
    $equipment->put('40nor',$hidden40nor);
    $equipment->put('45',$hidden45);
    return($equipment);
  }

  public function ratesCurrency($id,$typeCurrency){
    $rates = Currency::where('id','=',$id)->get();
    foreach($rates as $rate){
      if($typeCurrency == "USD"){
        $rateC = $rate->rates;
      }else{
        $rateC = $rate->rates_eur;
      }
    }
    return $rateC;
  }

  public function getPortsInArray($collection){
    $array = array();

    foreach($collection as $value){
      $array["port_id"]=$value->port_id;
    }

    return $array;
  }    

  public function getAirportsInArray($collection){
    $array = array();

    foreach($collection as $value){
      $array["airport_id"]=$value->airport_id;
    }

    return $array;
  }

  public function addSaleTermToRate($rates, $origin_ports, $destination_ports,$sale_terms_origin_grouped, $sale_terms_destination_grouped){
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

    return $rates;
  }


  public function addSaleTermToRateLcl($rates, $origin_ports, $destination_ports,$sale_terms_origin_grouped, $sale_terms_destination_grouped){
    $rates = $rates->map(function ($item, $key) use($origin_ports, $destination_ports, $sale_terms_origin_grouped, $sale_terms_destination_grouped){
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

    return $rates;
  }

  public function saveEmailNotification($to, $email_from, $subject, $body, $quote, $sign_type, $sign, $contact_email){
    if($to!=''){
      $explode=explode(';',$to);
      foreach($explode as $item) {
        $send_quote = new SendQuote();
        $send_quote->to = trim($item);
        $send_quote->from = $email_from;
        $send_quote->subject = $subject;
        $send_quote->body = $body;
        $send_quote->quote_id = $quote->id;
        if($sign_type!=''){
          $send_quote->sign_type = $sign_type;
        }
        $send_quote->sign = $sign;
        $send_quote->status = 0;
        $send_quote->save();
      }
    }else{
      $send_quote = new SendQuote();
      $send_quote->to = $contact_email->email;
      $send_quote->from = $email_from;
      $send_quote->subject = $subject;
      $send_quote->body = $body;
      $send_quote->quote_id = $quote->id;
      if($sign_type!=''){
        $send_quote->sign_type = $sign_type;
      }
      $send_quote->sign = $sign;
      $send_quote->status = 0;
      $send_quote->save();
    }
  }


  public function generatepdf($id,$company_user,$currency_cfg){




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
    
    $pdfarray = array('pdf'=>$pdf,'view'=>$view,'idQuote'=>$quote->quote_id,'idQ'=>$quote->id);

    return $pdfarray;



  }


}