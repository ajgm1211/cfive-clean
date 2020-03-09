<?php

namespace App\Http\Controllers;

use App\User;
use App\Harbor;
use HelperAll;
use App\Carrier;
use App\Contract;
use App\Container;
use App\Direction;
use \Carbon\Carbon;
use App\CompanyUser;
use App\GroupContainer;
use App\ContractCarrier;
use App\RequetsCarrierFcl;
use App\NewContractRequest;
use Illuminate\Http\Request;
use App\Jobs\NotificationsJob;
use App\Mail\RequestToUserMail;
use App\Notifications\N_general;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\NewRequestToAdminMail;
use App\Mail\NotificationAutoImport;
use App\Jobs\SendEmailRequestFclJob;
use Spatie\MediaLibrary\MediaStream;
use Spatie\MediaLibrary\Models\Media;
use App\Notifications\SlackNotification;
use Spatie\Permission\Models\Permission;

class RequestFclV2Controller extends Controller
{
	// Load View
	public function index()
	{
		$now 		= Carbon::now();
		$now2 		= Carbon::now();
		$date_start = $now->subMonth(3)->format('Y-m-d');
		$date_end	= $now2->format('Y-m-d');
		$date = $date_start.' / '.$date_end;
		return view('RequestV2.Fcl.show',compact('date'));
	}

	// Load Datatable
	public function create(Request $request)
	{
		$date_start = $request->dateS;
		$date_end	= $request->dateE;
		//$date_start = '2019-08-26 00:00:00';
		//$date_end	= '2020-03-03 12:39:54';
		$Ncontracts = DB::select('call  select_request_fcl("'.$date_start.'","'.$date_end.'")');
		//        dd($Ncontracts);
		//dd($Ncontracts[0]['Requestcarriers']->pluck('carrier')->pluck('name'));
		$permiso_eliminar = false;
		$user  = \Auth::user();
		if($user->hasAnyPermission([1])){
			$permiso_eliminar = true;
		}
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
                <samp class="la la-pencil-square-o" id="statusSamp'.$Ncontracts->id.'" class="statusHrf'.$Ncontracts->id.'" for="" style="'.$color.'"></samp>';
			})
			->addColumn('action', function ($Ncontracts) use($permiso_eliminar) {

				$buttons = '
                &nbsp;&nbsp;
				<a href="'.route("RequestImportation.show",$Ncontracts->id).'" title="Download File">
                    <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E"></samp>
                </a>
                &nbsp;&nbsp;';
				$eliminiar_buton = '
                <a href="#" class="eliminarrequest" data-id-request="'.$Ncontracts->id.'" data-info="id:'.$Ncontracts->id.' Number Contract: '.$Ncontracts->numbercontract.'"  title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>';

				if($permiso_eliminar){
					$buttons = $buttons . $eliminiar_buton;
				}

				if(empty($Ncontracts->contract) != true){
					$buttonDp = "<a href='#' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' onclick='AbrirModal(\"DuplicatedContractOtherCompany\",".$Ncontracts->contract.",".$Ncontracts->id.")'  title='Duplicate to another company'>
                      <i style='color:#b90000' class='la la-copy'></i>
                    </a>";   
					$butPrCt = '<a href="/Importation/RequestProccessFCL/'.$Ncontracts->contract.'/2/'.$Ncontracts->id.'" title="Proccess FCL Contract">
                    <samp class="la la-cogs" style="font-size:20px; color:#04950f"></samp>
                    </a>

                    '.$buttonDp.'
                    &nbsp;&nbsp;
                    <a href="#" title="Edit FCL Contract">
                    <samp class="la la-edit" onclick="editcontract('.$Ncontracts->contract.')" style="font-size:20px; color:#04950f"></samp>
                    </a>
                    ';
					$buttons = $butPrCt . $buttons;
				} else{
					$butPrRq = '<a href="/Importation/RequestProccessFCL/'.$Ncontracts->id.'/1/0" title="Proccess FCL Request">
                    <samp class="la la-cogs" style="font-size:20px; color:#D85F00"></samp>
                    </a>';
					$buttons = $butPrRq . $buttons;
				}
				return $buttons;
			})->make();
	}

	public function store(Request $request)
	{
		//dd($request->all());
		$CompanyUserId 		= $request->CompanyUserId;
		$direction_id  		= $request->direction;
		$carriers			= $request->carrierM;
		$name				= $request->name;
		$user				= $request->user;
		$groupContainer		= $request->groupContainers;
		$containers			= $request->containers;
		$validationexp		= $request->validation_expire;
		$validity			= explode('/',$validationexp);
		$time   			= new \DateTime();
		$now    			= $time->format('dmY_His');
		$now2   			= $time->format('Y-m-d H:i:s');
		$file 				= $request->input('document');

		if(!empty($file)){
			$gpContainer = GroupContainer::find($groupContainer);			
			$ArrayData['group_containers'] = [
				'id' => $gpContainer->id,
				'name' => $gpContainer->name
			];
			$ArrayData['containers'] = [];
			foreach($containers as $containerId){
				$container = Container::find($containerId);
				$ArrayData['containers'][] = [
					'id' => $container->id,
					'name' => $container->name,
					'code' => $container->code
				];
			}
			$data = json_encode($ArrayData);

			$contract                   = new Contract();
			$contract->name             = $name;
			$contract->validity         = $validity[0];
			$contract->expire           = $validity[1];
			$contract->direction_id     = $direction_id;
			$contract->status           = 'incomplete';
			$contract->company_user_id  = $CompanyUserId;
			$contract->save();

			foreach($carriers as $carrierVal){
				ContractCarrier::create([
					'carrier_id'    => $carrierVal,
					'contract_id'   => $contract->id
				]);
			}

			$Ncontract  = new NewContractRequest();
			$Ncontract->namecontract    = $name;
			$Ncontract->validation      = $validationexp;
			$Ncontract->direction_id    = $direction_id;
			$Ncontract->company_user_id = $CompanyUserId;
			$Ncontract->user_id         = $user;
			$Ncontract->created         = $now2;
			$Ncontract->username_load   = 'Not assigned';
			$Ncontract->data            = $data;
			$Ncontract->contract_id     = $contract->id;
			$Ncontract->save();

			foreach($carriers as $carrierVal){
				ContractCarrier::create([
					'carrier_id'    => $carrierVal,
					'contract_id'   => $contract->id
				]);

				RequetsCarrierFcl::create([
					'carrier_id' => $carrierVal,
					'request_id' => $Ncontract->id
				]);
			}

			$Ncontract->addMedia(storage_path('tmp/request/' . $file))->toMediaCollection('document','contracts3');

			$user 		= User::find($request->user);
			$message 	= "There is a new request from ".$user->name." - ".$user->companyUser->name;
			$user->notify(new SlackNotification($message));
			$admins 	= User::where('type','admin')->get();
			$message 	= 'has created an new request: '.$Ncontract->id;

			NotificationsJob::dispatch('Request-Fcl',[
				'user' => $request->user,
				'ncontract' => $Ncontract->toArray()
			]);

			foreach($admins as $userNotifique){
				$userNotifique->notify(new N_general($user,$message));
			}

			$request->session()->flash('message.nivel', 'success');
			$request->session()->flash('message.content', 'Your request was created');
			return redirect()->route('contracts.index');
		} else {

			$request->session()->flash('message.nivel', 'error');
			$request->session()->flash('message.content', 'Your request was not created');
			return redirect()->route('contracts.index');
		}
	}

	public function newRequest(Request $request){
		$carrier        = carrier::all()->pluck('name','id');
		$direction		= HelperAll::addOptionSelect(Direction::all(),'id','name');
		$groupContainer	= HelperAll::addOptionSelect(GroupContainer::all(),'id','name');
		$containers		= Container::pluck('name','id');
		$user           = \Auth::user();

		return view('RequestV2.Fcl.index',compact('carrier','user','direction','groupContainer','containers'));
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

	public function getContainers(Request $request){
		$groupContainers = $request->groupContainers;
		$containers 	 = Container::where('gp_container_id',$groupContainers)->where('name','!=','45 HC')->where('name','!=','40 NOR')->pluck('id');
		return response()->json(['success' => true,'data' => ['values' => $containers->all() ]]);
	}

	public function storeMedia(Request $request)
	{
		$path = storage_path('tmp/request');

		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}

		$file = $request->file('file');

		$name = uniqid() . '_' . trim($file->getClientOriginalName());

		$file->move($path, $name);

		return response()->json([
			'name'          => $name,
			'original_name' => $file->getClientOriginalName(),
		]);
	}
}
