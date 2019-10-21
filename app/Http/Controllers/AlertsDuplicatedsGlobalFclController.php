<?php

namespace App\Http\Controllers;

use App\User;
use App\StatusAlert;
use GuzzleHttp\Client;
use App\AlertCompanyUser;
use App\AlertDuplicateGcFcl;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

class AlertsDuplicatedsGlobalFclController extends Controller
{
    // CARGA LA VISTA LAS COMPAÑIAS CON G.C DUPLICADOS
    public function index(Request $request)
    {
        return view('alertsDuplicatedsGCFcl.index');
    }

    // MUESTRA LAS ALERTAS QUE TIENEN G.C DUPLICADOS
    public function create()
    {
        $alerts = AlertDuplicateGcFcl::with('status')->get();
        //dd($alerts);
        return DataTables::of($alerts)
            ->addColumn('status', function ($alerts){ 
                $color='';
                if(strnatcasecmp($alerts->status->name,'pending')==0){
                    //$color = 'color:#031B4E';
                    $color = 'color:#f81538';
                } else if(strnatcasecmp($alerts->status->name,'false')==0){
                    $color = 'color:#5527f0';
                } else if(strnatcasecmp($alerts->status->name,'solved')==0){
                    $color = 'color:#e07000';
                } else {
                    $color = 'color:#04950f';
                }

                return '<a href="#" onclick="showModal('.$alerts->id.')" id="statusHrf'.$alerts->id.'" class="statusHrf'.$alerts->id.'" style="'.$color.'">'.$alerts->status->name.'</a>
                &nbsp;
                <span class="la la-pencil-square-o" for="" id="statusSamp'.$alerts->id.'" class="statusHrf'.$alerts->id.'" style="font-size:15px;'.$color.'"></span>';

            })
            ->addColumn('action', function ( $alerts) {
                return '
                    <a href="'.route('globalsduplicated.show',$alerts->id).'"   class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"   title="Companies G.C. Duplicateds " ">
                        <i style="color:#036aa0" class="la la-eye"></i>
				    </a>';
            })
            //->editColumn('id', '{{$alerts->id}}')->toJson();
            ->toJson();
    }

    public function store(Request $request)
    {
        //
    }

    // CARGA LA VISTA LAS COMPAÑIAS CON G.C DUPLICADOS
    public function show($id)
    {
        return view('alertsDuplicatedsGCFcl.show',compact('id'));
    }

    // MUESTRA LAS COMPAÑIAS QUE TIENEN G.C DUPLICADOS DE LA PRESENTE ALERTA  
    public function edit($id)
    {
        $alertsCmp = AlertCompanyUser::where('alert_dp_id',$id)->with('alert','company_user')->get();

        return DataTables::of($alertsCmp)
            ->addColumn('company', function ($alertsCmp){ 
                return $alertsCmp->company_user->name;
            })
            ->addColumn('date', function ($alertsCmp){ 
                return $alertsCmp->alert->date;
            })
            ->addColumn('action', function ( $alertsCmp) {
                return '
                    <a href="'.route('groupglobalsduplicated.show',$alertsCmp->id).'"   class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"   title="Companies G.C. Duplicateds " ">
                        <i style="color:#036aa0" class="la la-eye"></i>
				    </a>';
            })
            //->editColumn('id', '{{$alerts->id}}')->toJson();
            ->toJson();
    }

    public function showStatus($id){
        $status = StatusAlert::pluck('name','id');
        $alert  = AlertDuplicateGcFcl::find($id);
        //dd($alert,$status);
        return view('alertsDuplicatedsGCFcl.Body-Modals.edit',compact('status','alert'));
    }

    public function updateStatus(Request $request,$id){
        try{
            $alert  = AlertDuplicateGcFcl::find($id);
            $alert->status_alert_id = $request->status_id;
            $alert->update();
            $alert  = AlertDuplicateGcFcl::with('status')->find($id);
            if(strnatcasecmp($alert->status->name,'pending')==0){
                $color = '#f81538';
            } else if(strnatcasecmp($alert->status->name,'false')==0){
                $color = '#5527f0';
            } else if(strnatcasecmp($alert->status->name,'solved')==0){
                $color = '#e07000';
            } else {
                $color = '#04950f';
            }
            return response()->json(['data'=> 1,'status' => $alert->status->name,'color'=> $color]);
        } catch(\Exception $e){
            return response()->json(['data'=> 2]);            
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

    public function searchDuplicateds(Request $request){
        $user_adm_rq = User::where('email','admin@example.com')->orWhere('email','info@cargofive.com')->first();
    
        if(env('APP_ENV') == 'local'){
            $client = new Client(['base_uri' => 'http://duplicate-gc/DuplicateGCFCL/']);                           
        }else if(env('APP_ENV') == 'developer'){
            $client = new Client(['base_uri' => 'http://duplicateds-globalchargers-dev.eu-central-1.elasticbeanstalk.com/DuplicateGCFCL/']);                           
        }else{
            $client = new Client(['base_uri' => 'http://prod.duplicatedscg.cargofive.com/DuplicateGCFCL/']);
        }

        $response = $client->request('GET','DGCFCL-Create', [
            'headers' => [
                'Authorization' => 'Bearer '.$user_adm_rq->api_token,
                'Accept'        => 'application/json',
            ]
        ]);
        $dataGen = json_decode($response->getBody()->getContents(),true);

        if($dataGen['Success'] == 1){
            $request->session()->flash('message.content', 'Job - Duplicateds Active. Please wait a few minutes and refresh the page' );
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            return redirect()->route('globalsduplicated.index'); 
        } else {
            $request->session()->flash('message.content', 'Job - Duplicateds Not Active' );
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.title', 'Well done!');
            return redirect()->route('globalsduplicated.index'); 
        }
    }
}
