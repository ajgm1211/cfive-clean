<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NewContractRequest;
use App\User;
use App\CompanyUser;
use App\Harbor;
use App\Carrier;
use App\Notifications\N_general;
use Illuminate\Support\Facades\Storage;
use App\Mail\NewRequestToAdminMail;
use App\Mail\RequestToUserMail;

class NewContractRequestsController extends Controller
{


    public function index()
    {
        $Ncontracts = NewContractRequest::with('user','companyuser')->orderBy('id', 'desc')->get();
        //dd($Ncontracts);
        return view('contracts.Requests.index',compact('Ncontracts'));
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //dd($request->all());

        $time   = new \DateTime();
        $now    = $time->format('dmY_His');
        $now2   = $time->format('Y-m-d');
        $file   = $request->file('file');
        $ext    = strtolower($file->getClientOriginalExtension());
        $validator = \Validator::make(
            array('ext' => $ext),
            array('ext' => 'in:xls,xlsx,csv')
        );

        if ($validator->fails()) {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
            return redirect()->route('Requestimporfcl');
        }
        //obtenemos el nombre del archivo
        $nombre = $file->getClientOriginalName();
        $nombre = $now.'_'.$nombre;
        \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));

        $typeVal = 1;
        $arreglotype = '';

        if($request->type == 2){
            // Rate And Surcharger 
            $typeVal    = 2;
            $type = array('type'=>$typeVal,'values'=>$request->valuesCurrency);
        } else {
            $type = array('type'=>$typeVal);
            $arreglotype = '"type":'.$typeVal;
        }

        $origin  = [];
        $destiny = [];
        $carrier = [];    

        $DatOriBol = false;
        $DatDesBol = false;
        $DatCarBol = false;

        if($request->DatOri == true){
            $origin = $request->origin;
            $DatOriBol = true;
        } 

        if($request->DatDes == true){
            $destiny = $request->destiny;
            $DatDesBol = true;
        } 

        if($request->DatCar == true){
            $carrier = $request->carrier;
            $DatCarBol = true;
        } 

        $data = array('DatOri'  => $DatOriBol,
                      'origin'  => $origin,
                      'DatDes'  => $DatDesBol,
                      'destiny' => $destiny,
                      'DatCar'  => $DatCarBol,
                      'carrier' => $carrier
                     );
        $type         = json_encode($type);
        $data         = json_encode($data);

        $Ncontract  = new NewContractRequest();
        $Ncontract->namecontract    = $request->name;
        $Ncontract->numbercontract  = $request->number;
        $Ncontract->validation      = $request->validation_expire;
        $Ncontract->company_user_id = $request->CompanyUserId;
        $Ncontract->namefile        = $nombre;
        $Ncontract->user_id         = $request->user;
        $Ncontract->created         = $now2;
        $Ncontract->type            = $type;
        $Ncontract->data            = $data;
        $Ncontract->save();
        $user = User::find($request->user);
        $admins = User::where('type','admin')->get();
        $message = 'has created an new request: '.$Ncontract->id;
        foreach($admins as $userNotifique){
            \Mail::to($userNotifique->email)->send(new NewRequestToAdminMail($userNotifique->toArray(),
                                                                             $user->toArray(),
                                                                             $Ncontract->toArray()));
            $userNotifique->notify(new N_general($user,$message));
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Your request was created');
        return redirect()->route('contracts.index');
    }

    //Para descargar el archivo
    public function show($id)
    {
        $Ncontract = NewContractRequest::find($id);
        $time       = new \DateTime();
        $now        = $time->format('d-m-Y_s');
        $company    = CompanyUser::find($Ncontract->company_user_id);
        $extObj     = new \SplFileInfo($Ncontract->namefile);
        $ext        = $extObj->getExtension();
        $name       = $company->name.'_'.$now.'.'.$ext;
        return Storage::download($Ncontract->namefile,$name);
    }



    public function edit($id)
    {
        $Ncontracts = NewContractRequest::with('companyuser','user')->find($id);
        //dd($Ncontracts);
        $type = json_decode($Ncontracts->type);
        $data = json_decode($Ncontracts->data);

        //dd($data);

        $surchargeBol       = false;
        $rateBol            = false; 
        $ValuesSomeBol      = false; 
        $ValuesWithCurreBol = false; 
        $ValCarrierBol      = false; 
        $ValuesDestinyBol   = false; 
        $ValuesOriginBol    = false; 
        $tarjetBol          = false; 

        $contenSurchar          = '';
        $contenRate             = '';
        $contenValuesSome       = '';
        $contenValuesWithCurre  = '';
        $contenValuesCarrier    = '';
        $contenValuesDestiny    = '';
        $contenValuesOrigin     = '';

        //dd($type);
        if($type->type == 2){
            $surchargeBol = true;
            $contenSurchar = 'El archivo contiene Rates + Surchargers';

            if($type->values == 1){
                $contenValuesSome = 'Las columnas valores solo contiene los valores';
                $ValuesSomeBol = true;

            } else if($type->values == 2){
                $contenValuesWithCurre = 'Las columnas de los valores, contienen los currency';
                $ValuesWithCurreBol = true;
            }
        } else if($type->type == 1){
            $rateBol = true;
            $contenRate = 'El archivo contiene solo Rates';
        }

        if($data->DatCar){
            $ValCarrierBol = true;
            $carrierObj = Carrier::find($data->carrier);
            $contenValuesCarrier = 'El archivo no contiene la columna Carrier. Carrier: '.$carrierObj->name;
        }

        if($data->DatDes){
            $ValuesDestinyBol = true;
            $destinos ='';
            foreach($data->destiny as $destiny){
                $destinosObj = Harbor::find($destiny);
                $destinos  = $destinos.$destinosObj->display_name.'.. ';
            }
            $contenValuesDestiny = 'El archivo no contiene la columna Destino. Destino: '.$destinos;
        }

        if($data->DatOri){
            $ValuesOriginBol = true;
            $origenes ='';
            foreach($data->origin as $origen){
                $origenObj = Harbor::find($origen);
                $origenes  = $origenes.''.$origenObj->display_name.'...  ';
            }
            $contenValuesOrigin = 'El archivo no contiene la columna Origen. Origen: '.$origenes;
        }

        if($ValuesOriginBol == true || $ValuesDestinyBol == true || $ValCarrierBol == true){
            $tarjetBol = true;
        }

        $colectionFinal = collect([]);

        $Contenido = [
            'namecontract'          => $Ncontracts->namecontract,
            'numbercontract'        => $Ncontracts->numbercontract,
            'validation'            => $Ncontracts->validation,
            'company'               => $Ncontracts->companyuser->name,
            'status'                => $Ncontracts->status,
            'User'                  => $Ncontracts->user->name.' '.$Ncontracts->user->lastname,
            'created'               => $Ncontracts->created,

            'surchargeBol'          => $surchargeBol,
            'contenSurchar'         => $contenSurchar,
            'rateBol'               => $rateBol,
            'contenRate'            => $contenRate,

            'ValuesSomeBol'         => $ValuesSomeBol,
            'contenValuesSome'      => $contenValuesSome,
            'ValuesWithCurreBol'    => $ValuesWithCurreBol,
            'contenValuesWithCurre' => $contenValuesWithCurre,

            'ValCarrierBol'         => $ValCarrierBol,
            'contenValuesCarrier'   => $contenValuesCarrier,
            'ValuesDestinyBol'      => $ValuesDestinyBol,
            'contenValuesDestiny'   => $contenValuesDestiny,

            'ValuesOriginBol'       => $ValuesOriginBol,
            'contenValuesOrigin'    => $contenValuesOrigin,
            'tarjetBol'             => $tarjetBol

        ];

        $colectionFinal->push($Contenido);
        //dd($ColectionFinal);

        return view('contracts.Requests.DetailNewRequest',compact('colectionFinal'));
    }


    public function update(Request $request, $id)
    {
        //
    }

    public function UpdateStatusRequest(){
        $id     = $_REQUEST['id'];
        $status = $_REQUEST['status'];

        try {
            $Ncontract = NewContractRequest::find($id);
            $Ncontract->status = $status;
            $Ncontract->save();

            if($Ncontract->status == 'Done'){

                $users = User::all()->where('company_user_id','=',$Ncontract->company_user_id);
                $message = 'The request was processed N°: ' . $Ncontract->id;
                foreach ($users as $user) {

                    $user->notify(new N_general(\Auth::user(),$message));
                }

                $usersCompa = User::all()->where('type','=','company')->where('company_user_id','=',$Ncontract->company_user_id);
                foreach ($usersCompa as $userCmp) {
                    if($userCmp->id != $Ncontract->user_id){
                        \Mail::to($userCmp->email)->send(new RequestToUserMail($userCmp->toArray(),
                                                                               $Ncontract->toArray()));
                    }
                }

                $usercreador = User::find($Ncontract->user_id);

                \Mail::to($usercreador->email)->send(new RequestToUserMail($usercreador->toArray(),
                                                                           $Ncontract->toArray()));

            }

            return response()->json($data=['status'=>1,'data'=>$status]);
        } catch (\Exception $e){
            return response()->json($data=['status'=>2]);;
        }

    }

    public function destroy($id)
    {
        //
    }
}
