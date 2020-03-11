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
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\Models\Media;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SlackNotification;
use Spatie\Permission\Models\Permission;

class RequestFclV2Controller extends Controller
{
    // Load View
    public function index()
    {
        $now 		= Carbon::now();
        $now2 		= Carbon::now();
        $date_start = $now->subMonth(10)->format('Y-m-d');
        $date_end	= $now2->format('Y-m-d');
        $date = $date_start.' / '.$date_end;
        return view('RequestV2.Fcl.show',compact('date'));
    }

    // Load Datatable
    public function create(Request $request)
    {
        $date_start = $request->dateS;
        $date_end	= $request->dateE;
        $date_end   = Carbon::parse($date_end);
        $date_end   = $date_end->addDay(1);
        //$date_start = '2019-08-26 00:00:00';
        //$date_end	= '2020-03-03 12:39:54';
        $Ncontracts = DB::select('call  select_request_fcl("'.$date_start.'","'.$date_end.'")');
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

                if(empty($Ncontracts->namefile) != true){
                    $buttons = '
                        &nbsp;&nbsp;
				    <a href="'.route("RequestFcl.donwload.files",[$Ncontracts->id,"storage"]).'" title="Download File">
                        <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E"></samp>
                    </a>
                    &nbsp;&nbsp;';
                } else{
                    $buttons = '
                        &nbsp;&nbsp;
				    <a href="'.route("RequestFcl.donwload.files",[$Ncontracts->id,"media"]).'" title="Download File">
                        <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E"></samp>
                    </a>
                    &nbsp;&nbsp;';
                }
                $eliminiar_buton = '
                <a href="#" class="eliminarrequest" data-id-request="'.$Ncontracts->id.'" data-info="id:'.$Ncontracts->id.' Number Contract: '.$Ncontracts->numbercontract.'"  title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>';

                if($permiso_eliminar){
                    $buttons = $buttons . $eliminiar_buton;
                }

                if(empty($Ncontracts->contract) != true){
                    $butPrCt 	= '';
                    $buttonDp	= '';
                    if(strnatcasecmp($Ncontracts->status,'Done')==0){
                        $hidden = '';
                    } else {
                        $hidden = 'hidden';                        
                    }
                    $buttonDp = "<a href='#' id='statusHiden".$Ncontracts->id."' ".$hidden." class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' onclick='AbrirModal(\"DuplicatedContractOtherCompany\",".$Ncontracts->contract.",".$Ncontracts->id.")'  title='Duplicate to another company'>                      <i style='color:#b90000' class='la la-copy'></i></a>";   
                    if(strnatcasecmp($Ncontracts->status,'Pending')!=0){
                        $butPrCt = '<a href="/Importation/RequestProccessFCL/'.$Ncontracts->contract.'/2/'.$Ncontracts->id.'" title="Proccess FCL Contract"><samp class="la la-cogs" style="font-size:20px; color:#04950f"></samp></a>                    &nbsp;&nbsp;';
                    } 

                    $buttoEdit = '<a href="#" title="Edit FCL Contract">
                    <samp class="la la-edit" onclick="editcontract('.$Ncontracts->contract.')" style="font-size:20px; color:#04950f"></samp>
                    </a>
                    ';

                    $buttons = $butPrCt . $buttonDp . $buttoEdit . $buttons;
                } else{
                    if(strnatcasecmp($Ncontracts->status,'Pending')!=0){
                        $butPrRq = '<a href="/Importation/RequestProccessFCL/'.$Ncontracts->id.'/1/0" title="Proccess FCL Request">
                    <samp class="la la-cogs" style="font-size:20px; color:#D85F00"></samp>
                    </a>';
                        $buttons = $butPrRq . $buttons;
                    }
                }
                return $buttons;
            })->make();
    }

    // Crea un nueva Solicitud. (Cliente)
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

    //Carga los Status segun la posicion actual
    public function show($id){
        $requests = NewContractRequest::find($id);
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

        return view('RequestV2.Fcl.Body-Modals.edit',compact('requests','status_arr'));
    }


    public function UpdateStatusRequest(){
        $id     = $_REQUEST['id'];
        $status = $_REQUEST['status'];
        // $id     = 1;
        // $status = 'Done';

        $time   = new \DateTime();
        $now2   = $time->format('Y-m-d H:i:s');

        try {
            $Ncontract = NewContractRequest::find($id);
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
                        $time_exacto = '';
                        $fechaStar = Carbon::parse($Ncontract->time_star);
                        $time_exacto = $fechaEnd->diffInMinutes($fechaStar);
                        if($time_exacto == 0 || $time_exacto == '0'){
                            $time_exacto = '1 minute';
                        } else {
                            $time_exacto = $time_exacto.' minutes';							
                        }				
                        $Ncontract->time_total = $time_exacto;
                    }
                }
            } elseif($Ncontract->status == 'Done'){

                if($Ncontract->time_manager == null){
                    $fechaEnd = Carbon::parse($now2);
                    $fechaStar = Carbon::parse($Ncontract->created);
                    $time_manager = number_format($fechaEnd->diffInMinutes($fechaStar)/60,2);
                    $Ncontract->time_manager = $time_manager.' hours';
                    //$Ncontract->time_manager = $fechaEnd->diffInHours($fechaStar).' hours';
                }

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
                        SendEmailRequestFclJob::dispatch($usercreador->toArray(),$id)->onQueue('operaciones');
                    } else {
                        SendEmailRequestFclJob::dispatch($usercreador->toArray(),$id);				
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
            return response()->json($data=['data'=>1,'status' => $Ncontract->status,'color'=> $color,'request' => $Ncontract]);
        } catch (\Exception $e){
            return response()->json($data=['data'=>2]);;
        }

    }

    // Descargar archivos, dependiendo si es Storage o Media
    public function donwloadFiles($id,$selector)
    {
        if(strnatcasecmp($selector,'media')==0){
            $Ncontract	= NewContractRequest::find($id);
            $Ncontract->load('companyuser');
            $time       = new \DateTime();
            $now        = $time->format('d-m-y');
            $mediaItem = $Ncontract->getFirstMedia('document');
            $extObj     = new \SplFileInfo($mediaItem->file_name);
            $ext        = $extObj->getExtension();
            $name       = $Ncontract->id.'-'.$Ncontract->companyuser->name.'_'.$now.'-FLC.'.$ext;
            return Storage::disk('contracts3')->download($mediaItem->id.'/'.$mediaItem->file_name,$name);

        } elseif(strnatcasecmp($selector,'storage')==0){

            $Ncontract = NewContractRequest::find($id);
            $time       = new \DateTime();
            $now        = $time->format('d-m-y');
            $company    = CompanyUser::find($Ncontract->company_user_id);
            $extObj     = new \SplFileInfo($Ncontract->namefile);
            $ext        = $extObj->getExtension();
            $name       = $Ncontract->id.'-'.$company->name.'_'.$now.'-FLC.'.$ext;
            $success 	= false;
            $descarga 	= null;

            if(Storage::disk('s3_upload')->exists('Request/FCL/'.$Ncontract->namefile,$name)){
                $success 	= true;
                return	Storage::disk('s3_upload')->download('Request/FCL/'.$Ncontract->namefile,$name);
            } elseif(Storage::disk('s3_upload')->exists('contracts/'.$Ncontract->namefile,$name)){
                $success 	= true;
                return	Storage::disk('s3_upload')->download('contracts/'.$Ncontract->namefile,$name);
            } elseif(Storage::disk('FclRequest')->exists($Ncontract->namefile,$name)){
                $success 	= true;
                return	Storage::disk('FclRequest')->download($Ncontract->namefile,$name);
            } elseif(Storage::disk('UpLoadFile')->exists($Ncontract->namefile,$name)){
                $success 	= true;
                return	Storage::disk('UpLoadFile')->download($Ncontract->namefile,$name);
            } else{
                $request->session()->flash('message.nivel', 'danger');
                $request->session()->flash('message.content', 'Error. File not found');
                return back();
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
