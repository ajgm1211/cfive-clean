<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyUser;
use App\Currency;
use App\User;
use App\Quote;
use App\Surcharge;
use App\SaleTerm;
use App\Price;
use App\Contract;
use App\GlobalCharge;
use App\Inland;
use App\NewContractRequest;
use App\TermAndCondition;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Input;

class SettingController extends Controller
{
    public function index()
    {
        $company = User::where('id',\Auth::id())->with('companyUser')->first();
        $currencies = Currency::where('alphacode','=','USD')->orwhere('alphacode','=','EUR')->pluck('alphacode','id');
        return view('settings/index',compact('company','currencies'));
    }

    public function store(Request $request){

        $file = Input::file('image');

        if($file != ""){
            //Creamos una instancia de la libreria instalada   
            $image = Image::make(Input::file('image'));
            //Ruta donde queremos guardar las imagenes
            $path = public_path().'/uploads/logos/';
            // Guardar Original
            //$image->save($path.$file->getClientOriginalName());
            // Cambiar de tamaño
            //$image->resize(300,500);
            // Guardar
            $image->save($path.$file->getClientOriginalName());
        }

        if(!$request->company_id){

            //$company=CompanyUser::create($request->all());

            $company = new CompanyUser();
            $company->name = $request->name;
            $company->address = $request->address;
            $company->phone = $request->phone;
            $company->currency_id = $request->currency_id;
            $company->hash = \Hash::make($request->name);
            $company->pdf_language = $request->pdf_language;
            $company->type_pdf = 2;
            $company->pdf_ammounts = 2;
            if($file != ""){
                $company->logo = 'uploads/logos/'.$file->getClientOriginalName();
            }
            $company->save();

            User::where('id',\Auth::id())->update(['company_user_id'=>$company->id]);

        }else{
            $company=CompanyUser::findOrFail($request->company_id);
            $company->name=$request->name;
            $company->phone=$request->phone;
            $company->address=$request->address;
            $company->currency_id=$request->currency_id;
            $company->pdf_language = $request->pdf_language;
            if($file != ""){
                $company->logo = 'uploads/logos/'.$file->getClientOriginalName();
            }
            $company->update();
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

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register completed successfully!');

        return view('settings/list_companies',compact('companies'));
    }
}
