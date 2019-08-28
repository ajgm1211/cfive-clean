<?php

namespace App\Http\Controllers;

use App\Harbor;
use App\Region;
use App\Country;
use App\Carrier;
use App\CompanyUser;
use App\TypeDestiny;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\AccountImportationGlobalChargerLcl;

class ImportationGlobalChargerLclController extends Controller
{

    public function index()
    {
        $harbor         = Harbor::all()->pluck('display_name','id');
        $country        = Country::all()->pluck('name','id');
        $carrier        = Carrier::all()->pluck('name','id');
        $region         = Region::all()->pluck('name','id');
        $companysUser   = CompanyUser::all()->pluck('name','id');
        $typedestiny    = TypeDestiny::all()->pluck('description','id');
        return view('importationGlobalChargerLcl.index',compact('harbor','region','country','carrier','companysUser','typedestiny'));
    }

    public function UploadFileNewContract(Request $request){
        
    }
    
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    public function indexAccount(){
        $account = AccountImportationGlobalChargerLcl::with('companyuser')->get();
        return DataTables::of($account)
            ->addColumn('status', function ( $account) {
                return  $account->status;

            })
            ->addColumn('company_user_id', function ( $account) {
                return  $account->companyuser->name;
            })
            ->addColumn('requestgc_id', function ( $account) {
                if(empty($account->requestgc_id) != true){
                    return  $account->requestgc_id;
                } else {
                    return 'Manual';
                }
            })
            ->addColumn('action', function ( $account) {
                return '<a href="/ImportationGlobalchargesFcl/FailedGlobalchargers/'.$account->id.'/1" class="show"  title="Failed-Good" >
                            <samp class="la la-pencil-square-o" style="font-size:20px; color:#031B4E"></samp>
                        </a>
                        &nbsp;
                        &nbsp;
                        <a href="/ImportationGlobalchargesFcl/DownloadAccountgcfcl/'.$account->id.'" class="">
                            <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E" title="Download"></samp>
                        </a>
                        &nbsp; &nbsp; 
                        <a href="#" class="eliminaracount" data-id-acount="'.$account->id.'"  title="Delete" >
                            <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                        </a>';
            })
            ->editColumn('id', '{{$id}}')->toJson();
    }
}
