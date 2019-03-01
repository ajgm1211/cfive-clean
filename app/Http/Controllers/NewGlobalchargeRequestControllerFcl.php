<?php

namespace App\Http\Controllers;

use App\User;
use App\Harbor;
use App\Carrier;
use App\CompanyUser;
use Illuminate\Http\Request;
use App\Notifications\N_general;
use App\Jobs\ProcessContractFile;
use App\NewGlobalchargeRequestFcl;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SlackNotification;
use App\Mail\NewRequestGlobalChargeToUserMail;
use App\Mail\NewRequestGlobalChargeToAdminMail;

class NewGlobalchargeRequestControllerFcl extends Controller
{

    public function index()
    {
        $Ncontracts = NewGlobalchargeRequestFcl::with('user','companyuser')->orderBy('id', 'desc')->get();
        //dd($Ncontracts);
        return view('RequestGlobalChargeFcl.index',compact('Ncontracts'));
    }

    public function create()
    {
        $harbor         = harbor::all()->pluck('display_name','id');
        $carrier        = carrier::all()->pluck('name','id');
        $user   = \Auth::user();
        return view('RequestGlobalChargeFcl.NewRequest',compact('harbor','carrier','user'));
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
        $fileBoll = \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));

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

            ProcessContractFile::dispatch($Ncontract->id, $Ncontract->namefile ,'fcl');

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
        $now        = $time->format('d-m-Y_s');
        $company    = CompanyUser::find($Ncontract->company_user_id);
        $extObj     = new \SplFileInfo($Ncontract->namefile);
        $ext        = $extObj->getExtension();
        $name       = $company->name.'_'.$now.'.'.$ext;
        return Storage::disk('UpLoadFile')->download($Ncontract->namefile,$name);
        //return Storage::disk('s3_upload')->download('contracts/'.$Ncontract->namefile,$name);
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
            Storage::disk('UpLoadFile')->delete($Ncontract->namefile);
            $Ncontract->delete();
            return 1;
        } catch(\Exception $e){
            return 2;
        }
    }
}
