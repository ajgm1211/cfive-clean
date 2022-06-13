<?php

namespace App\Http\Controllers;

use App\Carrier;
use App\CompanyUser;
use App\Container;
use App\Contract;
use App\ContractCarrier;
use App\Direction;
use App\GroupContainer;
use App\Http\Traits\MixPanelTrait;
use App\Http\Traits\SearchTrait;
use App\Jobs\ExportRequestsJob;
use App\Jobs\NotificationsJob;
use App\Jobs\SelectionAutoImportJob;
use App\Jobs\SendEmailRequestFclJob;
use App\Jobs\ValidateTemplateJob;
use App\NewContractRequest;
use App\Notifications\N_general;
use App\Notifications\SlackNotification;
use App\RequetsCarrierFcl;
use App\User;
use EventIntercom;
use Excel;
use HelperAll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer as Writer;
use PrvRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\Datatables\Datatables;
use \Carbon\Carbon;

class RequestFclV2Controller extends Controller
{
    use SearchTrait, MixPanelTrait;
    // Load View All Request
    public function index(Request $request)
    {
        $now = Carbon::now();
        $now2 = Carbon::now();
        $date_start = $now->subMonth(1)->format('Y-m-d');
        $date_end = $now2->format('Y-m-d');
        $date = $date_start . ' / ' . $date_end;
        return view('RequestV2.Fcl.show', compact('date'));
    }

    // Load Datatable
    public function create(Request $request)
    {
        $date_start = $request->dateS;
        $date_end = $request->dateE;
        $date_end = Carbon::parse($date_end);
        $date_end = $date_end->addDay(1);
        $Ncontract = DB::select('call  select_request_fcl_v2("' . $date_start . '","' . $date_end . '")');

        $Ncontracts = $Ncontract;

        /*  foreach ($Ncontract as $contract) {
        $request_id = NewContractRequest::find($contract->id);

        if ($request_id->status_erased == 0) {
        $Ncontracts[] = $contract;
        }
        }*/

        $permiso_eliminar = false;
        $user = \Auth::user();
        if ($user->hasAnyPermission([1])) {
            $permiso_eliminar = true;
        }
        $groupContainers = GroupContainer::all();
        return Datatables::of($Ncontracts)
            ->addColumn('Company', function ($Ncontracts) {
                return $Ncontracts->company_user;
            })
            ->addColumn('equiment', function ($Ncontracts) use ($groupContainers) {
                $color = '#012586';
                //$color = '#058b0a';
                if (json_decode($Ncontracts->request_data, true) != null) {
                    $data = json_decode($Ncontracts->request_data, true);
                    if (!empty($data['group_containers'])) {
                        $name = $data['group_containers']['name'];
                        $groupContainers = $groupContainers->firstWhere('id', $data['group_containers']['id']);
                        $data_gp = json_decode($groupContainers->data, true);
                        $color = $data_gp['color'];
                    } else {
                        $name = 'DRY \\';
                    }
                } else {
                    $name = 'DRY \\';
                }
                return '<span style="color:' . $color . '"><strong>' . $name . '</strong></span>';
            })
            ->addColumn('name', function ($Ncontracts) {
                return $Ncontracts->namecontract;
            })
            ->addColumn('code', function ($Ncontracts) {
                return $Ncontracts->contract_code;
            })
            ->addColumn('number', function ($Ncontracts) {
                return $Ncontracts->numbercontract;
            })
            ->addColumn('direction', function ($Ncontracts) {
                if (empty($Ncontracts->direction) == true) {
                    return " -------- ";
                } else {
                    return $Ncontracts->direction;
                }
            })
            ->addColumn('carrier', function ($Ncontracts) {
                if (count((array) $Ncontracts->carriers) >= 1) {
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

                $color = HelperAll::statusColorRq($Ncontracts->status);
                $color = 'color:' . $color;
                if ($Ncontracts->erased_contract == false || empty($Ncontracts->erased_contract) == true) {
                    return '<a href="#" onclick="showModal(' . $Ncontracts->id . ')"style="' . $color . '" id="statusHrf' . $Ncontracts->id . '" class="statusHrf' . $Ncontracts->id . '">' . $Ncontracts->status . '</a>
                &nbsp;
                <samp class="la la-pencil-square-o" id="statusSamp' . $Ncontracts->id . '" class="statusHrf' . $Ncontracts->id . '" for="" style="' . $color . '"></samp>';
                } else {
                    return '<a  style="' . $color . '" id="statusHrf' . $Ncontracts->id . '" class="statusHrf' . $Ncontracts->id . '">' . $Ncontracts->status . '</a>
                    &nbsp;
                    <samp class="la la-unlock" id="statusSamp' . $Ncontracts->id . '" class="statusHrf' . $Ncontracts->id . '" for="" style="' . $color . '"></samp>';
                }
            })
            ->addColumn('action', function ($Ncontracts) use ($permiso_eliminar) {

                if ($Ncontracts->erased_contract == false || empty($Ncontracts->erased_contract) == true) {
                    if (empty($Ncontracts->namefile) != true) {
                        $disk = 'storage';
                    } else {
                        $disk = 'media';
                    }

                    $buttons = '
				    <a href="' . route("RequestFcl.donwload.files", [$Ncontracts->id, $disk]) . '" title="Download File">
                        <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E"></samp>
                    </a>&nbsp;&nbsp;';

                    $eliminiar_buton = '
                <a href="#" class="eliminarrequest" data-id-request="' . $Ncontracts->id . '" data-info="id:' . $Ncontracts->id . ' Number Contract: ' . $Ncontracts->numbercontract . '"  title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>';

                    if ($permiso_eliminar) {
                        $buttons = $buttons . $eliminiar_buton;
                    }

                    if (empty($Ncontracts->contract) != true) {
                        $butPrCt = '';
                        $buttonDp = '';
                        if (strnatcasecmp($Ncontracts->status, 'Done') == 0) {
                            $hidden = '';
                        } else {
                            $hidden = 'hidden';
                        }
                        $buttonDp = "<a href='#' id='statusHiden" . $Ncontracts->id . "' " . $hidden . " class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' onclick='AbrirModal(\"DuplicatedContractOtherCompany\"," . $Ncontracts->contract . "," . $Ncontracts->id . ")'  title='Duplicate to another company'>                      <i style='color:#b90000' class='la la-copy'></i></a>";
                        if (strnatcasecmp($Ncontracts->status, 'Pending') == 0) {
                            $hiddenPrCt = 'hidden';
                        } else {
                            $hiddenPrCt = '';
                        }
                        $butPrCt = '<a href="/Importation/RequestProccessFCL/' . $Ncontracts->contract . '/2/' . $Ncontracts->id . '" ' . $hiddenPrCt . ' title="Process FCL Contract" class="PrCHidden' . $Ncontracts->id . '"><samp class="la la-cogs" style="font-size:20px; color:#04950f"></samp></a>                    &nbsp;&nbsp;';

                        $butFailsR = '<a href="' . route('Failed.Developer.For.Contracts', [$Ncontracts->contract, 0]) . '" ' . $hiddenPrCt . ' title="Failed - FCL Contract" class="PrCHidden' . $Ncontracts->id . '"><samp class="la la-credit-card" style="font-size:20px;"></samp></a>                    &nbsp;&nbsp;&nbsp;';
                        $validator_color = 'color:#c000d0';
                        $validator_toute = route('check.surchargers', $Ncontracts->contract);
                        if ($Ncontracts->validator_contract == 1) {
                            $validator_color = 'color:#04950f';
                            $validator_toute = route('show.validator.surcharge', $Ncontracts->contract);
                        }
                        $butValidateR = '<a href="' . $validator_toute . '" ' . $hiddenPrCt . ' title="Validator" class="PrCHidden' . $Ncontracts->id . '"><samp class="la la-vine" style="font-size:20px;' . $validator_color . '"></samp></a>  &nbsp;&nbsp;';

                        $buttoEdit = '<a href="#" title="Edit FCL Contract">
                    <samp class="la la-edit" onclick="editcontract(' . $Ncontracts->contract . ')" style="font-size:20px; color:#a56c04"></samp>
                    </a>&nbsp;&nbsp;
                    ';

                        $buttons = $butPrCt . $butFailsR . $butValidateR . $buttonDp . $buttoEdit . $buttons;
                    } else {

                        if (strnatcasecmp($Ncontracts->status, 'Pending') == 0) {
                            $hiddenPrRq = 'hidden';
                        } else {
                            $hiddenPrRq = '';
                        }
                        $butPrRq = '<a href="/Importation/RequestProccessFCL/' . $Ncontracts->id . '/1/0" ' . $hiddenPrRq . ' id="PrCHidden' . $Ncontracts->id . '" title="Process FCL Request">
                    <samp class="la la-cogs" style="font-size:20px; color:#D85F00"></samp>
                    </a>';
                        $buttons = $butPrRq . $buttons;
                    }

                    $excel_button = '&nbsp;&nbsp;
				    <a href="' . route("RequestFcl.edit", [$Ncontracts->id, $disk]) . '" title="Download Template">
                        <samp class="la la-file-excel-o" style="font-size:20px; color:#9a028e"></samp>
                    </a>
                    &nbsp;&nbsp;';
                    $buttons = $excel_button . $buttons;
                } else {

                    $delete = '<center><h5 style="color:#f81538"><u>Contract Deleted By Customer </u></h5></center>';
                    $change_status_erased = '
                    <center><a href="#" class="eliminarrequest" data-id-request="' . $Ncontracts->id . '" data-info="id:' . $Ncontracts->id . ' Number Contract: ' . $Ncontracts->numbercontract . '"  title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a></center>';
                    $buttons = $delete . $change_status_erased;
                }
                return $buttons;
            })->make();
    }

    // Crea un nueva Solicitud. (Cliente)
    public function store(Request $request)
    {
        //dd($request->all());
        $CompanyUserId = $request->CompanyUserId;
        $direction_id = $request->direction;
        $carriers = $request->carrierM;
        $name = $request->name;
        $user = $request->user;
        $groupContainer = $request->container_type;
        $containers = $request->equipment;
        $validationexp = $request->validation_expire;
        $validity = explode('/', $validationexp);
        $time = new \DateTime();
        $now = $time->format('dmY_His');
        $now2 = $time->format('Y-m-d H:i:s');
        $file = $request->input('document');
        $ext = null;
        if (!empty($file)) {
            $info_file = pathinfo($file);
            $ext = (strtoupper($info_file['extension']) == 'PDF') ? 'PDF' : 'EXCEL';
            $gpContainer = GroupContainer::find($groupContainer);
            $ArrayData['group_containers'] = ['id' => $gpContainer->id, 'name' => $gpContainer->name];
            $ArrayData['containers'] = [];
            foreach ($containers as $containerId) {
                $container = Container::find($containerId);
                $ArrayData['containers'][] = ['id' => $container->id, 'name' => $container->name, 'code' => $container->code];
            }
            $data = json_encode($ArrayData);

            $contract = new Contract();
            $contract->name = $name;
            $contract->validity = $validity[0];
            $contract->expire = $validity[1];
            $contract->direction_id = $direction_id;
            $contract->status = 'incomplete';
            $contract->company_user_id = $CompanyUserId;
            $contract->user_id = Auth::user()->id;
            $contract->gp_container_id = $gpContainer->id;
            $contract->save();

            //Creating custom code
            $contract->createCustomCode();

            $Ncontract = new NewContractRequest();
            $Ncontract->namecontract = $name;
            $Ncontract->validation = $validationexp;
            $Ncontract->direction_id = $direction_id;
            $Ncontract->company_user_id = $CompanyUserId;
            $Ncontract->user_id = $user;
            $Ncontract->created = $now2;
            $Ncontract->username_load = 'Not assigned';
            $Ncontract->data = $data;
            $Ncontract->contract_id = $contract->id;
            $Ncontract->save();
            $Ncontract->setAttribute('carrier', null);
            foreach ($carriers as $carrierVal) {
                ContractCarrier::create([
                    'carrier_id' => $carrierVal,
                    'contract_id' => $contract->id,
                ]);

                RequetsCarrierFcl::create([
                    'carrier_id' => $carrierVal,
                    'request_id' => $Ncontract->id,
                ]);

                $Ncontract->carrier = $carrierVal;
                $Ncontract->type = 'FCL';

                //Calling Mix Panel's event
                $this->trackEvents("new_request_by_carrier", $Ncontract);
            }

            $contract->addMedia(storage_path('tmp/request/' . $file))->preservingOriginal()->toMediaCollection('document', 'contracts3');
            $Ncontract->addMedia(storage_path('tmp/request/' . $file))->toMediaCollection('document', 'FclRequest-New');
            $ext_at_sl = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            if (
                strnatcasecmp($ext_at_sl, 'xls') == 0 ||
                strnatcasecmp($ext_at_sl, 'xlsx') == 0 ||
                strnatcasecmp($ext_at_sl, 'csv') == 0
            ) {
                if (env('APP_VIEW') == 'operaciones') {
                    SelectionAutoImportJob::dispatch($Ncontract->id, 'fcl')->onQueue('operaciones');
                } else {
                    SelectionAutoImportJob::dispatch($Ncontract->id, 'fcl');
                }
            }

            if (env('APP_VIEW') == 'operaciones') {
                ValidateTemplateJob::dispatch($Ncontract->id)->onQueue('operaciones');
            } else {
                ValidateTemplateJob::dispatch($Ncontract->id);
            }

            $user = User::find($request->user);
            $message = "There is a new request from " . $user->name . " - " . $user->companyUser->name;
            $user->notify(new SlackNotification($message));
            $admins = User::where('type', 'admin')->get();
            $message = 'has created a new request: ' . $Ncontract->id;

            //Calling Mix Panel's event
            $Ncontract->setAttribute('file_ext', $ext);
            $this->trackEvents("new_request_Fcl", $Ncontract);

            // EVENTO INTERCOM
            $event = new EventIntercom();
            $event->event_newRequest();

            NotificationsJob::dispatch('Request-Fcl', [
                'user' => $request->user,
                'ncontract' => $Ncontract->toArray(),
            ]);

            foreach ($admins as $userNotifique) {
                $userNotifique->notify(new N_general($user, $message));
            }

            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.content', 'Your request was created');
            return redirect()->route('new.contracts.index');
        } else {

            $request->session()->flash('message.nivel', 'error');
            $request->session()->flash('message.content', 'Your request was not created');
            return redirect()->route('new.contracts.index');
        }
    }

    public function newRequest(Request $request)
    {
        $carrier = carrier::all()->pluck('name', 'id');
        $direction = HelperAll::addOptionSelect(Direction::all(), 'id', 'name');
        $groupContainer = HelperAll::addOptionSelect(GroupContainer::all(), 'id', 'name');
        //C5 select
        $contain = Container::pluck('code', 'id');
        $contain->prepend('Select an option', '');
        $group_contain = GroupContainer::pluck('name', 'id');
        $containers = Container::get();
        $equipment = array('1', '2', '3');
        $validateEquipment = $this->validateEquipment($equipment, $containers);
        $containerType = $validateEquipment['gpId'];

        $containers = Container::pluck('name', 'id');
        $user = \Auth::user();

        return view('RequestV2.Fcl.index', compact('carrier', 'user', 'direction', 'groupContainer', 'containers', 'containerType', 'equipment', 'group_contain', 'contain'));
    }

    //Carga los Status segun la posicion actual
    public function show($id)
    {
        $requests = NewContractRequest::find($id);
        $status = $requests->status;
        $status_arr = [];
        if ($status == 'Pending') {
            $status_arr['Pending'] = 'Pending';
            $status_arr['Processing'] = 'Processing';
        } elseif ($status == 'Processing') {
            $status_arr['Processing'] = 'Processing';
            $status_arr['Review'] = 'Review';
            $status_arr['Clarification needed'] = 'Clarification needed';
        } elseif ($status == 'Imp Finished' || $status == 'Clarification needed') {
            $status_arr['Clarification needed'] = 'Clarification needed';
            $status_arr['Processing'] = 'Processing';
            $status_arr['Imp Finished'] = 'Imp Finished';
            $status_arr['Review'] = 'Review';
        } elseif ($status == 'Review' || $status == 'Done') {
            $status_arr['Processing'] = 'Processing';
            $status_arr['Imp Finished'] = 'Imp Finished';
            $status_arr['Review'] = 'Review';
            $status_arr['Done'] = 'Done';
        }

        return view('RequestV2.Fcl.Body-Modals.edit', compact('requests', 'status_arr'));
    }

    public function UpdateStatusRequest()
    {
        $id = $_REQUEST['id'];
        $status = $_REQUEST['status'];
        // $id     = 1;
        // $status = 'Done';

        $time = new \DateTime();
        $now2 = $time->format('Y-m-d H:i:s');

        try {
            $Ncontract = NewContractRequest::find($id);
            $Ncontract->status = $status;
            $Ncontract->updated = $now2;
            $Ncontract->setAttribute('module', 'FCL');
            if ($Ncontract->username_load == 'Not assigned' || empty($Ncontract->username_load) == true) {
                $Ncontract->username_load = \Auth::user()->name . ' ' . \Auth::user()->lastname;
            }

            if ($Ncontract->status == 'Processing') {
                $this->setStatusContract($Ncontract->contract_id,'incomplete');
                if ($Ncontract->time_star_one == false) {
                    $Ncontract->time_star = $now2;
                    $Ncontract->time_star_one = true;
                }
                //Calling Mix Panel's event
            } elseif ($Ncontract->status == 'Review' || $Ncontract->status == 'Clarification needed') {
                if ($Ncontract->time_total == null) {
                    $fechaEnd = Carbon::parse($now2);
                    if (empty($Ncontract->time_star) == true) {
                        $Ncontract->time_total = 'No time';
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
                }
                if($Ncontract->status == 'Review'){
                    $this->trackEvents("Request_Review", $Ncontract);
                    $this->setStatusContract($Ncontract->contract_id,'incomplete');
                }else if($Ncontract->status == 'Clarification needed') {
                    $this->setStatusContract($Ncontract->contract_id,'Clarification needed');
                }
                //Calling Mix Panel's event
            } elseif ($Ncontract->status == 'Done') {
            /*    $contractObj = Contract::find($Ncontract->contract_id);
                $contractObj->status = 'publish';
                $contractObj->update();*/

                $this->setStatusContract($Ncontract->contract_id,'publish');
                if ($Ncontract->time_manager == null) {
                    $fechaEnd = Carbon::parse($now2);
                    $fechaStar = Carbon::parse($Ncontract->created);
                    $time_manager = number_format($fechaEnd->diffInMinutes($fechaStar) / 60, 2);
                    $Ncontract->time_manager = $time_manager . ' hours';
                    //$Ncontract->time_manager = $fechaEnd->diffInHours($fechaStar).' hours';
                }

                if ($Ncontract->sentemail == false) {
                    $users = User::all()->where('company_user_id', '=', $Ncontract->company_user_id);
                    $message = 'The request ' . $Ncontract->id . ' was processed';
                    foreach ($users as $user) {

                        $user->notify(new N_general(\Auth::user(), $message));
                    }

                    $usercreador = User::find($Ncontract->user_id);
                    $message = "The importation " . $Ncontract->id . " was completed";
                    $usercreador->notify(new SlackNotification($message));
                    if (env('APP_VIEW') == 'operaciones') {
                        SendEmailRequestFclJob::dispatch($usercreador->toArray(), $id)->onQueue('operaciones');
                    } else {
                        SendEmailRequestFclJob::dispatch($usercreador->toArray(), $id)->onQueue('high');
                    }
                }
                //Calling Mix Panel's event
            }
            $this->trackEvents("Request_Status_fcl", $Ncontract);
            unset($Ncontract->module);
            $Ncontract->save();
            $color = HelperAll::statusColorRq($Ncontract->status);

            return response()->json($data = ['data' => 1, 'status' => $Ncontract->status, 'color' => $color, 'request' => $Ncontract]);
        } catch (\Exception $e) {
            print($e);
            return response()->json($data = ['data' => 2]);
        }
    }

    public function setStatusContract($contract_id,$new_status)
    {
        $contractObj = Contract::find($contract_id);
        if($contractObj->status != $new_status ){
            $contractObj->status = $new_status;
            $contractObj->update();
    
        }
 
    }

    public function sendEmailRequest(Request $request)
    {
        $success = false;
        $error = null;
        try {
            $id = $request->request_id;
            $Ncontract = NewContractRequest::find($id);
            $users = User::all()->where('company_user_id', '=', $Ncontract->company_user_id);
            $message = 'The request was processed N°: ' . $Ncontract->id;
            foreach ($users as $user) {
                $user->notify(new N_general(\Auth::user(), $message));
            }

            $usercreador = User::find($Ncontract->user_id);
            $message = "The importation " . $Ncontract->id . " was completed";
            $usercreador->notify(new SlackNotification($message));
            if (env('APP_VIEW') == 'operaciones') {
                SendEmailRequestFclJob::dispatch($usercreador->toArray(), $id)->onQueue('operaciones');
            } else {
                SendEmailRequestFclJob::dispatch($usercreador->toArray(), $id)->onQueue('high');
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

    // Descargar archivos, dependiendo si es Storage o Media
    public function donwloadFiles(Request $request, $id, $selector)
    {
        if (strnatcasecmp($selector, 'media') == 0) {
            $Ncontract = NewContractRequest::find($id);
            $Ncontract->load('companyuser');
            $data = json_decode($Ncontract->data, true);
            $time = new \DateTime();
            $now = $time->format('d-m-y');
            $mediaItem = $Ncontract->getFirstMedia('document');
            $extObj = new \SplFileInfo($mediaItem->file_name);
            $ext = $extObj->getExtension();
            $name = $Ncontract->id . '-' . preg_replace('([^A-Za-z0-9])', '_', $Ncontract->companyuser->name) . '_' . $data['group_containers']['name'] . '_' . $now . '-FLC.' . $ext;
            return Storage::disk('FclRequest-New')->download($mediaItem->id . '/' . $mediaItem->file_name, $name);
        } elseif (strnatcasecmp($selector, 'storage') == 0) {

            $Ncontract = NewContractRequest::find($id);
            $time = new \DateTime();
            $now = $time->format('d-m-y');
            $company = CompanyUser::find($Ncontract->company_user_id);
            $extObj = new \SplFileInfo($Ncontract->namefile);
            $ext = $extObj->getExtension();
            $name = $Ncontract->id . '-' . preg_replace('([^A-Za-z0-9])', '_', $company->name) . '_' . $now . '-FLC.' . $ext;
            $success = false;
            $descarga = null;

            if (Storage::disk('s3_upload')->exists('Request/FCL/' . $Ncontract->namefile, $name)) {
                $success = true;
                return Storage::disk('s3_upload')->download('Request/FCL/' . $Ncontract->namefile, $name);
            } elseif (Storage::disk('s3_upload')->exists('contracts/' . $Ncontract->namefile, $name)) {
                $success = true;
                return Storage::disk('s3_upload')->download('contracts/' . $Ncontract->namefile, $name);
            } elseif (Storage::disk('FclRequest')->exists($Ncontract->namefile, $name)) {
                $success = true;
                return Storage::disk('FclRequest')->download($Ncontract->namefile, $name);
            } elseif (Storage::disk('UpLoadFile')->exists($Ncontract->namefile, $name)) {
                $success = true;
                return Storage::disk('UpLoadFile')->download($Ncontract->namefile, $name);
            } else {
                $request->session()->flash('message.nivel', 'danger');
                $request->session()->flash('message.content', 'Error. File not found');
                return back();
            }
        }
    }

    public function edit($id)
    {
        $Ncontract = NewContractRequest::find($id);
        $Ncontract->load('direction', 'Requestcarriers', 'companyuser');
        $data = json_decode($Ncontract->data);
        $name = $Ncontract->id . '_' . $Ncontract->companyuser->name . '_' . $data->group_containers->name . '_IMP_FCL';

        $columns = ['ORIGIN', 'DESTINY', 'CHARGE', 'CALCULATION TYPE'];
        $cellDir = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'Ñ', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'Y', 'Z'];
        $countCell = 0;
        foreach ($data->containers as $container) {
            array_push($columns, $container->code);
            $countCell++;
        }
        array_push($columns, 'TYPE DESTINY');
        array_push($columns, 'CURRENCY');
        array_push($columns, 'CARRIER');
        array_push($columns, 'DIFFERENTIATOR');
        //dd($columns);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()
            ->fromArray(
                $columns, // The data to set
                null, // Array values with this value will not be set
                'A1' // Top left coordinate of the worksheet range where
            );
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension($cellDir[$countCell + 4])->setWidth(15);
        $sheet->getColumnDimension($cellDir[$countCell + 5])->setWidth(10);
        $sheet->getColumnDimension($cellDir[$countCell + 6])->setWidth(10);
        $sheet->getColumnDimension($cellDir[$countCell + 7])->setWidth(15);

        $writer = new Writer\Xlsx($spreadsheet);

        $response = new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $name . '.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');
        return $response;
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        try {
            $Ncontract = NewContractRequest::find($id);
            $status_erased = 1;
            if ($Ncontract->erased_contract == 1) {
                $Ncontract->status_erased = $status_erased;
                $Ncontract->update();
                return 1;
            } else {
                if (!empty($Ncontract->namefile)) {
                    try {
                        Storage::disk('FclRequest')->delete($Ncontract->namefile);
                    } catch (\Exception $e) {
                    }
                } else {
                    try {
                        $mediaItem = $Ncontract->getFirstMedia('document');
                        $mediaItems->delete();
                    } catch (\Exception $e) {
                    }
                }
                $Ncontract->delete();
                return 1;
            }
        } catch (\Exception $e) {
            Log::error($e);
            return 2;
        }
    }

    public function getContainers(Request $request)
    {
        $groupContainers = $request->groupContainers;
        $containers = Container::where('gp_container_id', $groupContainers)->where('name', '!=', '45 HC')->where('name', '!=', '40 NOR')->pluck('id');
        return response()->json(['success' => true, 'data' => ['values' => $containers->all()]]);
        //return view('RequestV2.Fcl.select',compact('containers'));
    }

    public function storeMedia(Request $request)
    {
        $path = storage_path('tmp/request');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        $name = uniqid() . '_' . trim($file->getClientOriginalName());
        $fileName = HelperAll::removeAccent($name);

        try {

            $file->move($path, $fileName);
        } catch (\Exception $e) {
            return response()->json([
                'name' => $fileName,
                'original_name' => $file->getClientOriginalName(),
                'error' => true,
            ]);
        }
        return response()->json([
            'name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'error' => false,
        ]);
    }

    public function export(Request $request)
    {
        $dates = explode('/', $request->between);
        $dateStart = trim($dates[0]);
        $dateEnd = trim($dates[1]);
        $now = new \DateTime();
        $now = $now->format('dmY_His');
        $dateEnd = \Carbon\Carbon::parse($dateEnd);
        $dateEnd = $dateEnd->addDay()->format('Y-m-d');

        $countNRq = NewContractRequest::whereBetween('created', [$dateStart . ' 00:00:00', $dateEnd . ' 23:59:59'])->count();

        if ($countNRq <= 100) {
            $nameFile = 'Request_Fcl_' . $now;
            $data = PrvRequest::RequestFclBetween($dateStart, $dateEnd);

            //dd($data->chunk(2));

            $myFile = Excel::create($nameFile, function ($excel) use ($data) {

                $excel->sheet('REQUEST_FCL', function ($sheet) use ($data) {
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
                        'G' => 15,
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
            ExportRequestsJob::dispatch($dateStart, $dateEnd, $auth, 'fcl')->onQueue('operaciones');
            $response = array(
                'actt' => 2,
            );
        }
        return response()->json($response);
    }
}
