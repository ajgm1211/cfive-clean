<?php

namespace App\Http\Controllers;

use App\User;
use App\Harbor;
use App\Carrier;
use App\CompanyUser;
use Illuminate\Http\Request;
use App\Notifications\N_general;
use Yajra\Datatables\Datatables;
use App\Jobs\ProcessContractFile;
use App\NewGlobalchargeRequestFcl;
use App\AccountImportationGlobalcharge;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SlackNotification;
use App\Mail\NewRequestGlobalChargeToUserMail;
use App\Mail\NewRequestGlobalChargeToAdminMail;

class NewGlobalchargeRequestControllerFcl extends Controller
{

    public function index()
    {
        $accounts = AccountImportationGlobalcharge::with('companyuser')->orderBy('id','desc')->get();
        return view('RequestGlobalChargeFcl.index',compact('accounts'));
    }

    public function create()
    {
        $harbor         = harbor::all()->pluck('display_name','id');
        $carrier        = carrier::all()->pluck('name','id');
        $user   = \Auth::user();
        return view('RequestGlobalChargeFcl.NewRequest',compact('harbor','carrier','user'));
    }

    public function create2(){
        $Ncontracts = NewGlobalchargeRequestFcl::with('user','companyuser')->orderBy('id', 'desc')->get();
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
                <a href="/ImportationGlobalchargesFcl/RequestProccessGC/'.$Ncontracts->id.'" title="Proccess GC Request">
                    <samp class="la la-cogs" style="font-size:20px; color:#031B4E"></samp>
                </a>
                &nbsp;&nbsp;
                <a href="/RequestsGlobalchargers/RequestsGlobalchargersFcl/'.$Ncontracts->id.'" title="Download File">
                    <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E"></samp>
                </a>
                &nbsp;&nbsp;
                <a href="#" class="eliminarrequest" data-id-request="'.$Ncontracts->id.'" data-info="id:'.$Ncontracts->id.' Number Contract: '.$Ncontracts->numbercontract.'"  title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>';
            })

            ->make();
    }

    public function showStatus($id){
        $requests = NewGlobalchargeRequestFcl::find($id);
        //dd($requests);
        return view('RequestGlobalChargeFcl.Body-Modals.edit',compact('requests'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $fileBoll = false;
        $time   = new \DateTime();
        $now    = $time->format('dmY_His');
        $now2   = $time->format('Y-m-d H:i:s');
        $file   = $request->file('file');
        $ext    = strtolower($file->getClientOriginalExtension());
        //obtenemos el nombre del archivo
        $nombre = $file->getClientOriginalName();
        $nombre = $now.'_'.$nombre;
        $fileBoll = \Storage::disk('GCRequest')->put($nombre,\File::get($file));

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
            $Ncontract  = new NewGlobalchargeRequestFcl();
            $Ncontract->name			    = $request->name;
            $Ncontract->validation      = $request->validation_expire;
            $Ncontract->company_user_id = $request->CompanyUserId;
            $Ncontract->namefile        = $nombre;
            $Ncontract->user_id         = $request->user;
            $Ncontract->created         = $now2;
            $Ncontract->type            = $type;
            $Ncontract->data            = $data;
            $Ncontract->save();

            ProcessContractFile::dispatch($Ncontract->id,$Ncontract->namefile,'gcfcl','request');

            $user = User::find($request->user);
            $message = "There is a new request from ".$user->name." - ".$user->companyUser->name;
            $user->notify(new SlackNotification($message));
            $admins = User::where('type','admin')->get();
            $message = 'has created an new request: '.$Ncontract->id;
            foreach($admins as $userNotifique){
                \Mail::to($userNotifique->email)->send(new NewRequestGlobalChargeToAdminMail($userNotifique->toArray(),
                                                                                             $user->toArray(),
                                                                                             $Ncontract->toArray()));
                $userNotifique->notify(new N_general($user,$message));
            }

            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.content', 'Your request was created');
            return redirect()->route('globalcharges.index');
            //return response()->json(['success'=>'You have successfully upload file.']);
        } else {

            $request->session()->flash('message.nivel', 'error');
            $request->session()->flash('message.content', 'Your request was not created');
            return redirect()->route('globalcharges.index');
            //return response()->json(['danger'=>'You was not upload file.']);

        }
    }

    public function show($id)
    {
        $Ncontract = NewGlobalchargeRequestFcl::find($id);
        $time       = new \DateTime();
        $now        = $time->format('d-m-y');
        $company    = CompanyUser::find($Ncontract->company_user_id);
        $extObj     = new \SplFileInfo($Ncontract->namefile);
        $ext        = $extObj->getExtension();
        $name       = $Ncontract->id.'-'.$company->name.'_'.$now.'-GCFCL.'.$ext;
        try{
            return Storage::disk('s3_upload')->download('Request/Global-charges/FCL/'.$Ncontract->namefile,$name);
        } catch(\Exception $e){
            try{
                return Storage::disk('s3_upload')->download('contracts/'.$Ncontract->namefile,$name);
            } catch(\Exception $e){
                try{
                    return Storage::disk('GCRequest')->download($Ncontract->namefile,$name);
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


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
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

            $Ncontract = NewGlobalchargeRequestFcl::find($id);
            $Ncontract->status        = $status;
            $Ncontract->updated       = $now2;
            $Ncontract->username_load = \Auth::user()->name.' '.\Auth::user()->lastname;
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
                        \Mail::to($userCmp->email)->send(new NewRequestGlobalChargeToAdminMail($userCmp->toArray(),
                                                                                               $Ncontract->toArray()));
                    }
                }

                $usercreador = User::find($Ncontract->user_id);
                $message = "The importation ".$Ncontract->id." was completed";
                $usercreador->notify(new SlackNotification($message));

                \Mail::to($usercreador->email)->send(new NewRequestGlobalChargeToUserMail($usercreador->toArray(),
                                                                                          $Ncontract->toArray()));

            }

            return response()->json($data=['status'=>1,'data'=>$status]);
        } catch (\Exception $e){
            return response()->json($data=['status'=>2]);;
        }

    }

    // Delete Request Importation ----------------------------------------------------------
    public function destroyRequest($id)
    {
        try{
            $Ncontract = NewGlobalchargeRequestFcl::find($id);
            Storage::disk('GCRequest')->delete($Ncontract->namefile);
            $Ncontract->delete();
            return 1;
        } catch(\Exception $e){
            return 2;
        }
    }
}
