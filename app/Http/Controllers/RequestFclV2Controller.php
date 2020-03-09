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
		//
	}

	// Load Datatable
	public function create()
	{
		//
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
