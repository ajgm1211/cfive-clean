<?php

namespace App\Http\Controllers;

use App\User;
use App\Harbor;
use App\Carrier;
use Carbon\Carbon;
use App\CompanyUser;
use Illuminate\Http\Request;
use App\Jobs\NotificationsJob;
use Yajra\Datatables\Datatables;
use App\Notifications\N_general;
use App\Jobs\ProcessContractFile;
use App\Jobs\SendEmailRequestGcJob;
use App\NewRequestGlobalChargerLcl;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SlackNotification;
use Spatie\Permission\Models\Permission;
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
		$permiso_eliminar = false;
		$user  = \Auth::user();
		if($user->hasAnyPermission([1])){
			$permiso_eliminar = true;
		}
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
			->addColumn('username_load', function ($Ncontracts) {
				return '<span id="userLoad'.$Ncontracts->id.'">'.$Ncontracts->username_load.'</span>';
			})
			->addColumn('time_elapsed', function ($Ncontracts) {
				if(empty($Ncontracts->time_elapsed) != true){
					return $Ncontracts->time_elapsed;
				} else {
					return '<span id="timeElapsed'.$Ncontracts->id.'"> ------------------ </span>';
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

				return '<a href="#" onclick="showModal('.$Ncontracts->id.')"style="'.$color.'" id="statusHrf'.$Ncontracts->id.'" class="statusHrf'.$Ncontracts->id.'">'.$Ncontracts->status.'</a>
                &nbsp;
                <samp class="la la-pencil-square-o statusHrf'.$Ncontracts->id.'" id="statusSamp'.$Ncontracts->id.'" for="" style="font-size:15px;'.$color.'"></samp>';
			})
			->addColumn('action', function ($Ncontracts) use($permiso_eliminar) {
				$buttons = '
                <a href="'.route('process.request.gc.lcl',$Ncontracts->id).'" title="Proccess GC Request">
                    <samp class="la la-cogs" style="font-size:20px; color:#031B4E"></samp>
                </a>
                &nbsp;&nbsp;
                <a href="'.route('RequestsGlobalchargersLcl.show',$Ncontracts->id).'" title="Download File">
                    <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E"></samp>
                </a>
                &nbsp;&nbsp;';
				$eliminiar_buton = '
                <a href="#" class="eliminarrequest" data-id-request="'.$Ncontracts->id.'" data-info="id:'.$Ncontracts->id.' Number Contract: "  title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>';

				if($permiso_eliminar){
					$buttons = $buttons . $eliminiar_buton;
				}

				return $buttons;
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

			NotificationsJob::dispatch('Request-Lcl-GC',[
				'user' => $request->user,
				'ncontract' => $Ncontract->toArray()
			]);

			foreach($admins as $userNotifique){
				/*\Mail::to($userNotifique->email)->send(new NewRequestGlobalChargeLclToAdminMail(
					$userNotifique->toArray(),
					$user->toArray(),
					$Ncontract->toArray()));*/
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

	public function show($id,Request $request)
	{
		$Ncontract = NewRequestGlobalChargerLcl::find($id);
		$time       = new \DateTime();
		$now        = $time->format('d-m-y');
		$company    = CompanyUser::find($Ncontract->company_user_id);
		$extObj     = new \SplFileInfo($Ncontract->namefile);
		$ext        = $extObj->getExtension();
		$name       = $Ncontract->id.'-'.$company->name.'_'.$now.'-GCFCL.'.$ext;

		if(Storage::disk('s3_upload')->exists('Request/Global-charges/LCL/'.$Ncontract->namefile)){
			return Storage::disk('s3_upload')->download('Request/Global-charges/LCL/'.$Ncontract->namefile,$name);
		} elseif(Storage::disk('s3_upload')->exists('contracts/'.$Ncontract->namefile)){
			return Storage::disk('s3_upload')->download('contracts/'.$Ncontract->namefile,$name);
		} elseif(Storage::disk('GCRequestLcl')->exists($Ncontract->namefile)){
			return Storage::disk('GCRequestLcl')->download($Ncontract->namefile,$name);
		} elseif(Storage::disk('UpLoadFile')->exists($Ncontract->namefile)){
			return Storage::disk('UpLoadFile')->download($Ncontract->namefile,$name);
		}

		return back();


		/* try{
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
        }*/

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

	public function destroyRequest($id){
		try{
			$Ncontract = NewRequestGlobalChargerLcl::find($id);
			Storage::disk('GCRequestLcl')->delete($Ncontract->namefile);
			$Ncontract->delete();
			return 1;
		} catch(\Exception $e){
			return 2;
		}
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
					$Ncontract->updated       = $now2;
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
					if(env('APP_VIEW') == 'operaciones') {
						SendEmailRequestGcJob::dispatch($usercreador->toArray(),$id,'lcl')->onQueue('operaciones');
					} else {
						SendEmailRequestGcJob::dispatch($usercreador->toArray(),$id,'lcl');
					}
				}

			}
			$Ncontract->save();
			if(strnatcasecmp($Ncontract->status,'Pending')==0){
				$color = '#f81538';
			} else if(strnatcasecmp($Ncontract->status,'Processing')==0){
				$color = '#5527f0';
			} else if(strnatcasecmp($Ncontract->status,'Review')==0){
				$color = '#e07000';
			} else if(strnatcasecmp($Ncontract->status,'Done')==0){
				$color = '#04950f';
			}
			return response()->json($data=['data'=>1,'status' => $Ncontract->status,'color'=> $color,'request' => $Ncontract->toArray()]);
		} catch (\Exception $e){
			return response()->json($data=['data'=>2]);;
		}

	}
}
