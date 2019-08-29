<?php

namespace App\Http\Controllers;

use App\User;
use App\Harbor;
use App\Carrier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Notifications\N_general;
use App\Jobs\ProcessContractFile;
use App\Jobs\SendEmailRequestGcJob;
use App\NewRequestGlobalChargerLcl;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SlackNotification;
use App\AccountImportationGlobalChargerLcl;
use App\Mail\NewRequestGlobalChargeLclToUsernMail;
use App\Mail\NewRequestGlobalChargeLclToAdminMail;


class NewRequestGlobalChargerLclController extends Controller
{

    public function index()
    {
        $accounts = AccountImportationGlobalChargerLcl::with('companyuser')->orderBy('id','desc')->get();
        return view('RequestGlobalChargeLcl.index',compact('accounts'));
    }

    public function create()
    {
        $harbor         = harbor::all()->pluck('display_name','id');
        $carrier        = carrier::all()->pluck('name','id');
        $user           = \Auth::user();
        return view('RequestGlobalChargeLcl.NewRequest',compact('harbor','carrier','user'));
    }

    public function create2()
    {
        $Ncontracts = NewRequestGlobalChargerLcl::with('user','companyuser')->orderBy('id', 'desc')->get();
        //dd($Ncontracts[0]['companyuser']['name']);

        return Datatables::of($Ncontracts)
            ->addColumn('Company', function ($Ncontracts) {
                return $Ncontracts->companyuser->name;
            })
            ->addColumn('name', function ($Ncontracts) {
                return $Ncontracts->name;
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
            ->addColumn('time_elapsed', function ($Ncontracts) {
                if(empty($Ncontracts->time_total) != true){
                    return $Ncontracts->time_total;
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
                return '
                <!--<a href="/ImportationGlobalchargesFcl/RequestProccessGC/'.$Ncontracts->id.'" title="Proccess GC Request">
                    <samp class="la la-cogs" style="font-size:20px; color:#031B4E"></samp>
                </a>
                &nbsp;&nbsp;
                <a href="/RequestsGlobalchargers/RequestsGlobalchargersFcl/'.$Ncontracts->id.'" title="Download File">
                    <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E"></samp>
                </a>
                &nbsp;&nbsp;
                <a href="#" class="eliminarrequest" data-id-request="'.$Ncontracts->id.'" data-info="id:'.$Ncontracts->id.' Number Contract: "  title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>-->';
            })
            ->make();
    }

    public function store(Request $request)
    {
        $fileBoll = false;
        $time   = new \DateTime();
        $now    = $time->format('dmY_His');
        $now2   = $time->format('Y-m-d H:i:s');
        $file   = $request->file('file');
        $ext    = strtolower($file->getClientOriginalExtension());
        //obtenemos el nombre del archivo
        $nombre = $file->getClientOriginalName();
        $nombre = $now.'_'.$nombre;
        $fileBoll = Storage::disk('GCRequestLcl')->put($nombre,\File::get($file));

        if($fileBoll){
            $Ncontract                  = new NewRequestGlobalChargerLcl();
            $Ncontract->name			= $request->name;
            $Ncontract->validation      = $request->validation_expire;
            $Ncontract->company_user_id = $request->CompanyUserId;
            $Ncontract->namefile        = $nombre;
            $Ncontract->user_id         = $request->user;
            $Ncontract->created         = $now2;
            $Ncontract->save();

            ProcessContractFile::dispatch($Ncontract->id,$Ncontract->namefile,'gclcl','request');

            $user = User::find($request->user);
            $message = "There is a new request from ".$user->name." - ".$user->companyUser->name;
            $user->notify(new SlackNotification($message));
            $admins = User::where('type','admin')->get();
            $message = 'has created an new request: '.$Ncontract->id;
            foreach($admins as $userNotifique){
                \Mail::to($userNotifique->email)->send(new NewRequestGlobalChargeLclToAdminMail($userNotifique->toArray(),
                                                                                             $user->toArray(),
                                                                                             $Ncontract->toArray()));
                $userNotifique->notify(new N_general($user,$message));
            }

            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.content', 'Your request was created');
            return redirect()->route('globalchargeslcl.index');
            //return response()->json(['success'=>'You have successfully upload file.']);
        } else {

            $request->session()->flash('message.nivel', 'error');
            $request->session()->flash('message.content', 'Your request was not created');
            return redirect()->route('globalchargeslcl.index');
            //return response()->json(['danger'=>'You was not upload file.']);

        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function destroyRequest($id)
    {
        //
    }

    public function showStatus($id){
        $requests = NewRequestGlobalChargerLcl::find($id);
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
        return view('RequestGlobalChargeLcl.Body-Modals.edit',compact('requests','status_arr'));
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
            $Ncontract = NewRequestGlobalChargerLcl::find($id);
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
                    SendEmailRequestGcJob::dispatch($usercreador->toArray(),$id,'lcl');

                }

            }
            $Ncontract->save();
            return response()->json($data=['status'=>1,'data'=>$status]);
        } catch (\Exception $e){
            return response()->json($data=['status'=>2]);;
        }

    }
}
