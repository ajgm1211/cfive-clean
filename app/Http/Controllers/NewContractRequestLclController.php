<?php

namespace App\Http\Controllers;

use App\Carrier;
use App\CompanyUser;
use App\ContractCarrierLcl;
use App\ContractLcl;
use App\Direction;
use App\Harbor;
use App\Http\Requests\StoreNewRequestLcl;
use App\Jobs\ExportRequestsJob;
use App\Jobs\ValidateTemplateLclJob;
use App\Jobs\NotificationsJob;
use App\Jobs\ProcessContractFile;
use App\Jobs\SendEmailRequestLclJob;
use App\NewContractRequestLcl;
use App\Notifications\N_general;
use App\Notifications\SlackNotification;
use App\RequetsCarrierLcl;
use App\User;
use Carbon\Carbon;
use EventIntercom;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PrvRequest;
use Yajra\Datatables\Datatables;
use App\Http\Traits\MixPanelTrait;
use App\Http\Traits\UtilTrait;
use Illuminate\Support\Facades\Log;
use HelperAll;

class NewContractRequestLclController extends Controller
{
    use MixPanelTrait, UtilTrait;

    public function index()
    {
        return view('RequestsLcl.index');
    }

    public function indexListClient()
    {
        $company_userid = \Auth::user()->company_user_id;
        return view('RequestsLcl.indexClient', compact('company_userid'));
    }

    //lista todos los request Admin
    public function create()
    {
        //$Ncontracts = NewContractRequestLcl::with('user','companyuser','Requestcarriers.carrier','direction')->orderBy('id', 'desc')->get();
        $Ncontracts = DB::select('call  select_request_lcl()');
        //dd($Ncontracts[0]['companyuser']['name']);
        $permiso_eliminar = false;
        $user = \Auth::user();
        if ($user->hasAnyPermission([1])) {
            $permiso_eliminar = true;
        }
        return Datatables::of($Ncontracts)
            ->addColumn('Company', function ($Ncontracts) {
                return $Ncontracts->company_user;
            })
            ->addColumn('name', function ($Ncontracts) {
                return $Ncontracts->namecontract;
            })
            ->addColumn('code', function ($Ncontracts) {
                return $Ncontracts->contract_code;
            })
            ->addColumn('direction', function ($Ncontracts) {
                if (empty($Ncontracts->direction) == true) {
                    return " ---------- ";
                } else {
                    return $Ncontracts->direction;
                }
            })
            ->addColumn('carrier', function ($Ncontracts) {
                if (empty($Ncontracts->carriers) != true) {
                    return $Ncontracts->carriers;
                } else {
                    return " ------------------ ";
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
                return '<span id="userLoad' . $Ncontracts->id . '">' . $Ncontracts->username_load . '</span>';
            })
            ->addColumn('time_elapsed', function ($Ncontracts) {
                if (empty($Ncontracts->time_elapsed) != true) {
                    return $Ncontracts->time_elapsed;
                } else {
                    return '<span id="timeElapsed' . $Ncontracts->id . '"> ------------------ </span>';
                }
            })
            ->addColumn('status', function ($Ncontracts) {
                $color = '';    
                    if (strnatcasecmp($Ncontracts->status, 'Pending') == 0) {
                        //$color = 'color:#031B4E';
                        $color = 'color:#f81538';
                    } else if (strnatcasecmp($Ncontracts->status, 'Processing') == 0) {
                        $color = 'color:#5527f0';
                    } else if (strnatcasecmp($Ncontracts->status, 'Review') == 0) {
                        $color = 'color:#e07000';
                    } else if (strnatcasecmp($Ncontracts->status, 'Done') == 0) {
                        $color = 'color:#04950f';
                    } else if (strnatcasecmp($Ncontracts->status, 'Clarification needed') == 0) {
                        $color = 'color:#e11584';
                    }

                if ($Ncontracts->erased_contract == false || empty($Ncontracts->erased_contract) == true) {
                    return '<a href="#" onclick="showModal(' . $Ncontracts->id . ')"style="' . $color . '" id="statusHrf' . $Ncontracts->id . '" class="statusHrf' . $Ncontracts->id . '">' . $Ncontracts->status . '</a>
                    &nbsp;
                    <samp class="la la-pencil-square-o statusHrf' . $Ncontracts->id . '" for="" id="statusSamp' . $Ncontracts->id . '"  style="font-size:15px;' . $color . '"></samp>';

                }else{
                    return '<a  style="' . $color . '" id="statusHrf' . $Ncontracts->id . '" class="statusHrf' . $Ncontracts->id . '">' . $Ncontracts->status . '</a>
                    &nbsp;
                    <samp class="la la-unlock" id="statusSamp' . $Ncontracts->id . '" class="statusHrf' . $Ncontracts->id . '" for="" style="' . $color . '"></samp>';
                }

               
            })
            ->addColumn('action', function ($Ncontracts) use ($permiso_eliminar) {
            if ($Ncontracts->erased_contract == false || empty($Ncontracts->erased_contract) == true) {
                $buttons = '&nbsp;&nbsp;
                <a href="' . route("RequestImportationLcl.show", $Ncontracts->id) . '" title="Download File">
                    <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E"></samp>
                </a>&nbsp;&nbsp;';
                $eliminiar_buton = '
                <a href="#" class="eliminarrequest" data-id-request="' . $Ncontracts->id . '" data-info="id:' . $Ncontracts->id . ' References: ' . $Ncontracts->namecontract . '"  title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>';

                if ($permiso_eliminar) {
                    $buttons = $buttons . $eliminiar_buton;
                }

                if (empty($Ncontracts->contract) != true) {
                    $butPrCt = '
                <a href="/ImportationLCL/RequestProccessLCL/' . $Ncontracts->contract . '/2/' . $Ncontracts->id . '" title="Proccess LCL Contract">
                    <samp class="la la-cogs" style="font-size:20px; color:#04950f"></samp>
                </a>
                &nbsp;&nbsp;
                <a href="#" title="Edit LCL Contract">
                    <samp class="la la-edit" onclick="editcontract(' . $Ncontracts->contract . ')" style="font-size:20px; color:#04950f"></samp>
                </a>
                    ';
                    $buttons = $butPrCt . $buttons;
                } else {
                    $butPrRq = '
                <a href="/ImportationLCL/RequestProccessLCL/' . $Ncontracts->id . '/1/0" title="Proccess LCL Request">
                    <samp class="la la-cogs" style="font-size:20px; color:#D85F00"></samp>
                </a>';
                    $buttons = $butPrRq . $buttons;
                }
            }else{
                $delete = '<center><h5 style="color:#f81538"><u>Contract Deleted By Customer </u></h5></center>';
                $change_status_erased = '
                <center><a href="#" class="eliminarrequest" data-id-request="' . $Ncontracts->id . '" data-info="id:' . $Ncontracts->id . '"  title="Delete" >
                <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a></center>';
                $buttons = $delete . $change_status_erased;
            }    

                return $buttons;
            })

            ->make();
    }

    //lista todos los request pero por compa??ia
    public function listClient($id)
    {
        $Ncontracts = NewContractRequestLcl::where('company_user_id', $id)->get();
        //dd($Ncontracts[0]['companyuser']['name']);

        return Datatables::of($Ncontracts)
            ->addColumn('Company', function ($Ncontracts) {
                return $Ncontracts->companyuser->name;
            })
            ->addColumn('name', function ($Ncontracts) {
                return $Ncontracts->namecontract;
            })
            ->addColumn('number', function ($Ncontracts) {
                return $Ncontracts->numbercontract;
            })
            ->addColumn('validation', function ($Ncontracts) {
                return $Ncontracts->validation;
            })
            ->addColumn('date', function ($Ncontracts) {
                return $Ncontracts->created;
            })
            ->addColumn('updated', function ($Ncontracts) {
                if (empty($Ncontract->updated) != true) {
                    return Carbon::parse($Ncontract->updated)->format('d-m-Y h:i:s');
                } else {
                    return '00-00-0000 00:00:00';
                }
            })
            ->addColumn('user', function ($Ncontracts) {
                return $Ncontracts->user->name . ' ' . $Ncontracts->user->lastname;
            })
            ->addColumn('status', function ($Ncontracts) {
                $color = '';
                if (strnatcasecmp($Ncontracts->status, 'Pending') == 0) {
                    //$color = 'color:#031B4E';
                    $color = 'color:#f81538';
                } else if (strnatcasecmp($Ncontracts->status, 'Processing') == 0) {
                    $color = 'color:#5527f0';
                } else if (strnatcasecmp($Ncontracts->status, 'Review') == 0) {
                    $color = 'color:#e07000';
                } else if (strnatcasecmp($Ncontracts->status, 'Done') == 0) {
                    $color = '#04950f';
                } else if (strnatcasecmp($Ncontracts->status, 'Clarification needed') == 0) {
                    $color = '#e11584';
                }

                return '<label style="' . $color . '">' . $Ncontracts->status . '</label>';
            })
            ->addColumn('action', function ($Ncontracts) {
                return '<a href="/RequestsLcl/RequestImportationLcl/' . $Ncontracts->id . '" title="Download File">
                    <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E"></samp>
                </a>';
            })

            ->make();
    }

    public function store(Request $request)
    {
        //dd($request->all());
    }

    public function store2(StoreNewRequestLcl $request)
    {
        //Validate if the company has remaining requests
        /**$quota = $this->validateQuota($request->CompanyUserId);
        if(!$quota){
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'You have exceeded your contract quota or you are not enabled to create new requests. Please contact our support or sales team.');
            return redirect()->route('Request.importaion.lcl');
        }**/
        $fileBoll = false;
        $time = new \DateTime();
        $now = $time->format('dmY_His');
        $now2 = $time->format('Y-m-d H:i:s');
        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());

        //obtenemos el nombre del archivo
        $nombre = $file->getClientOriginalName();
        $nombre = quitar_caracteres($nombre);
        $nombre = $now . '_' . $nombre;
        // $fileName = HelperAll::removeAcent($nombre);
        $info_file = pathinfo($nombre);
        $ext = (strtoupper($info_file['extension']) == 'PDF') ? 'PDF' : 'EXCEL';
        $fileBoll = \Storage::disk('LclRequest')->put($nombre, \File::get($file));

        $typeVal = 1;
        $arreglotype = '';

        $data = '';
        $type = '';
        $type = json_encode($type);
        $data = json_encode($data);
        if ($fileBoll) {

            $direction_id = $request->direction;
            $CompanyUserId = $request->CompanyUserId;

            $contract = new ContractLcl();
            $contract->name = $request->name;
            $validity = explode('/', $request->validation_expire);
            $contract->validity = $validity[0];
            $contract->expire = $validity[1];
            $contract->status = 'incomplete';
            //$contract->comments         = '';
            $contract->company_user_id = $CompanyUserId;
            $contract->direction_id = $direction_id;
            $contract->user_id = $request->user;
            $contract->save();

            //Creating custom code
            $contract->createCustomCode();
            
            $Contract_id = $contract->id;

            foreach ($request->carrierM as $carrierVal) {
                ContractCarrierLcl::create([
                    'carrier_id' => $carrierVal,
                    'contract_id' => $Contract_id,
                ]);
            }

            $Ncontract = new NewContractRequestLcl();
            $Ncontract->namecontract = $request->name;
            $Ncontract->validation = $request->validation_expire;
            $Ncontract->direction_id = $direction_id;
            $Ncontract->company_user_id = $CompanyUserId;
            $Ncontract->namefile = $nombre;
            $Ncontract->user_id = $request->user;
            $Ncontract->created = $now2;
            $Ncontract->type = $type;
            $Ncontract->data = $data;
            $Ncontract->contract_id = $Contract_id;
            $Ncontract->save();

            $Ncontract->setAttribute('carrier', null);
            $Ncontract->type = 'LCL';
            foreach ($request->carrierM as $carrierVal) {
                RequetsCarrierLcl::create([
                    'carrier_id' => $carrierVal,
                    'request_id' => $Ncontract->id,
                ]);
                $Ncontract->carrier = $carrierVal;
                $this->trackEvents("new_request_by_carrier", $Ncontract);
            }

            if (env('APP_VIEW') == 'operaciones') {
                ProcessContractFile::dispatch($Ncontract->id, $Ncontract->namefile, 'lcl', 'request')->onQueue('operaciones');
            } else {
                ProcessContractFile::dispatch($Ncontract->id, $Ncontract->namefile, 'lcl', 'request');
            }

            $user = User::find($request->user);
            $message = "There is a new request from " . $user->name . " - " . $user->companyUser->name;
            $user->notify(new SlackNotification($message));
            $admins = User::where('type', 'admin')->get();
            $message = 'has created an new request: ' . $Ncontract->id;
            NotificationsJob::dispatch('Request-Lcl', [
                'user' => $request->user,
                'ncontract' => $Ncontract->toArray(),
            ]);
            foreach ($admins as $userNotifique) {
                /*\Mail::to($userNotifique->email)->send(new NewRequestLclToAdminMail(
                $userNotifique->toArray(),
                $user->toArray(),
                $Ncontract->toArray()));*/
                $userNotifique->notify(new N_general($user, $message));
            }


            if (env('APP_VIEW') == 'operaciones') {
                ValidateTemplateLclJob::dispatch($Ncontract->id)->onQueue('operaciones');
            } else {
                ValidateTemplateLclJob::dispatch($Ncontract->id);
            }
            $Ncontract->setAttribute('file_ext', $ext);
            $this->trackEvents("new_request_Lcl", $Ncontract);

            // EVENTO INTERCOM
            $event = new EventIntercom();
            $event->event_newRequestLCL();

            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.content', 'Your request was created');
            return redirect()->route('contractslcl.index');
        } else {

            $request->session()->flash('message.nivel', 'error');
            $request->session()->flash('message.content', 'Your request was not created');
            return redirect()->route('contractslcl.index');
        }
    }

    public function showStatus($id)
    {
        $requests = NewContractRequestLcl::find($id);
        //dd($requests);
        $status = $requests->status;
        $status_arr = [];
        if ($status == 'Pending') {
            $status_arr['Pending'] = 'Pending';
            $status_arr['Processing'] = 'Processing';
        } elseif ($status == 'Processing') {
            $status_arr['Processing'] = 'Processing';
            $status_arr['Review'] = 'Review';
            $status_arr['Clarification needed'] = 'Clarification needed';
        } elseif ($status == 'Review' || $status == 'Done' || $status == 'Clarification needed') {
            $status_arr['Processing'] = 'Processing';
            $status_arr['Review'] = 'Review';
            $status_arr['Done'] = 'Done';
        }
        return view('RequestsLcl.Body-Modals.edit', compact('requests', 'status_arr'));
    }

    //Para descargar el archivo
    public function show(Request $request, $id)
    {
        $Ncontract = NewContractRequestLcl::find($id);
        $time = new \DateTime();
        $now = $time->format('d-m-y');
        $company = CompanyUser::find($Ncontract->company_user_id);
        $extObj = new \SplFileInfo($Ncontract->namefile);
        $ext = $extObj->getExtension();
        $name = $Ncontract->id . '-' . $company->name . '_' . $now . '-LCL.' . $ext;
        $originales = '????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????';
        $modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyyby';
        $name = utf8_decode($name);
        $name = strtr($name, utf8_decode($originales), $modificadas);

        $success = false;
        $descarga = null;

        if (Storage::disk('s3_upload')->exists('Request/LCL/' . $Ncontract->namefile, $name)) {
            $success = true;
            return Storage::disk('s3_upload')->download('Request/LCL/' . $Ncontract->namefile, $name);
        } elseif (Storage::disk('s3_upload')->exists('contracts/' . $Ncontract->namefile, $name)) {
            $success = true;
            return Storage::disk('s3_upload')->download('contracts/' . $Ncontract->namefile, $name);
        } elseif (Storage::disk('LclRequest')->exists($Ncontract->namefile, $name)) {
            $success = true;
            return Storage::disk('LclRequest')->download($Ncontract->namefile, $name);
        } elseif (Storage::disk('UpLoadFile')->exists($Ncontract->namefile, $name)) {
            $success = true;
            return Storage::disk('UpLoadFile')->download($Ncontract->namefile, $name);
        } else {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'Error. File not found');
            return back();
        }

        /*try{
    return Storage::disk('s3_upload')->download('Request/LCL/'.$Ncontract->namefile,$name);
    } catch(\Exception $e){
    try{
    return Storage::disk('s3_upload')->download('contracts/'.$Ncontract->namefile,$name);
    } catch(\Exception $e){
    try{
    return Storage::disk('LclRequest')->download($Ncontract->namefile,$name);
    } catch(\Exception $e){
    try{
    return Storage::disk('UpLoadFile')->download($Ncontract->namefile,$name);
    } catch(\Exception $e){
    $request->session()->flash('message.nivel', 'danger');
    $request->session()->flash('message.content', 'Error. File not found');
    return back();
    }
    }
    }
    }*/
    }

    public function edit($id)
    {
        //
    }

    // Update Request Importation ----------------------------------------------------------
    public function UpdateStatusRequest()
    {
        $id = $_REQUEST['id'];
        $status = $_REQUEST['status'];
        // $id     = 1;
        // $status = 'Done';

        try {

            $time = new \DateTime();
            $now2 = $time->format('Y-m-d H:i:s');

            $Ncontract = NewContractRequestLcl::find($id);
            $Ncontract->status = $status;
            $Ncontract->updated = $now2;
            $Ncontract->setAttribute('module', 'LCL');
            if ($Ncontract->username_load == 'Not assigned') {
                $Ncontract->username_load = \Auth::user()->name . ' ' . \Auth::user()->lastname;
            }

            if ($Ncontract->status == 'Processing') {
                if ($Ncontract->time_star_one == false) {
                    $Ncontract->time_star = $now2;
                    $Ncontract->time_star_one = true;
                }
                //Calling Mix Panel's event
                $this->trackEvents("Request_Status_lcl", $Ncontract);
                if ($Ncontract->contract_id != null) {
                    $contract = ContractLcl::find($Ncontract->contract_id);
                    $contract->status = 'incomplete';
                    $contract->update();
                }
            } elseif ($Ncontract->status == 'Review' || $Ncontract->status == 'Clarification needed') {
                if ($Ncontract->time_total == null) {
                    $fechaEnd = Carbon::parse($now2);
                    if (empty($Ncontract->time_star) == true) {
                        $Ncontract->time_total = 'It did not go through the processing state';
                    } else {
                        $time_exacto = '';
                        $fechaStar = Carbon::parse($Ncontract->time_star);
                        $time_exacto = $fechaEnd->diffInMinutes($fechaStar);
                        if ($time_exacto == 0 || $time_exacto == '0') {
                            $time_exacto = '1 minute';
                        } else {
                            $time_exacto = $time_exacto . ' minutes';
                        }
                        $Ncontract->time_total = $time_exacto;
                    }
                    if($Ncontract->status == 'Review'){
                        $this->trackEvents("Request_Review", $Ncontract);
                    }
                }
                //Calling Mix Panel's event
                if ($Ncontract->contract_id != null && $Ncontract->status == 'Clarification needed') {
                    $contract = ContractLcl::find($Ncontract->contract_id);
                    $contract->status = 'Clarification needed';
                    $contract->update();
                }


            } elseif ($Ncontract->status == 'Done') {

                if ($Ncontract->time_manager == null) {
                    $fechaEnd = Carbon::parse($now2);
                    $fechaStar = Carbon::parse($Ncontract->created);
                    $time_manager = number_format($fechaEnd->diffInMinutes($fechaStar) / 60, 2);
                    $Ncontract->time_manager = $time_manager . ' hours';
                    //$Ncontract->time_manager = $fechaEnd->diffInHours($fechaStar).' hours';
                }

                if ($Ncontract->sentemail == false) {
                    $users = User::all()->where('company_user_id', '=', $Ncontract->company_user_id);
                    $message = 'The request was processed N??: ' . $Ncontract->id;
                    foreach ($users as $user) {

                        $user->notify(new N_general(\Auth::user(), $message));
                    }

                    $usercreador = User::find($Ncontract->user_id);
                    $message = "The importation " . $Ncontract->id . " was completed";
                    $usercreador->notify(new SlackNotification($message));
                    if (env('APP_VIEW') == 'operaciones') {
                        SendEmailRequestLclJob::dispatch($usercreador->toArray(), $id)->onQueue('operaciones');
                    } else {
                        SendEmailRequestLclJob::dispatch($usercreador->toArray(), $id);
                    }
                }
                if ($Ncontract->contract_id != null) {
                    $contract = ContractLcl::find($Ncontract->contract_id);
                    $contract->status = 'publish';
                    $contract->update();
                }
            }

            //Calling Mix Panel's event
            $this->trackEvents("Request_Status_lcl", $Ncontract);
            unset($Ncontract->module);
            $Ncontract->save();

            if (strnatcasecmp($Ncontract->status, 'Pending') == 0) {
                $color = '#f81538';
            } else if (strnatcasecmp($Ncontract->status, 'Processing') == 0) {
                $color = '#5527f0';
            } else if (strnatcasecmp($Ncontract->status, 'Review') == 0) {
                $color = '#e07000';
            } else if (strnatcasecmp($Ncontract->status, 'Done') == 0) {
                $color = '#04950f';
            } else if (strnatcasecmp($Ncontract->status, 'Clarification needed') == 0) {
                $color = '#e11584';
            }
            return response()->json($data = ['data' => 1, 'status' => $Ncontract->status, 'color' => $color, 'request' => $Ncontract->toArray()]);
        } catch (\Exception $e) {
            return response()->json($data = ['data' => 2]);
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
    // Api Request SendEmail ---------------------------------------------------------------
    public function sendEmailRequest(Request $request)
    {
        $success = false;
        $error = null;
        try {
            $id = $request->request_id;
            $Ncontract = NewContractRequestLcl::find($id);
            $users = User::all()->where('company_user_id', '=', $Ncontract->company_user_id);
            $message = 'The request was processed N??: ' . $Ncontract->id;
            foreach ($users as $user) {
                $user->notify(new N_general(\Auth::user(), $message));
            }

            $usercreador = User::find($Ncontract->user_id);
            $message = "The importation " . $Ncontract->id . " was completed";
            $usercreador->notify(new SlackNotification($message));
            if (env('APP_VIEW') == 'operaciones') {
                SendEmailRequestLclJob::dispatch($usercreador->toArray(), $id)->onQueue('operaciones');
            } else {
                SendEmailRequestLclJob::dispatch($usercreador->toArray(), $id);
            }
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            Log::error($e);
            $error = $e->getMessage();
        } finally {
            return response()->json(['success' => $success, 'error' => $error]);
        }
    }

    // Delete Request Importation ----------------------------------------------------------
    public function destroyRequest($id)
    {
        try {
            $Ncontract = NewContractRequestLcl::find($id);
            Storage::disk('LclRequest')->delete($Ncontract->namefile);
            $Ncontract->delete();
            return 1;
        } catch (\Exception $e) {
            return 2;
        }
    }

    // New Request Importation -------------------------------------------------------------
    public function LoadViewRequestImporContractLcl()
    {
        $harbor = harbor::all()->pluck('display_name', 'id');
        $carrier = carrier::all()->pluck('name', 'id');
        $direction = [null => 'Please Select'];
        $direction2 = Direction::all();
        foreach ($direction2 as $d) {
            $direction[$d['id']] = $d->name;
        }
        $user = \Auth::user();
        return view('RequestsLcl.NewRequest', compact('harbor', 'carrier', 'user', 'direction'));
    }

    // EXPORT Request Importation ----------------------------------------------------------
    public function export(Request $request)
    {
        $dates = explode('/', $request->between);
        $dateStart = trim($dates[0]);
        $dateEnd = trim($dates[1]);
        $now = new \DateTime();
        $now = $now->format('dmY_His');
        $dateEnd = \Carbon\Carbon::parse($dateEnd);
        $dateEnd = $dateEnd->addDay()->format('Y-m-d');
        $countNRq = NewContractRequestLcl::whereBetween('created', [$dateStart . ' 00:00:00', $dateEnd . ' 23:59:59'])->count();

        if ($countNRq <= 100) {
            $nameFile = 'Request_Lcl_' . $now;
            $data = PrvRequest::RequestLclBetween($dateStart, $dateEnd);

            //dd($data->chunk(2));

            $myFile = Excel::create($nameFile, function ($excel) use ($data) {

                $excel->sheet('REQUEST_LCL', function ($sheet) use ($data) {
                    $sheet->cells('A1:N1', function ($cells) {
                        $cells->setBackground('#2525ba');
                        $cells->setFontColor('#ffffff');
                        //$cells->setValignment('center');
                    });

                    $sheet->setWidth(array(
                        'A' => 10,
                        'B' => 30,
                        'C' => 25,
                        'D' => 10,
                        'E' => 20,
                        'F' => 25,
                        'G' => 25,
                        'H' => 20,
                        'I' => 20,
                        'J' => 25,
                        'K' => 25,
                        'L' => 15,
                        'M' => 15,
                        'N' => 15,
                    ));

                    $sheet->row(1, array(
                        "Id",
                        "Company",
                        "Reference",
                        "Direction",
                        "Carrier",
                        "Validation",
                        "Date",
                        "User",
                        "Username load",
                        "Time Start",
                        "Time End",
                        "Time Elapsed. Minutes",
                        "Management Time. Hours",
                        "Status",
                    ));
                    $i = 2;

                    $data = $data->chunk(500);
                    $data = $data->toArray();;
                    foreach ($data as $nrequests) {
                        foreach ($nrequests as $nrequest) {
                            $sheet->row($i, array(
                                "Id" => $nrequest['id'],
                                "Company" => $nrequest['company'],
                                "Reference" => $nrequest['reference'],
                                "Direction" => $nrequest['direction'],
                                "Carrier" => $nrequest['carrier'],
                                "Validation" => $nrequest['validation'],
                                "Date" => $nrequest['date'],
                                "User" => $nrequest['user'],
                                "Username load" => $nrequest['username_load'],
                                "Time Start" => $nrequest['time_start'],
                                "Time End" => $nrequest['time_end'],
                                "Time Elapsed" => $nrequest['time_elapsed'],
                                "Management Time" => $nrequest['time_manager'],
                                "Status" => $nrequest['status'],
                            ));
                            $sheet->setBorder('A1:N' . $i, 'thin');

                            $sheet->cells('F' . $i, function ($cells) {
                                $cells->setAlignment('center');
                            });

                            $sheet->cells('G' . $i, function ($cells) {
                                $cells->setAlignment('center');
                            });

                            $sheet->cells('K' . $i, function ($cells) {
                                $cells->setAlignment('center');
                            });

                            $sheet->cells('J' . $i, function ($cells) {
                                $cells->setAlignment('center');
                            });

                            $i++;
                        }
                    }
                });
            });

            $myFile = $myFile->string('xlsx'); //change xlsx for the format you want, default is xls
            $response = array(
                'actt' => 1,
                'name' => $nameFile . '.xlsx', //no extention needed
                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($myFile), //mime type of used format
            );
        } else {
            $auth = \Auth::user()->toArray();
            ExportRequestsJob::dispatch($dateStart, $dateEnd, $auth, 'lcl')->onQueue('operaciones');
            $response = array(
                'actt' => 2,
            );
        }
        return response()->json($response);
    }

    // TEST --------------------------------------------------------------------------------
    public function test()
    {
        $dateStart = '2019-08-19 00:00:00';
        $dateEnd = '2019-08-19 23:59:59';
        $data = PrvRequest::RequestLclBetween($dateStart, $dateEnd);
        $data = NewContractRequestLcl::whereBetween('created', [$dateStart, $dateEnd])->get();
        dd($data);
    }

    public function similarcontracts(Request $request, $id)
    {
        $contracts = ContractLcl::select([
            'id',
            'name',
            'company_user_id',
            'account_id',
            'direction_id',
            'validity',
            'expire',
        ]);

        return Datatables::of($contracts->where('company_user_id', $id))
            ->filter(function ($query) use ($request, $id) {
                if ($request->has('direction') && $request->get('direction') != null) {
                    $query->where('direction_id', '=', $request->get('direction'));
                } else {
                    $query;
                }
                if ($request->has('carrierM')) {
                    $query->whereHas('carriers', function ($q) use ($request) {
                        $q->whereIn('carrier_id', $request->get('carrierM'));
                    });
                }
                if ($request->has('dateO') && $request->get('dateO') != null && $request->has('dateT') && $request->get('dateT') != null) {
                    $query->where('validity', '=', $request->get('dateO'))->where('expire', '=', $request->get('dateT'));
                }
            })
            ->addColumn('carrier', function ($contracts) {
                $dd = $contracts->load('carriers.carrier');
                if (count($dd->carriers) != 0) {
                    return str_replace(['[', ']', '"'], ' ', $dd->carriers->pluck('carrier')->pluck('name'));
                } else {
                    return '-------';
                }
            })
            ->addColumn('direction', function ($contracts) {
                $dds = $contracts->load('direction');
                if (count((array) $dds->direction) != 0) {
                    return $dds->direction->name;
                } else {
                    return '-------';
                }
            })
            ->make(true);
    }
}
