<?php

namespace App\Http\Controllers;

use App\Jobs\SendQuotes;
use App\SendQuote;
use Illuminate\Http\Request;
use App\Company;
use App\CompanyUser;
use App\Currency;
use App\CompanyPrice;
use App\Contact;
use App\Country;
use App\DestinationAmmount;
use App\DestinationAmount;
use App\FreightAmmount;
use App\OriginAmmount;
use App\OriginAmount;
use App\Harbor;
use App\Price;
use App\Quote;
use App\User;
use App\EmailTemplate;
use App\PackageLoad;
use App\Mail\SendQuotePdf;
use App\TermsPort;
use Api2Pdf\Api2Pdf;

class PdfController extends Controller
{
    public function quote($id)
    {
        // set API Endpoint and access key (and any options of your choice)
        $id = obtenerRouteKey($id);
        $endpoint = 'live';
        $access_key = 'a0a9f774999e3ea605ee13ee9373e755';

        $quote = Quote::where('id',$id)->with('contact')->first();
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
        $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
        $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
        $user = User::where('id',\Auth::id())->with('companyUser')->first();
        $package_loads = PackageLoad::where('quote_id',$id)->get();

        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $type=$company_user->type_pdf;
            $ammounts_type=$company_user->pdf_ammounts;
            $currency_cfg = Currency::find($company_user->currency_id);
            $port_all = harbor::where('name','ALL')->first();
            $terms_all = TermsPort::where('port_id',$port_all->id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
            $terms_origin = TermsPort::where('port_id',$quote->origin_harbor_id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
            $terms_destination = TermsPort::where('port_id',$quote->destination_harbor_id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
        }

        foreach($origin_ammounts as $item){
            $currency=Currency::find($item->currency_id);
            // Initialize CURL:
            $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&source='.$currency->alphacode);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $exchangeRates = json_decode($json, true);

            if($quote->currencies->alphacode=='USD'){
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'USD'];
                $currency_rate=Currency::where('api_code','USD'.$currency->alphacode)->first();
                $rate=$currency_rate->rates;
            }else{
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'EUR'];
                $currency_rate=Currency::where('api_code_eur','EUR'.$currency->alphacode)->first();
                $rate=$currency_rate->rates_eur;
            }
            $item->markup_converted = $markup_converted;
            $item->rate = $rate;
        }

        foreach($freight_ammounts as $item){
            $currency=Currency::find($item->currency_id);
            // Initialize CURL:
            $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&source='.$currency->alphacode);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $exchangeRates = json_decode($json, true);

            if($quote->currencies->alphacode=='USD'){
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'USD'];
                $currency_rate=Currency::where('api_code','USD'.$currency->alphacode)->first();
                $rate=$currency_rate->rates;
            }else{
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'EUR'];
                $currency_rate=Currency::where('api_code_eur','EUR'.$currency->alphacode)->first();
                $rate=$currency_rate->rates;
            }
            $item->markup_converted = $markup_converted;
            $item->rate = $rate;
        }

        //dd(json_encode($item->markup/1.16));

        foreach($destination_ammounts as $item){
            $currency=Currency::find($item->currency_id);
            // Initialize CURL:
            $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&source='.$currency->alphacode);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $exchangeRates = json_decode($json, true);

            if($quote->currencies->alphacode=='USD'){
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'USD'];
                $currency_rate=Currency::where('api_code','USD'.$currency->alphacode)->first();
                $rate=$currency_rate->rates;
            }else{
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'EUR'];
                $currency_rate=Currency::where('api_code_eur','EUR'.$currency->alphacode)->first();
                $rate=$currency_rate->rates;
            }
            $item->markup_converted = $markup_converted;
            $item->rate = $rate;
        }

        if($quote->pdf_language!=''){
            if($quote->pdf_language==3){
                $view = \View::make('quotes.pdf.index-portuguese', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else if($quote->pdf_language==2){
                $view = \View::make('quotes.pdf.index-spanish', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else{
                $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }
        }else if($quote->company->pdf_language!=''){
            if($quote->company->pdf_language==3){
                $view = \View::make('quotes.pdf.index-portuguese', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else if($quote->company->pdf_language==2){
                $view = \View::make('quotes.pdf.index-spanish', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else{
                $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }
        }else{
            if($company_user->pdf_language==1){
                $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else if($company_user->pdf_language==2){
                $view = \View::make('quotes.pdf.index-spanish', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else if($company_user->pdf_language==3){
                $view = \View::make('quotes.pdf.index-portuguese', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else{
                $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }
        }




        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);

        return $pdf->stream('quote_'.$quote->id.'.pdf');
    }

    public function quote_2($id)
    {
        // set API Endpoint and access key (and any options of your choice)
        $id = obtenerRouteKey($id);
        $endpoint = 'live';
        $access_key = 'a0a9f774999e3ea605ee13ee9373e755';

        $quote = Quote::where('id',$id)->with('contact')->first();
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
        $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
        $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
        $user = User::where('id',\Auth::id())->with('companyUser')->first();
        $package_loads = PackageLoad::where('quote_id',$id)->get();

        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $type=$company_user->type_pdf;
            $ammounts_type=$company_user->pdf_ammounts;
            $currency_cfg = Currency::find($company_user->currency_id);
            $port_all = harbor::where('name','ALL')->first();
            $terms_all = TermsPort::where('port_id',$port_all->id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
            $terms_origin = TermsPort::where('port_id',$quote->origin_harbor_id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
            $terms_destination = TermsPort::where('port_id',$quote->destination_harbor_id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
        }

        foreach($origin_ammounts as $item){
            $currency=Currency::find($item->currency_id);
            // Initialize CURL:
            $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&source='.$currency->alphacode);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $exchangeRates = json_decode($json, true);

            if($quote->currencies->alphacode=='USD'){
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'USD'];
                $currency_rate=Currency::where('api_code','USD'.$currency->alphacode)->first();
                $rate=$currency_rate->rates;
            }else{
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'EUR'];
                $currency_rate=Currency::where('api_code_eur','EUR'.$currency->alphacode)->first();
                $rate=$currency_rate->rates_eur;
            }
            $item->markup_converted = $markup_converted;
            $item->rate = $rate;
        }

        foreach($freight_ammounts as $item){
            $currency=Currency::find($item->currency_id);
            // Initialize CURL:
            $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&source='.$currency->alphacode);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $exchangeRates = json_decode($json, true);

            if($quote->currencies->alphacode=='USD'){
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'USD'];
                $currency_rate=Currency::where('api_code','USD'.$currency->alphacode)->first();
                $rate=$currency_rate->rates;
            }else{
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'EUR'];
                $currency_rate=Currency::where('api_code_eur','EUR'.$currency->alphacode)->first();
                $rate=$currency_rate->rates;
            }
            $item->markup_converted = $markup_converted;
            $item->rate = $rate;
        }

        //dd(json_encode($item->markup/1.16));

        foreach($destination_ammounts as $item){
            $currency=Currency::find($item->currency_id);
            // Initialize CURL:
            $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&source='.$currency->alphacode);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $exchangeRates = json_decode($json, true);

            if($quote->currencies->alphacode=='USD'){
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'USD'];
                $currency_rate=Currency::where('api_code','USD'.$currency->alphacode)->first();
                $rate=$currency_rate->rates;
            }else{
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'EUR'];
                $currency_rate=Currency::where('api_code_eur','EUR'.$currency->alphacode)->first();
                $rate=$currency_rate->rates;
            }
            $item->markup_converted = $markup_converted;
            $item->rate = $rate;
        }


        $view = \View::make('quotes.pdf.index-new', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);




        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);

        return $pdf->stream('quote_'.$quote->id.'.pdf');
    }

    public function send_pdf_quote(Request $request)
    {
        // set API Endpoint and access key (and any options of your choice)
        $endpoint = 'live';
        $access_key = 'a0a9f774999e3ea605ee13ee9373e755';

        $quote = Quote::findOrFail($request->id);
        $contact_email = Contact::find($quote->contact_id);
        $companies = Company::all()->pluck('business_name','id');
        $harbors = Harbor::all()->pluck('name','id');
        $origin_harbor = Harbor::where('id',$quote->origin_harbor_id)->first();
        $destination_harbor = Harbor::where('id',$quote->destination_harbor_id)->first();
        $prices = Price::all()->pluck('name','id');
        $contacts = Contact::where('company_id',$quote->company_id)->pluck('first_name','id');
        $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
        $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
        $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
        $user = User::where('id',\Auth::id())->with('companyUser')->first();
        $package_loads = PackageLoad::where('quote_id',$request->id)->get();
        if(\Auth::user()->company_user_id){
            $company_user=CompanyUser::find(\Auth::user()->company_user_id);
            $type=$company_user->type_pdf;
            $ammounts_type=$company_user->pdf_ammounts;
            $currency_cfg = Currency::find($company_user->currency_id);
            $port_all = harbor::where('name','ALL')->first();
            $terms_all = TermsPort::where('port_id',$port_all->id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
            $terms_origin = TermsPort::where('port_id',$quote->origin_harbor_id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
            $terms_destination = TermsPort::where('port_id',$quote->destination_harbor_id)->with('term')->whereHas('term', function($q)  {
                $q->where('termsAndConditions.company_user_id',\Auth::user()->company_user_id);
            })->get();
        }

        foreach($origin_ammounts as $item){
            $currency=Currency::find($item->currency_id);
            // Initialize CURL:
            $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&source='.$currency->alphacode);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $exchangeRates = json_decode($json, true);

            if($quote->currencies->alphacode=='USD'){
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'USD'];
            }else{
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'EUR'];
            }
            $item->markup_converted = $markup_converted;
        }

        foreach($freight_ammounts as $item){
            $currency=Currency::find($item->currency_id);
            // Initialize CURL:
            $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&source='.$currency->alphacode);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $exchangeRates = json_decode($json, true);

            if($quote->currencies->alphacode=='USD'){
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'USD'];
            }else{
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'EUR'];
            }
            $item->markup_converted = $markup_converted;
        }

        //dd(json_encode($item->markup/1.16));

        foreach($destination_ammounts as $item){
            $currency=Currency::find($item->currency_id);
            // Initialize CURL:
            $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&source='.$currency->alphacode);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            // Decode JSON response:
            $exchangeRates = json_decode($json, true);

            if($quote->currencies->alphacode=='USD'){
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'USD'];
            }else{
                $markup_converted=$item->markup/$exchangeRates['quotes'][$currency->alphacode.'EUR'];
            }
            $item->markup_converted = $markup_converted;
        }

        if($quote->pdf_language!=''){
            if($quote->pdf_language==3){
                $view = \View::make('quotes.pdf.index-portuguese', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else if($quote->pdf_language==2){
                $view = \View::make('quotes.pdf.index-spanish', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else{
                $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }
        }else if($quote->company->pdf_language!=''){
            if($quote->company->pdf_language==3){
                $view = \View::make('quotes.pdf.index-portuguese', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else if($quote->company->pdf_language==2){
                $view = \View::make('quotes.pdf.index-spanish', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else{
                $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }
        }else{
            if($company_user->pdf_language==1){
                $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else if($company_user->pdf_language==2){
                $view = \View::make('quotes.pdf.index-spanish', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else if($company_user->pdf_language==3){
                $view = \View::make('quotes.pdf.index-portuguese', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }else{
                $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all,'charges_type'=>$type,'ammounts_type'=>$ammounts_type]);
            }
        }



        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->save('pdf/temp_'.$quote->id.'.pdf');

        $subject = $request->subject;
        $body = $request->body;
        $to = $request->to;

        if($to!=''){
            $explode=explode(';',$to);
            foreach($explode as $item) {
                $send_quote = new SendQuote();
                $send_quote->to = trim($item);
                $send_quote->from = \Auth::user()->email;
                $send_quote->subject = $subject;
                $send_quote->body = $body;
                $send_quote->quote_id = $quote->id;
                $send_quote->status = 0;
                $send_quote->save();
            }
        }else{
            $send_quote = new SendQuote();
            $send_quote->to = $contact_email->email;
            $send_quote->from = \Auth::user()->email;
            $send_quote->subject = $subject;
            $send_quote->body = $body;
            $send_quote->quote_id = $quote->id;
            $send_quote->status = 0;
            $send_quote->save();
        }
        //SendQuotes::dispatch($subject,$body,$to,$quote,$contact_email->email);

        $quote->status_quote_id=2;
        $quote->update();
        return response()->json(['message' => 'Ok']);

        /*if(count($contact_email)>0) {
    
                $subject = $request->subject;
                $body = $request->body;
                $to = $request->to;
                if($to!=''){
                    \Mail::to($to)->bcc(\Auth::user()->email,\Auth::user()->name)->send(new SendQuotePdf($subject,$body,$quote));
                }else{
                    \Mail::to($contact_email->email)->bcc(\Auth::user()->email,\Auth::user()->name)->send(new SendQuotePdf($subject,$body,$quote));
                }
    
                $quote->status_quote_id=2;
                $quote->update();
                return response()->json(['message' => 'Ok']);
            }else{
                return response()->json(['message' => 'Error']);
            }*/
        }

        public function test(){
            $apiClient = new Api2Pdf('8a8fd7ad-0bca-4130-949c-5b4f22003fba');
            $apiClient->setInline(true);
            $apiClient->setFilename('test.pdf');
            $apiClient->setOptions(
                [
                    'orientation' => 'landscape', 
                    'pageSize'=> 'A4'
                ]
            );
            $result = $apiClient->wkHtmlToPdfFromUrl('https://app.cargofive.com/v2/quotes/show/oW');
            dd($result);
        }
    }
