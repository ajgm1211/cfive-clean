<?php

namespace App\Http\Controllers;

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

class PdfController extends Controller
{
    public function quote($id)
    {
        // set API Endpoint and access key (and any options of your choice)
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

        $view = \View::make('quotes.pdf.index', ['quote'=>$quote,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all]);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);

        return $pdf->stream('quote');
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
        $view = \View::make('quotes.pdf.index', ['companies' => $companies,'quote'=>$quote,'harbors'=>$harbors,
                                                 'prices'=>$prices,'contacts'=>$contacts,'origin_harbor'=>$origin_harbor,'destination_harbor'=>$destination_harbor,
                                                 'origin_ammounts'=>$origin_ammounts,'freight_ammounts'=>$freight_ammounts,'destination_ammounts'=>$destination_ammounts,'user'=>$user,'currency_cfg'=>$currency_cfg,'package_loads'=>$package_loads,'terms_origin'=>$terms_origin,'terms_destination'=>$terms_destination,'terms_all'=>$terms_all]);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view)->save('pdf/temp_'.$quote->id.'.pdf');

        if(count($contact_email)>0) {

            $subject = $request->subject;
            $body = $request->body;

            \Mail::to($contact_email->email)->send(new SendQuotePdf($subject,$body,$quote));

            $quote->status_quote_id=2;
            $quote->update();
            return response()->json(['message' => 'Ok']);
        }else{
            return response()->json(['message' => 'Error']);
        }
    }
}
