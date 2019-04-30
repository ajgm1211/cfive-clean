<?php

namespace App\Http\Controllers;

use App\User;
use App\Harbor;
use App\Carrier;
use App\Contract;
use App\Direction;
use EventIntercom;
use \Carbon\Carbon;
use App\CompanyUser;
use App\RequetsCarrierFcl;
use App\NewContractRequest;
use Illuminate\Http\Request;
use App\Mail\RequestToUserMail;
use App\Notifications\N_general;
use Yajra\Datatables\Datatables;
use App\Jobs\ProcessContractFile;
use Illuminate\Support\Facades\DB;
use App\Mail\NewRequestToAdminMail;
use App\Jobs\SendEmailRequestFclJob;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SlackNotification;


class NewContractRequestsController extends Controller
{


    public function index()
    {
        return view('Requests.index');
    }


    public function create()
    {
        /*$Ncontracts = NewContractRequest::with('user','companyuser','Requestcarriers.carrier','direction')->orderBy('id', 'desc')->get();*/
        
        $Ncontracts = DB::select('call  select_request_fcl()');
//        dd($Ncontracts);
        //dd($Ncontracts[0]['Requestcarriers']->pluck('carrier')->pluck('name'));

        return Datatables::of($Ncontracts)
            ->addColumn('Company', function ($Ncontracts) {
                return $Ncontracts->company_user;
            })
            ->addColumn('name', function ($Ncontracts) {
                return $Ncontracts->namecontract;
            })
            ->addColumn('number', function ($Ncontracts) {
                return $Ncontracts->numbercontract;
            })
            ->addColumn('direction', function ($Ncontracts) {
                if(empty($Ncontracts->direction) == true){
                    return " -------- ";
                }else {
                    return $Ncontracts->direction;
                }
            })
            ->addColumn('carrier', function ($Ncontracts) {
                if(count($Ncontracts->carriers) >= 1){
                    return $Ncontracts->carriers;
                } else {
                    return " -------- ";
                }
            })
            ->addColumn('validation', function ($Ncontracts) {
                return $Ncontracts->validation;
            })
            ->addColumn('date', function ($Ncontracts) {
                return $Ncontracts->created;
            })
            ->addColumn('user', function ($Ncontracts) {
                return $Ncontracts->user;
            })
            ->addColumn('time_elapsed', function ($Ncontracts) {
                if(empty($Ncontracts->time_elapsed) != true){
                    return $Ncontracts->time_elapsed;
                } else {
                    return '--------';
                }
            })
            ->addColumn('status', function ($Ncontracts) {
                $color='';
                if(strnatcasecmp($Ncontracts->status,'Pending')==0){
                    //$color = 'color:#031B4E';
                    $color = 'color:#f81538';
                } else if(strnatcasecmp($Ncontracts->status,'Processing')==0){
                    $color = 'color:#5527f0';
                } else {
                    $color = 'color:#04950f';
                }

                return '<a href="#" onclick="showModal('.$Ncontracts->id.')"style="'.$color.'">'.$Ncontracts->status.'</a>
                &nbsp;
                <samp class="la la-pencil-square-o" for="" style="font-size:15px;'.$color.'"></samp>';
            })
            ->addColumn('action', function ($Ncontracts) {
                return '
                <a href="/Importation/RequestProccessFCL/'.$Ncontracts->id.'" title="Proccess FCL Request">
                    <samp class="la la-cogs" style="font-size:20px; color:#031B4E"></samp>
                </a>
                &nbsp;&nbsp;
                <a href="/Requests/RequestImportation/'.$Ncontracts->id.'" title="Download File">
                    <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E"></samp>
                </a>
                &nbsp;&nbsp;
                <a href="#" class="eliminarrequest" data-id-request="'.$Ncontracts->id.'" data-info="id:'.$Ncontracts->id.' Number Contract: '.$Ncontracts->numbercontract.'"  title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>';
            })

            ->make();
    }

    public function indexListClient(){
        $company_userid = \Auth::user()->company_user_id;
        return view('Requests.indexClient',compact('company_userid'));
    }

    public function listClient($id){

        //dd('llega');
        $Ncontracts = NewContractRequest::where('company_user_id',$id)->orderBy('id', 'desc')->get();
        //dd($Ncontracts[0]['companyuser']['name']);

        return Datatables::of($Ncontracts)
            ->addColumn('name', function ($Ncontracts) {
                return $Ncontracts->namecontract;
            })
            ->addColumn('number', function ($Ncontracts) {
                return $Ncontracts->numbercontract;
            })
            ->addColumn('validation', function ($Ncontracts) {
                return $Ncontracts->validation;
            })
            ->addColumn('date', function ($Ncontracts) {
                return $Ncontracts->created;
            })

            ->addColumn('status', function ($Ncontracts) {
                $color='';
                if(strnatcasecmp($Ncontracts->status,'Pending')==0){
                    //$color = 'color:#031B4E';
                    $color = 'color:#f81538';
                } else if(strnatcasecmp($Ncontracts->status,'Processing')==0){
                    $color = 'color:#5527f0';
                } else {
                    $color = 'color:#04950f';
                }

                return '<label style="'.$color.'">'.$Ncontracts->status.'</label>';
            })
            ->addColumn('action', function ($Ncontracts) {
                return ' <a href="/Requests/RequestImportation/'.$Ncontracts->id.'" title="Download File">
                    <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E"></samp>
                </a>';
            })

            ->make();
    }

    public function store(Request $request)
    {
        //dd($request->all());
    }

    public function store2(Request $request)
    {
        //dd($request->all());
        $fileBoll = false;
        $time   = new \DateTime();
        $now    = $time->format('dmY_His');
        $now2   = $time->format('Y-m-d');
        $file   = $request->file('file');
        $ext    = strtolower($file->getClientOriginalExtension());
        /* $validator = \Validator::make(
                array('ext' => $ext),
                array('ext' => 'in:xls,xlsx,csv')
            );
            if ($validator->fails()) {
                $request->session()->flash('message.nivel', 'danger');
                $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
                return redirect()->route('Requestimporfcl');
            }*/
        //obtenemos el nombre del archivo
        $nombre = $file->getClientOriginalName();
        $nombre = $now.'_'.$nombre;
        $fileBoll = \Storage::disk('FclRequest')->put($nombre,\File::get($file));
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
        if($fileBoll){
            $Ncontract  = new NewContractRequest();
            $Ncontract->namecontract    = $request->name;
            $Ncontract->validation      = $request->validation_expire;
            $Ncontract->direction_id    = $request->direction;
            $Ncontract->company_user_id = $request->CompanyUserId;
            $Ncontract->namefile        = $nombre;
            $Ncontract->user_id         = $request->user;
            $Ncontract->created         = $now2;
            $Ncontract->username_load   = 'Not assigned';
            $Ncontract->type            = $type;
            $Ncontract->data            = $data;
            $Ncontract->save();

            foreach($request->carrierM as $carrierVal){
                RequetsCarrierFcl::create([
                    'carrier_id' => $carrierVal,
                    'request_id' => $Ncontract->id
                ]);
            }

            ProcessContractFile::dispatch($Ncontract->id,$Ncontract->namefile,'fcl','request');
            $user = User::find($request->user);
            $message = "There is a new request from ".$user->name." - ".$user->companyUser->name;
            $user->notify(new SlackNotification($message));
            $admins = User::where('type','admin')->get();
            $message = 'has created an new request: '.$Ncontract->id;
            foreach($admins as $userNotifique){
                \Mail::to($userNotifique->email)->send(new NewRequestToAdminMail($userNotifique->toArray(),
                                                                                 $user->toArray(),
                                                                                 $Ncontract->toArray()));
                $userNotifique->notify(new N_general($user,$message));
            }
            //evento Intercom 
            $event = new  EventIntercom();
            $event->event_newRequest();
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.content', 'Your request was created');
            return redirect()->route('contracts.index');
            //return redirect()->route('RequestImportation.indexListClient');
        } else {
            $request->session()->flash('message.nivel', 'error');
            $request->session()->flash('message.content', 'Your request was not created');
            return redirect()->route('contracts.index');
        }
    }

    //Para descargar el archivo
    public function show($id)
    {
        $Ncontract = NewContractRequest::find($id);
        $time       = new \DateTime();
        $now        = $time->format('d-m-y');
        $company    = CompanyUser::find($Ncontract->company_user_id);
        $extObj     = new \SplFileInfo($Ncontract->namefile);
        $ext        = $extObj->getExtension();
        $name       = $Ncontract->id.'-'.$company->name.'_'.$now.'-FLC.'.$ext;
        try{
            return Storage::disk('s3_upload')->download('Request/FCL/'.$Ncontract->namefile,$name);
        } catch(\Exception $e){
            try{
                return Storage::disk('s3_upload')->download('contracts/'.$Ncontract->namefile,$name);
            } catch(\Exception $e){
                try{
                    return Storage::disk('FclRequest')->download($Ncontract->namefile,$name);
                } catch(\Exception $e){
                    return Storage::disk('UpLoadFile')->download($Ncontract->namefile,$name);
                }
            }
        }
    }

    public function showStatus($id){
        $requests = NewContractRequest::find($id);
        //dd($requests);
        return view('Requests.Body-Modals.edit',compact('requests'));
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

        return view('Requests.DetailNewRequest',compact('colectionFinal'));
    }


    public function update(Request $request, $id)
    {
        //
    }

    public function UpdateStatusRequest(){
        $id     = $_REQUEST['id'];
        $status = $_REQUEST['status'];
        // $id     = 1;
        // $status = 'Done';

        $time   = new \DateTime();
        $now2   = $time->format('Y-m-d H:i:s');

        try {
            $Ncontract = NewContractRequest::find($id);
            $Ncontract->status        = $status;
            $Ncontract->updated       = $now2;
            $Ncontract->username_load = \Auth::user()->name.' '.\Auth::user()->lastname;


            if($Ncontract->status == 'Processing'){
                if($Ncontract->time_star_one == false){
                    $Ncontract->time_star       = $now2;
                    $Ncontract->time_star_one   = true;
                }

            } elseif($Ncontract->status == 'Done'){

                $fechaEnd = Carbon::parse($now2);
                if(empty($Ncontract->time_star) == true){
                    $Ncontract->time_total = 'It did not go through the processing state';
                } else{
                    $fechaStar = Carbon::parse($Ncontract->time_star);
                    $Ncontract->time_total = str_replace('after','',$fechaEnd->diffForHumans($fechaStar));
                }

                if($Ncontract->sentemail == false){
                    $users = User::all()->where('company_user_id','=',$Ncontract->company_user_id);
                    $message = 'The request was processed N°: ' . $Ncontract->id;
                    foreach ($users as $user) {

                        $user->notify(new N_general(\Auth::user(),$message));
                    }

                    // Intercom SEARCH
                    $event = new  EventIntercom();
                    $event->event_requestDone($Ncontract->user_id);

                    $usercreador = User::find($Ncontract->user_id);
                    $message = "The importation ".$Ncontract->id." was completed";
                    $usercreador->notify(new SlackNotification($message));
                    SendEmailRequestFclJob::dispatch($usercreador->toArray(),$id);

                }
            }

            $Ncontract->save();
            return response()->json($data=['status'=>1,'data'=>$status]);
        } catch (\Exception $e){
            return response()->json($data=['status'=>2]);;
        }

    }

    public function destroy($id)
    {
        return 1;
    }

    public function destroyRequest($id)
    {
        try{
            $Ncontract = NewContractRequest::find($id);
            Storage::disk('FclRequest')->delete($Ncontract->namefile);
            $Ncontract->delete();
            return 1;
        } catch(\Exception $e){
            return 2;
        }
    }

    // New Request Importation ----------------------------------------------------------
    public function LoadViewRequestImporContractFcl(){
        $harbor         = harbor::all()->pluck('display_name','id');
        $carrier        = carrier::all()->pluck('name','id');
        $direction      = Direction::all()->pluck('name','id');
        $user           = \Auth::user();
        return view('Requests.NewRequest',compact('harbor','carrier','user','direction'));
    }
    
    // Similar Contracts ----------------------------------------------------------------
    
    public function similarcontracts(Request $request,$id){
        $contracts = Contract::select(['name','number','company_user_id','account_id','validity','expire']);

        //return Datatables::of($contracts->where('company_user_id',$id))
        return Datatables::of($contracts->where('company_user_id',$id))
        ->filter(function ($query) use ($request,$id) {
            if ($request->has('name')) {
                $query->where('name', 'like', "%{$request->get('name')}%");
            }

            if ($request->has('carrier')) {
                $query->where('carrier_id', 'like', "%{$request->get('email')}%");
            }
        })
        ->make(true);
    }
        
    // TEST Request Importation ----------------------------------------------------------
    public function test(){
        $fecha_actual = date("Y-m-d H:i:s");
        /*$fecha1 = new \DateTime("2019-04-15 14:26:47");
        $fecha2 = new \DateTime($fecha_actual);
        $tiempo_transcurrido = $fecha1->diff($fecha2);*/

        $fechaExpiracion = Carbon::parse($fecha_actual);
        //$fechaEmision = Carbon::parse("2019-04-15 14:26:47");
        $fechaEmision = Carbon::parse($fecha_actual);

        $diasDiferencia = $fechaExpiracion->diffForHumans($fechaEmision);
        dd(str_replace('after','',$diasDiferencia));
    }
}
