<?php

namespace App\Http\Controllers;

use App\User;
use App\Harbor;
use App\Carrier;
use Carbon\Carbon;
use App\Direction;
use EventIntercom;
use App\ContractLcl;
use App\CompanyUser;
use App\RequetsCarrierLcl;
use App\ContractCarrierLcl;
use Illuminate\Http\Request;
use App\NewContractRequestLcl;
use App\Notifications\N_general;
use Yajra\Datatables\Datatables;
use App\Jobs\ProcessContractFile;
use Illuminate\Support\Facades\DB;
use App\Mail\RequestLclToUserMail;
use App\Jobs\SendEmailRequestLclJob;
use App\Mail\NewRequestLclToAdminMail;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SlackNotification;

class NewContractRequestLclController extends Controller
{

    public function index()
    {
        return view('RequestsLcl.index');
    }

    public function indexListClient(){
        $company_userid = \Auth::user()->company_user_id;
        return view('RequestsLcl.indexClient',compact('company_userid'));
    }

    //lista todos los request Admin
    public function create()
    {
        //$Ncontracts = NewContractRequestLcl::with('user','companyuser','Requestcarriers.carrier','direction')->orderBy('id', 'desc')->get();
        $Ncontracts = DB::select('call  select_request_lcl()');
        //dd($Ncontracts[0]['companyuser']['name']);

        return Datatables::of($Ncontracts)
            ->addColumn('Company', function ($Ncontracts) {
                return $Ncontracts->company_user;
            })
            ->addColumn('name', function ($Ncontracts) {
                return $Ncontracts->namecontract;
            })
            ->addColumn('direction', function ($Ncontracts) {
                if(empty($Ncontracts->direction) == true){
                    return " ---------- ";
                }else {
                    return $Ncontracts->direction;
                }
            })
            ->addColumn('carrier', function ($Ncontracts) {
                if(empty($Ncontracts->carriers) != true){
                    return $Ncontracts->carriers;
                } else {
                    return " ------------------ ";
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
                    return '------------------------';
                }
            })
            ->addColumn('status', function ($Ncontracts) {
                $color='';
                if(strnatcasecmp($Ncontracts->status,'Pending')==0){
                    //$color = 'color:#031B4E';
                    $color = 'color:#f81538';
                } else if(strnatcasecmp($Ncontracts->status,'Processing')==0){
                    $color = 'color:#5527f0';
                } else if(strnatcasecmp($Ncontracts->status,'Review')==0){
                    $color = 'color:#e07000';
                } else {
                    $color = 'color:#04950f';
                }

                return '<a href="#" onclick="showModal('.$Ncontracts->id.')"style="'.$color.'">'.$Ncontracts->status.'</a>
                &nbsp;
                <samp class="la la-pencil-square-o" for="" style="font-size:15px;'.$color.'"></samp>';
            })
            ->addColumn('action', function ($Ncontracts) {

                $buttons = '&nbsp;&nbsp;
                <a href="/RequestsLcl/RequestImportationLcl/'.$Ncontracts->id.'" title="Download File">
                    <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E"></samp>
                </a>
                &nbsp;&nbsp;
                <a href="#" class="eliminarrequest" data-id-request="'.$Ncontracts->id.'" data-info="id:'.$Ncontracts->id.' References: '.$Ncontracts->namecontract.'"  title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>';

                if(empty($Ncontracts->contract) != true){
                    $butPrCt = '
                <a href="/ImportationLCL/RequestProccessLCL/'.$Ncontracts->contract.'/2/'.$Ncontracts->id.'" title="Proccess LCL Contract">
                    <samp class="la la-cogs" style="font-size:20px; color:#04950f"></samp>
                </a>';
                    $buttons = $butPrCt . $buttons;
                } else {
                    $butPrRq = '
                <a href="/ImportationLCL/RequestProccessLCL/'.$Ncontracts->id.'/1/0" title="Proccess LCL Request">
                    <samp class="la la-cogs" style="font-size:20px; color:#D85F00"></samp>
                </a>';
                    $buttons = $butPrRq . $buttons;
                }

                return $buttons;
            })

            ->make();
    }

    //lista todos los request pero por compañia
    public function listClient($id)
    {
        $Ncontracts = NewContractRequestLcl::where('company_user_id',$id)->get();
        //dd($Ncontracts[0]['companyuser']['name']);

        return Datatables::of($Ncontracts)
            ->addColumn('Company', function ($Ncontracts) {
                return $Ncontracts->companyuser->name;
            })
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
            ->addColumn('updated', function ($Ncontracts) {
                if(empty($Ncontract->updated) != true){
                    return Carbon::parse($Ncontract->updated)->format('d-m-Y h:i:s');
                } else {
                    return '00-00-0000 00:00:00';
                }
            })
            ->addColumn('user', function ($Ncontracts) {
                return $Ncontracts->user->name.' '.$Ncontracts->user->lastname;
            })
            ->addColumn('status', function ($Ncontracts) {
                $color='';
                if(strnatcasecmp($Ncontracts->status,'Pending')==0){
                    //$color = 'color:#031B4E';
                    $color = 'color:#f81538';
                } else if(strnatcasecmp($Ncontracts->status,'Processing')==0){
                    $color = 'color:#5527f0';
                } else if(strnatcasecmp($Ncontracts->status,'Review')==0){
                    $color = 'color:#e07000';
                } else {
                    $color = 'color:#04950f';
                }

                return '<label style="'.$color.'">'.$Ncontracts->status.'</label>';
            })
            ->addColumn('action', function ($Ncontracts) {
                return '<a href="/RequestsLcl/RequestImportationLcl/'.$Ncontracts->id.'" title="Download File">
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
        $now2   = $time->format('Y-m-d H:i:s');
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
        $fileBoll = \Storage::disk('LclRequest')->put($nombre,\File::get($file));

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

            $direction_id   = $request->direction;
            $CompanyUserId  = $request->CompanyUserId;

            $contract     = new ContractLcl();
            $contract->name             = $request->name;
            $validity                   = explode('/',$request->validation_expire);
            $contract->validity         = $validity[0];
            $contract->expire           = $validity[1];
            $contract->status           = 'incomplete';
            $contract->comments         = 'Loaded from Request';
            $contract->company_user_id  = $CompanyUserId;
            $contract->direction_id     = $direction_id;
            $contract->save();

            $Contract_id = $contract->id;

            foreach($request->carrierM as $carrierVal){
                ContractCarrierLcl::create([
                    'carrier_id'    => $carrierVal,
                    'contract_id'   => $Contract_id
                ]);
            }

            $Ncontract  = new NewContractRequestLcl();
            $Ncontract->namecontract    = $request->name;
            $Ncontract->validation      = $request->validation_expire;
            $Ncontract->direction_id    = $direction_id;
            $Ncontract->company_user_id = $CompanyUserId;
            $Ncontract->namefile        = $nombre;
            $Ncontract->user_id         = $request->user;
            $Ncontract->created         = $now2;
            $Ncontract->type            = $type;
            $Ncontract->data            = $data;
            $Ncontract->contract_id     = $Contract_id;
            $Ncontract->save();

            foreach($request->carrierM as $carrierVal){
                RequetsCarrierLcl::create([
                    'carrier_id' => $carrierVal,
                    'request_id' => $Ncontract->id
                ]);
            }

            ProcessContractFile::dispatch($Ncontract->id,$Ncontract->namefile,'lcl','request');

            $user = User::find($request->user);
            $message = "There is a new request from ".$user->name." - ".$user->companyUser->name;
            $user->notify(new SlackNotification($message));
            $admins = User::where('type','admin')->get();
            $message = 'has created an new request: '.$Ncontract->id;
            foreach($admins as $userNotifique){
                \Mail::to($userNotifique->email)->send(new NewRequestLclToAdminMail($userNotifique->toArray(),
                                                                                    $user->toArray(),
                                                                                    $Ncontract->toArray()));
                $userNotifique->notify(new N_general($user,$message));
            }

            //evento Intercom 
            $event = new  EventIntercom();
            $event->event_newRequestLCL();

            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.content', 'Your request was created');
            return redirect()->route('contractslcl.index');

        } else {

            $request->session()->flash('message.nivel', 'error');
            $request->session()->flash('message.content', 'Your request was not created');
            return redirect()->route('contractslcl.index');

        }
    }

    public function showStatus($id){
        $requests = NewContractRequestLcl::find($id);
        //dd($requests);
        $status = $requests->status;
        $status_arr = [];
        if($status == 'Pending'){
            $status_arr['Pending'] = 'Pending';
            $status_arr['Processing'] = 'Processing';
        } elseif($status == 'Processing'){
            $status_arr['Processing'] = 'Processing';
            $status_arr['Review'] = 'Review';
        } elseif($status == 'Review' || $status == 'Done'){
            $status_arr['Processing'] = 'Processing';
            $status_arr['Review'] = 'Review';
            $status_arr['Done'] = 'Done';
        }
        return view('RequestsLcl.Body-Modals.edit',compact('requests','status_arr'));
    }

    //Para descargar el archivo
    public function show($id)
    {
        $Ncontract = NewContractRequestLcl::find($id);
        $time       = new \DateTime();
        $now        = $time->format('d-m-y');
        $company    = CompanyUser::find($Ncontract->company_user_id);
        $extObj     = new \SplFileInfo($Ncontract->namefile);
        $ext        = $extObj->getExtension();
        $name       = $Ncontract->id.'-'.$company->name.'_'.$now.'-LCL.'.$ext;

        try{
            return Storage::disk('s3_upload')->download('Request/LCL/'.$Ncontract->namefile,$name);
        } catch(\Exception $e){
            try{
                return Storage::disk('s3_upload')->download('contracts/'.$Ncontract->namefile,$name);
            } catch(\Exception $e){
                try{
                    return Storage::disk('LclRequest')->download($Ncontract->namefile,$name);
                } catch(\Exception $e){
                    return Storage::disk('UpLoadFile')->download($Ncontract->namefile,$name);
                }
            }
        }
    }


    public function edit($id)
    {
        //
    }

    // Update Request Importation ----------------------------------------------------------
    public function UpdateStatusRequest(){
        $id     = $_REQUEST['id'];
        $status = $_REQUEST['status'];
        // $id     = 1;
        // $status = 'Done';

        try {

            $time   = new \DateTime();
            $now2   = $time->format('Y-m-d H:i:s');

            $Ncontract = NewContractRequestLcl::find($id);
            $Ncontract->status        = $status;
            $Ncontract->updated       = $now2;
            if($Ncontract->username_load == 'Not assigned'){
                $Ncontract->username_load = \Auth::user()->name.' '.\Auth::user()->lastname;
            }

            if($Ncontract->status == 'Processing'){
                if($Ncontract->time_star_one == false){
                    $Ncontract->time_star       = $now2;
                    $Ncontract->time_star_one   = true;
                }

            } elseif($Ncontract->status == 'Review'){
                if($Ncontract->time_total == null){
                    $fechaEnd = Carbon::parse($now2);
                    if(empty($Ncontract->time_star) == true){
                        $Ncontract->time_total = 'It did not go through the processing state';
                    } else{
                        $fechaStar = Carbon::parse($Ncontract->time_star);
                        $Ncontract->time_total = str_replace('after','',$fechaEnd->diffForHumans($fechaStar));
                    }
                }

            } elseif($Ncontract->status == 'Done'){

                if($Ncontract->sentemail == false){
                    $users = User::all()->where('company_user_id','=',$Ncontract->company_user_id);
                    $message = 'The request was processed N°: ' . $Ncontract->id;
                    foreach ($users as $user) {

                        $user->notify(new N_general(\Auth::user(),$message));
                    }

                    $usercreador = User::find($Ncontract->user_id);
                    $message = "The importation ".$Ncontract->id." was completed";
                    $usercreador->notify(new SlackNotification($message));
                    SendEmailRequestLclJob::dispatch($usercreador->toArray(),$id);

                }
            }

            $Ncontract->save();
            return response()->json($data=['status'=>1,'data'=>$status]);
        } catch (\Exception $e){
            return response()->json($data=['status'=>2]);;
        }

    }

    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }


    // Delete Request Importation ----------------------------------------------------------
    public function destroyRequest($id)
    {
        try{
            $Ncontract = NewContractRequestLcl::find($id);
            Storage::disk('LclRequest')->delete($Ncontract->namefile);
            $Ncontract->delete();
            return 1;
        } catch(\Exception $e){
            return 2;
        }
    }

    // New Request Importation ----------------------------------------------------------
    public function LoadViewRequestImporContractLcl(){
        $harbor         = harbor::all()->pluck('display_name','id');
        $carrier        = carrier::all()->pluck('name','id');
        $direction      = [null=>'Please Select'];
        $direction2      = Direction::all();
        foreach($direction2 as $d){
            $direction[$d['id']]=$d->name;
        }
        $user   = \Auth::user();
        return view('RequestsLcl.NewRequest',compact('harbor','carrier','user','direction'));
    }

    public function similarcontracts(Request $request,$id){
        $contracts = ContractLcl::select(['id',
                                          'name',
                                          'company_user_id',
                                          'account_id',
                                          'direction_id',
                                          'validity',
                                          'expire'
                                         ]);

        return Datatables::of($contracts->where('company_user_id',$id))
            ->filter(function ($query) use ($request,$id) {
                if ($request->has('direction') && $request->get('direction') != null) {
                    $query->where('direction_id', '=',$request->get('direction'));
                } else{
                    $query;
                }
                if ($request->has('carrierM')) {
                    $query->whereHas('carriers',function($q) use($request) {
                        $q->whereIn('carrier_id',$request->get('carrierM'));
                    });
                }
                if($request->has('dateO') && $request->get('dateO') != null && $request->has('dateT') && $request->get('dateT') != null) {
                    $query->where('validity', '=',$request->get('dateO'))->where('expire', '=',$request->get('dateT'));
                }

            })
            ->addColumn('carrier', function ($contracts) {
                $dd = $contracts->load('carriers.carrier');
                if(count($dd->carriers) != 0){
                    return str_replace(['[',']','"'],' ',$dd->carriers->pluck('carrier')->pluck('name'));
                } else {
                    return '-------';
                }

            })
            ->addColumn('direction', function ($contracts) {
                $dds = $contracts->load('direction');
                if(count($dds->direction) != 0){
                    return $dds->direction->name;
                } else {
                    return '-------';
                }
            })
            ->make(true);
    }
}
