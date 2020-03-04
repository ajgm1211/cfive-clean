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
use App\Mail\RequestToUserMail;
use App\Notifications\N_general;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\NewRequestToAdminMail;
use App\Mail\NotificationAutoImport;
use App\Jobs\SendEmailRequestFclJob;
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
        //
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
		$containers 	 = Container::where('gp_container_id',$groupContainers)->pluck('id');
		return response()->json(['success' => true,'data' => ['values' => $containers->all() ]]);
	}
	
	public function storeMedia(Request $request)
    {
        $path = storage_path('tmp/uploads');

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
