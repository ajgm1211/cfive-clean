<?php

namespace App\Http\Controllers;

use App\User;
use App\Harbor;
use App\Carrier;
use App\Contract;
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
		$direction      = [null=>'Please Select'];
		$direction2     = Direction::all();
		$groupContainer	= GroupContainer::pluck('name','id');
		$user           = \Auth::user();
		foreach($direction2 as $d){
			//dd($direction2);
			$direction[$d['id']]=$d->name;
		}
		//dd($direction);
		return view('RequestV2.Fcl.index',compact('carrier','user','direction','groupContainer'));
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
}
