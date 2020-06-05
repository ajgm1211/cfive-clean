<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyUser;
use App\Currency;
use App\User;
use App\Quote;
use App\Surcharge;
use App\Contact;
use App\SaleTerm;
use App\Harbor;
use App\Airport;
use App\Country;
use App\Price;
use App\Contract;
use App\GlobalCharge;
use App\Inland;
use App\OriginAmmount;
use App\FreightAmmount;
use App\EmailSetting;
use App\DestinationAmmount;
use App\PackageLoad;
use App\NewContractRequest;
use App\TermAndCondition;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Input;
use App\Jobs\ProcessLogo;
use EventCrisp;

class SettingController extends Controller
{
  public function index()
  {
    $email_settings='';
    $company = User::where('id',\Auth::id())->with('companyUser')->first();
    if($company->companyUser){
      $email_settings = EmailSetting::where('company_user_id',$company->companyUser->id)->first();
      if($company->companyUser->decimals == '1'){
        $selectedTrue="checked='true'";
        $selectedFalse='';
      }else{
        $selectedTrue='';
        $selectedFalse="checked='true'";
  
      }
    }

    
    
    $currencies = Currency::where('alphacode','=','USD')->orwhere('alphacode','=','EUR')->pluck('alphacode','id');

    return view('settings/index',compact('company','currencies','email_settings','selectedTrue','selectedFalse'));
  }

  public function idPersonalizado($name,$company_id){
    $iniciales =  strtoupper(substr($name,0, 2));
    $quote = Quote::where('company_user_id',$company_id)->orderBy('created_at', 'desc')->first();

    if($quote == null){
      $iniciales = $iniciales."-1";
    }else{
      $numeroFinal = explode('-',$quote->company_quote);

      $numeroFinal = $numeroFinal[1] +1;

      $iniciales = $iniciales."-".$numeroFinal;
    }
    return $iniciales;
  }

  public function store(Request $request){

    $file = Input::file('image');
    $footer_image = Input::file('footer_image');
    $signature_image = Input::file('email_signature_image');
    $filepath = '';
    $filepath_footer_image = '';
    $filepath_signature_image = '';
    if($file != ""){
      $filepath = 'Logos/Companies/'.$file->getClientOriginalName();
      $name     = $file->getClientOriginalName();
      \Storage::disk('logos')->put($name,file_get_contents($file));
      $s3 = \Storage::disk('s3_upload');
      $s3->put($filepath, file_get_contents($file), 'public');
      //ProcessLogo::dispatch(auth()->user()->id,$filepath,$name,1);
    }
    if($footer_image != ""){
      $filepath_footer_image = 'Footer/'.$footer_image->getClientOriginalName();
      $name_footer_image = $footer_image->getClientOriginalName();
      \Storage::disk('logos')->put($name_footer_image,file_get_contents($footer_image));
      $s3 = \Storage::disk('s3_upload');
      $s3->put($filepath_footer_image, file_get_contents($footer_image), 'public');
      //ProcessLogo::dispatch(auth()->user()->id,$filepath,$name,1);
    }
    if($signature_image != ""){
      $filepath_signature_image = 'Email/'.$signature_image->getClientOriginalName();
      $name_sign_image = $signature_image->getClientOriginalName();
      \Storage::disk('logos')->put($name_sign_image,file_get_contents($signature_image));
      $s3 = \Storage::disk('s3_upload');
      $s3->put($filepath_signature_image, file_get_contents($signature_image), 'public');
      //ProcessLogo::dispatch(auth()->user()->id,$filepath,$name,1);
    }
    if($request->decimals)
      $decimals = 1;
    else
      $decimals = 0;
    if(!$request->company_id){
      //$company=CompanyUser::create($request->all());
      $company = new CompanyUser();
      $company->name = $request->name;
      $company->address = $request->address;
      $company->decimals = $decimals;
      $company->phone = $request->phone;
      $company->currency_id = $request->currency_id;
      $company->hash = \Hash::make($request->name);
      $company->pdf_language = $request->pdf_language;
      $company->footer_type = $request->footer_type;
      $company->footer_text = $request->footer_text_content;
      if($footer_image!=""){
        $company->footer_image = $filepath_footer_image;   
      }
      $company->type_pdf = 2;
      $company->pdf_ammounts = 2;
      if($file != ""){
        $company->logo = $filepath;
      }
      $company->save();

      User::where('id',\Auth::id())->update(['company_user_id'=>$company->id]);
      $usuario = User::find(\Auth::id());
      //Crisp Update 

      $CrispClient = new EventCrisp();
      $params = array('company' => array('name'=>$company->name ));
      $people = $CrispClient->updateProfile($params,$usuario->email);




      $email_settings = new EmailSetting();
      $email_settings->company_user_id = $company->id;
      $email_settings->email_from = $request->email_from_format;
      $email_settings->email_signature_type = $request->email_signature_type;
      $email_settings->email_signature_text = $request->signature_text_content;
      if($signature_image!=""){
        $email_settings->email_signature_image = $filepath_signature_image;
      }
      $email_settings->save();
    }else{
      $company=CompanyUser::findOrFail($request->company_id);
      $company->name=$request->name;
      $company->phone=$request->phone;
      $company->address=$request->address;
      $company->decimals = $decimals;
      $company->currency_id=$request->currency_id;
      $company->pdf_language = $request->pdf_language;
      $company->footer_type = $request->footer_type;
      $company->footer_text = $request->footer_text_content;
      if($footer_image!=""){
        $company->footer_image = $filepath_footer_image;   
      }
      if($file != ""){
        $company->logo = $filepath;
      }
      $company->update();

      $email_settings = EmailSetting::where('company_user_id',$request->company_id)->first();
      if($email_settings){
        $email_settings->email_from = $request->email_from_format;
        $email_settings->email_signature_type = $request->email_signature_type;
        $email_settings->email_signature_text = $request->signature_text_content;
        if($signature_image!=""){
          $email_settings->email_signature_image = $filepath_signature_image;
        }            
        $email_settings->update();
      }else{
        $email_settings = new EmailSetting();
        $email_settings->company_user_id = $company->id;
        $email_settings->email_from = $request->email_from_format;
        $email_settings->email_signature_type = $request->email_signature_type;
        $email_settings->email_signature_text = $request->signature_text_content;
        if($signature_image!=""){
          $email_settings->email_signature_image = $filepath_signature_image;
        }
        $email_settings->save();
      }
    }
    return response()->json(['message' => 'Ok']);
  }

  public function update_pdf_type(Request $request)
  {
    $company=CompanyUser::find(\Auth::user()->company_user_id);
    $company->type_pdf = $request->pdf_type;
    $company->update();

    return response()->json(['message' => 'Ok']);
  }

  public function update_pdf_ammount(Request $request)
  {
    $company=CompanyUser::find(\Auth::user()->company_user_id);
    $company->pdf_ammounts = $request->pdf_ammounts;
    $company->update();

    return response()->json(['message' => 'Ok']);
  }

  public function update_pdf_language(Request $request)
  {
    $quote=Quote::find($request->quote_id);
    $quote->pdf_language = $request->pdf_language;
    $quote->update();

    return response()->json(['message' => 'Ok']);
  }

  public function list_companies()
  {
    $companies=CompanyUser::all();

    return view('settings/list_companies',compact('companies'));
  }

  public function delete_company_user(Request $request,$id)
  {
    Quote::where('company_user_id',$id)->delete();
    Company::where('company_user_id',$id)->delete();
    User::where('company_user_id',$id)->delete();
    Surcharge::where('company_user_id',$id)->delete();
    SaleTerm::where('company_user_id',$id)->delete();
    Price::where('company_user_id',$id)->delete();
    Contract::where('company_user_id',$id)->delete();
    GlobalCharge::where('company_user_id',$id)->delete();
    Inland::where('company_user_id',$id)->delete();
    NewContractRequest::where('company_user_id',$id)->delete();
    TermAndCondition::where('company_user_id',$id)->delete();
    CompanyUser::where('id',$id)->delete();

    return response()->json(['message' => 'Ok']);

  }

  public function duplicate(Request $request)
  {
    //$id = obtenerRouteKey($id);
    $company_user = CompanyUser::findOrFail($request->company_user_id);

    $company_user_duplicate = new CompanyUser();
    $company_user_duplicate->name=$request->name;
    $company_user_duplicate->address=$request->address;
    $company_user_duplicate->phone=$request->phone;
    $company_user_duplicate->logo=$company_user->logo;
    $company_user_duplicate->hash=\Hash::make($request->name.'_duplicate');
    $company_user_duplicate->currency_id=$request->currency_id;
    $company_user_duplicate->pdf_language=$request->pdf_language;
    $company_user_duplicate->type_pdf=$company_user->type_pdf;
    $company_user_duplicate->pdf_ammounts=$company_user->pdf_ammounts;
    $company_user_duplicate->save();

    $quotes = Quote::where('company_user_id',$company_user->id)->get();

    $companies = Company::where('company_user_id',$company_user->id)->get();
    $contracts = Contract::where('company_user_id',$company_user->id)->get();
    $surcharges = Surcharge::where('company_user_id',$company_user->id)->get();
    $terms = TermAndCondition::where('company_user_id',$company_user->id)->get();
    $custom_id =0;
    $harbors = Harbor::all()->pluck('name','id');
    $countries = Country::all()->pluck('name','id');

    $user = new User();
    $user->name='Admin_'.$company_user_duplicate->name;
    $user->lastname='Admin_'.$company_user_duplicate->name;
    $user->email=$company_user_duplicate->name.'@example.com';
    $user->phone='1234567890';
    $user->password=bcrypt('secret');
    $user->type='company';
    $user->verified=1;
    $user->state=1;
    $user->company_user_id=$company_user_duplicate->id;
    $user->save();

    foreach ($contracts as $contract){
      $contract_duplicate = new Contract();
      $contract_duplicate->name = $contract->name;
      $contract_duplicate->number = $contract->number;
      $contract_duplicate->validity = $contract->validity;
      $contract_duplicate->expire = $contract->expire;
      $contract_duplicate->status = $contract->status;
      $contract_duplicate->company_user_id = $company_user_duplicate->id;
      $contract_duplicate->save();
    }

    foreach ($surcharges as $surcharge){
      $surcharge_duplicate = new Surcharge();
      $surcharge_duplicate->name = $surcharge->name;
      $surcharge_duplicate->description = $surcharge->description;
      $surcharge_duplicate->sale_term_id = $surcharge->sale_term_id;
      $surcharge_duplicate->company_user_id = $company_user_duplicate->id;
      $surcharge_duplicate->save();
    }

    foreach ($terms as $term){
      $term_duplicate = new TermAndCondition();
      $term_duplicate->name = $term->name;
      $term_duplicate->user_id = $user->id;
      $term_duplicate->import = $term->import;
      $term_duplicate->export = $term->export;
      $term_duplicate->company_user_id = $company_user_duplicate->id;
      $term_duplicate->save();
    }

    foreach($companies as $company){
      $company_duplicate = new Company();
      $company_duplicate->business_name = $company->business_name;
      $company_duplicate->phone = $company->phone;
      $company_duplicate->address = $company->address;
      $company_duplicate->email = $company->email;
      $company_duplicate->tax_number = $company->tax_number;
      $company_duplicate->logo = $company->logo;
      $company_duplicate->associated_quotes = $company->associated_quotes;
      $company_duplicate->company_user_id = $company_user_duplicate->id;
      $company_duplicate->owner = $user->id;
      $company_duplicate->save();

      $contacts = Contact::where('company_id',$company->id)->get();

      foreach($contacts as $contact){
        $contact_duplicate = new Contact();
        $contact_duplicate->first_name = $contact->first_name;
        $contact_duplicate->last_name = $contact->last_name;
        $contact_duplicate->email = $contact->email;
        $contact_duplicate->phone = $contact->phone;
        $contact_duplicate->position = $contact->position;
        $contact_duplicate->company_id = $company_duplicate->id;
        $contact_duplicate->save();
      }
    }

    foreach($quotes as $quote){

      //Set custom quote id
      $custom_id_quote = $this->idPersonalizado($request->name,$request->company_user_id);
      $explode=explode('-',$custom_id_quote);
      $custom_id +=1;
      $company_quote = $explode[0]."-".$custom_id;

      $origin_ammounts = OriginAmmount::where('quote_id',$quote->id)->get();
      $freight_ammounts = FreightAmmount::where('quote_id',$quote->id)->get();
      $destination_ammounts = DestinationAmmount::where('quote_id',$quote->id)->get();
      $packaging_loads = PackageLoad::where('quote_id',$quote->id)->get();

      $quote_duplicate = new Quote();
      $quote_duplicate->owner=$user->id;
      $quote_duplicate->company_user_id=$company_user_duplicate->id;
      $quote_duplicate->company_quote=$company_quote;
      $quote_duplicate->incoterm=$quote->incoterm;
      $quote_duplicate->modality=$quote->modality;
      $quote_duplicate->currency_id=$quote->currency_id;
      $quote_duplicate->pick_up_date=$quote->pick_up_date;
      if($quote->validity){
        $quote_duplicate->validity=$quote->validity;
      }
      if($quote->origin_address){
        $quote_duplicate->origin_address=$quote->origin_address;
      }
      if($quote->destination_address){
        $quote_duplicate->destination_address=$quote->destination_address;
      }
      if($quote->company_id){
        $quote_duplicate->company_id=$quote->company_id;
      }
      if($quote->origin_harbor_id){
        $quote_duplicate->origin_harbor_id=$quote->origin_harbor_id;
      }
      if($quote->destination_harbor_id){
        $quote_duplicate->destination_harbor_id=$quote->destination_harbor_id;
      }
      if($quote->origin_airport_id){
        $quote_duplicate->origin_airport_id=$quote->origin_airport_id;
      }
      if($quote->destination_airport_id){
        $quote_duplicate->destination_airport_id=$quote->destination_airport_id;
      }
      if($quote->price_id){
        $quote_duplicate->price_id=$quote->price_id;
      }
      if($quote->contact_id){
        $quote_duplicate->contact_id=$quote->contact_id;
      }
      if($quote->qty_20){
        $quote_duplicate->qty_20=$quote->qty_20;
      }
      if($quote->qty_40){
        $quote_duplicate->qty_40=$quote->qty_40;
      }
      if($quote->qty_40_hc){
        $quote_duplicate->qty_40_hc=$quote->qty_40_hc;
      }
      if($quote->qty_45_hc){
        $quote_duplicate->qty_45_hc=$quote->qty_45_hc;
      }
      if($quote->qty_40_nor){
        $quote_duplicate->qty_40_nor=$quote->qty_40_nor;
      }
      if($quote->qty_20_reefer){
        $quote_duplicate->qty_20_reefer=$quote->qty_20_reefer;
      }
      if($quote->qty_40_reefer){
        $quote_duplicate->qty_40_reefer=$quote->qty_40_reefer;
      }
      if($quote->qty_40_hc_reefer){
        $quote_duplicate->qty_40_hc_reefer=$quote->qty_40_hc_reefer;
      }
      if($quote->qty_20_open_top){
        $quote_duplicate->qty_20_open_top=$quote->qty_20_open_top;
      }
      if($quote->qty_40_open_top){
        $quote_duplicate->qty_40_open_top=$quote->qty_40_open_top;
      }
      if($quote->delivery_type){
        $quote_duplicate->delivery_type=$quote->delivery_type;
      }
      if($quote->sub_total_origin){
        $quote_duplicate->sub_total_origin=$quote->sub_total_origin;
      }
      if($quote->sub_total_freight){
        $quote_duplicate->sub_total_freight=$quote->sub_total_freight;
      }
      if($quote->sub_total_destination){
        $quote_duplicate->sub_total_destination=$quote->sub_total_destination;
      }
      if($quote->total_markup_origin){
        $quote_duplicate->total_markup_origin=$quote->total_markup_origin;
      }
      if($quote->total_markup_freight){
        $quote_duplicate->total_markup_freight=$quote->total_markup_freight;
      }
      if($quote->total_markup_destination){
        $quote_duplicate->total_markup_destination=$quote->total_markup_destination;
      }
      if($quote->carrier_id){
        $quote_duplicate->carrier_id=$quote->carrier_id;
      }
      if($quote->airline_id){
        $quote_duplicate->airline_id=$quote->airline_id;
      }
      $quote_duplicate->status_quote_id=$quote->status_quote_id;
      $quote_duplicate->type_cargo=$quote->type_cargo;
      $quote_duplicate->type=$quote->type;
      $quote_duplicate->save();
      foreach ($origin_ammounts as $origin){
        $origin_ammount_duplicate = new OriginAmmount();
        $origin_ammount_duplicate->charge=$origin->charge;
        $origin_ammount_duplicate->detail=$origin->detail;
        $origin_ammount_duplicate->units=$origin->units;
        $origin_ammount_duplicate->price_per_unit=$origin->price_per_unit;
        $origin_ammount_duplicate->markup=$origin->markup;
        $origin_ammount_duplicate->currency_id=$origin->currency_id;
        $origin_ammount_duplicate->total_ammount=$origin->total_ammount;
        if($origin->total_ammount_2){
          $origin_ammount_duplicate->total_ammount_2=$origin->total_ammount_2;
        }
        $origin_ammount_duplicate->quote_id=$quote_duplicate->id;
        $origin_ammount_duplicate->save();
      }
      foreach ($freight_ammounts as $freight){
        $freight_ammount_duplicate = new FreightAmmount();
        $freight_ammount_duplicate->charge=$freight->charge;
        $freight_ammount_duplicate->detail=$freight->detail;
        $freight_ammount_duplicate->units=$freight->units;
        $freight_ammount_duplicate->price_per_unit=$freight->price_per_unit;
        $freight_ammount_duplicate->markup=$freight->markup;
        $freight_ammount_duplicate->currency_id=$freight->currency_id;
        $freight_ammount_duplicate->total_ammount=$freight->total_ammount;
        if($freight->total_ammount_2){
          $freight_ammount_duplicate->total_ammount_2=$freight->total_ammount_2;
        }
        $freight_ammount_duplicate->quote_id=$quote_duplicate->id;
        $freight_ammount_duplicate->save();
      }
      foreach ($destination_ammounts as $destination){
        $destination_ammount_duplicate = new DestinationAmmount();
        $destination_ammount_duplicate->charge=$destination->charge;
        $destination_ammount_duplicate->detail=$destination->detail;
        $destination_ammount_duplicate->units=$destination->units;
        $destination_ammount_duplicate->price_per_unit=$destination->price_per_unit;
        $destination_ammount_duplicate->markup=$destination->markup;
        $destination_ammount_duplicate->currency_id=$destination->currency_id;
        $destination_ammount_duplicate->total_ammount=$destination->total_ammount;
        if($destination->total_ammount_2){
          $destination_ammount_duplicate->total_ammount_2=$destination->total_ammount_2;
        }
        $destination_ammount_duplicate->quote_id=$quote_duplicate->id;
        $destination_ammount_duplicate->save();
      }

      foreach ($packaging_loads as $packaging_load){
        $packaging_load_duplicate = new PackageLoad();
        $packaging_load_duplicate->type_cargo=$packaging_load->type_cargo;
        $packaging_load_duplicate->quantity=$packaging_load->quantity;
        $packaging_load_duplicate->height=$packaging_load->height;
        $packaging_load_duplicate->width=$packaging_load->width;
        $packaging_load_duplicate->large=$packaging_load->large;
        $packaging_load_duplicate->weight=$packaging_load->weight;
        $packaging_load_duplicate->total_weight=$packaging_load->total_weight;
        $packaging_load_duplicate->volume=$packaging_load->volume;
        $packaging_load_duplicate->quote_id=$quote_duplicate->id;
        $packaging_load_duplicate->save();
      }

    }

    $request->session()->flash('message.nivel', 'success');
    $request->session()->flash('message.title', 'Well done!');
    $request->session()->flash('message.content', 'Company duplicated successfully!');
    return redirect()->back();
  }
}
