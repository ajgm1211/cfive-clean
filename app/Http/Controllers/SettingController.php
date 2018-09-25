<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyUser;
use App\Currency;
use App\User;
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
}
