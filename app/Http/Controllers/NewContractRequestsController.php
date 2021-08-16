<?php

namespace App\Http\Controllers;

use App\Carrier;
use App\CompanyUser;
use App\Contract;
use App\ContractCarrier;
use App\Direction;
use App\Harbor;
use App\Http\Requests\StoreNewRequestFcl;
use App\Jobs\ExportRequestsJob;
use App\Jobs\NotificationsJob;
use App\Jobs\ProcessContractFile;
use App\Jobs\SendEmailRequestFclJob;
use App\NewContractRequest;
use App\Notifications\N_general;
use App\Notifications\SlackNotification;
use App\RequetsCarrierFcl;
use App\User;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PrvRequest;
use Yajra\Datatables\Datatables;
use \Carbon\Carbon;

class NewContractRequestsController extends Controller
{

    public function index(Request $request)
    {
        return view('Requests.index');
    }

    public function create()
    {
        /*$Ncontracts = NewContractRequest::with('user','companyuser','Requestcarriers.carrier','direction')->orderBy('id', 'desc')->get();*/

        $Ncontracts = DB::select('call  select_request_fcl()');
        //        dd($Ncontracts);
        //dd($Ncontracts[0]['Requestcarriers']->pluck('carrier')->pluck('name'));
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
                if (count($Ncontracts->carriers) >= 1) {
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
                $color = '';
                if (strnatcasecmp($Ncontracts->status, 'Pending') == 0) {
                    //$color = 'color:#031B4E';
                    $color = 'color:#f81538';
                } else if (strnatcasecmp($Ncontracts->status, 'Processing') == 0) {
                    $color = 'color:#5527f0';
                } else if (strnatcasecmp($Ncontracts->status, 'Review') == 0) {
                    $color = 'color:#e07000';
                } else {
                    $color = 'color:#04950f';
                }

                return '<a href="#" onclick="showModal(' . $Ncontracts->id . ')"style="' . $color . '" id="statusHrf' . $Ncontracts->id . '" class="statusHrf' . $Ncontracts->id . '">' . $Ncontracts->status . '</a>
                &nbsp;
                <samp class="la la-pencil-square-o" id="statusSamp' . $Ncontracts->id . '" class="statusHrf' . $Ncontracts->id . '" for="" style="' . $color . '"></samp>';
            })
            ->addColumn('action', function ($Ncontracts) use ($permiso_eliminar) {

                $buttons = '
                &nbsp;&nbsp;
				<a href="' . route("RequestImportation.show", $Ncontracts->id) . '" title="Download File">
                    <samp class="la la-cloud-download" style="font-size:20px; color:#031B4E"></samp>
                </a>
                &nbsp;&nbsp;';
                $eliminiar_buton = '
                <a href="#" class="eliminarrequest" data-id-request="' . $Ncontracts->id . '" data-info="id:' . $Ncontracts->id . ' Number Contract: ' . $Ncontracts->numbercontract . '"  title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>';

                if ($permiso_eliminar) {
                    $buttons = $buttons . $eliminiar_buton;
                }

                if (empty($Ncontracts->contract) != true) {
                    $buttonDp = "<a href='#' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' onclick='AbrirModal(\"DuplicatedContractOtherCompany\"," . $Ncontracts->contract . "," . $Ncontracts->id . ")'  title='Duplicate to another company'>
                      <i style='color:#b90000' class='la la-copy'></i>
                    </a>";
                    $butPrCt = '<a href="/Importation/RequestProccessFCL/' . $Ncontracts->contract . '/2/' . $Ncontracts->id . '" title="Proccess FCL Contract">
                    <samp class="la la-cogs" style="font-size:20px; color:#04950f"></samp>
                    </a>

                    ' . $buttonDp . '
                    &nbsp;&nbsp;
                    <a href="#" title="Edit FCL Contract">
                    <samp class="la la-edit" onclick="editcontract(' . $Ncontracts->contract . ')" style="font-size:20px; color:#04950f"></samp>
                    </a>
                    ';
                    $buttons = $butPrCt . $buttons;
                } else {
                    $butPrRq = '<a href="/Importation/RequestProccessFCL/' . $Ncontracts->id . '/1/0" title="Proccess FCL Request">
                    <samp class="la la-cogs" style="font-size:20px; color:#D85F00"></samp>
                    </a>';
                    $buttons = $butPrRq . $buttons;
                }
                return $buttons;
            })

            ->make();
    }

    public function store(Request $request)
    {
        //dd($request->all());
    }

    public function store2(StoreNewRequestFcl $request)
    {
        //dd($request->all());
        //return response()->json($request);
        $fileBoll = false;
        $time = new \DateTime();
        $now = $time->format('dmY_His');
        $now2 = $time->format('Y-m-d H:i:s');

        $file = $request->file('file');

        $ext = strtolower($file->getClientOriginalExtension());
        /*$validator = \Validator::make(
        array('ext' => $ext),
        array('ext' => 'in:xls,xlsx,csv,pdf')
        );
        if ($validator->fails()) {
        $request->session()->flash('message.nivel', 'danger');
        $request->session()->flash('message.content', 'just archive with extension xlsx, xls, csv and pdf.');
        return redirect()->route('Requestimporfcl');
        }*/
        //obtenemos el nombre del archivo
        $nombre = $file->getClientOriginalName();
        $nombre = quitar_caracteres($nombre);
      
        $nombre = $now . '_' . $nombre;

        $fileBoll = \Storage::disk('FclRequest')->put($nombre, \File::get($file));
        $typeVal = 1;
        $arreglotype = '';
        $type = '';
        $data = '';
        $type = json_encode($type);
        $data = json_encode($data);
        if ($fileBoll) {

            $CompanyUserId = $request->CompanyUserId;
            $direction_id = $request->direction;

            $contract = new Contract();
            $contract->name = $request->name;
            $validity = explode('/', $request->validation_expire);
            $contract->validity = $validity[0];
            $contract->expire = $validity[1];
            $contract->direction_id = $direction_id;
            $contract->status = 'incomplete';
            $contract->company_user_id = $CompanyUserId;
            $contract->save();

            foreach ($request->carrierM as $carrierVal) {
                ContractCarrier::create([
                    'carrier_id' => $carrierVal,
                    'contract_id' => $contract->id,
                ]);
            }

            $Ncontract = new NewContractRequest();
            $Ncontract->namecontract = $request->name;
            $Ncontract->validation = $request->validation_expire;
            $Ncontract->direction_id = $direction_id;
            $Ncontract->company_user_id = $CompanyUserId;
            $Ncontract->namefile = $nombre;
            $Ncontract->user_id = $request->user;
            $Ncontract->created = $now2;
            $Ncontract->username_load = 'Not assigned';
            $Ncontract->type = $type;
            $Ncontract->data = $data;
            $Ncontract->contract_id = $contract->id;
            //$Ncontract->contract_id     = 100;
            $Ncontract->save();
            $carrier_arr = $request->carrierM;
            foreach ($carrier_arr as $carrierVal) {
                RequetsCarrierFcl::create([
                    'carrier_id' => $carrierVal,
                    'request_id' => $Ncontract->id,
                ]);
            }

            if (env('APP_VIEW') == 'operaciones') {
                ProcessContractFile::dispatch($Ncontract->id, $Ncontract->namefile, 'fcl', 'request')->onQueue('operaciones');
            } else {
                ProcessContractFile::dispatch($Ncontract->id, $Ncontract->namefile, 'fcl', 'request');
            }
            $user = User::find($request->user);
            $message = "There is a new request from " . $user->name . " - " . $user->companyUser->name;
            $user->notify(new SlackNotification($message));
            $admins = User::where('type', 'admin')->get();
            $message = 'A new request has been created - ' . $Ncontract->id;
            NotificationsJob::dispatch('Request-Fcl', [
                'user' => $request->user,
                'ncontract' => $Ncontract->toArray(),
            ]);
            foreach ($admins as $userNotifique) {
                /*\Mail::to($userNotifique->email)->send(new NewRequestToAdminMail(
                $userNotifique->toArray(),
                $user->toArray(),
                $Ncontract->toArray()));*/
                $userNotifique->notify(new N_general($user, $message));
            }

            return response()->json(['data' => 'ok']);
        } else {
            return response()->json(['data' => 'err']);
        }
    }

    //Para descargar el archivo
    public function show(Request $request, $id)
    {
        $Ncontract = NewContractRequest::find($id);
        $time = new \DateTime();
        $now = $time->format('d-m-y');
        $company = CompanyUser::find($Ncontract->company_user_id);
        $extObj = new \SplFileInfo($Ncontract->namefile);
        $ext = $extObj->getExtension();
        $name = $Ncontract->id . '-' . $company->name . '_' . $now . '-FLC.' . $ext;
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

    public function showStatus($id)
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
        } elseif ($status == 'Review' || $status == 'Done') {
            $status_arr['Processing'] = 'Processing';
            $status_arr['Review'] = 'Review';
            $status_arr['Done'] = 'Done';
        }

        return view('Requests.Body-Modals.edit', compact('requests', 'status_arr'));
    }
    public function edit($id)
    {
        $Ncontracts = NewContractRequest::with('companyuser', 'user')->find($id);
        //dd($Ncontracts);
        $type = json_decode($Ncontracts->type);
        $data = json_decode($Ncontracts->data);

        //dd($data);

        $surchargeBol = false;
        $rateBol = false;
        $ValuesSomeBol = false;
        $ValuesWithCurreBol = false;
        $ValCarrierBol = false;
        $ValuesDestinyBol = false;
        $ValuesOriginBol = false;
        $tarjetBol = false;

        $contenSurchar = '';
        $contenRate = '';
        $contenValuesSome = '';
        $contenValuesWithCurre = '';
        $contenValuesCarrier = '';
        $contenValuesDestiny = '';
        $contenValuesOrigin = '';

        //dd($type);
        if ($type->type == 2) {
            $surchargeBol = true;
            $contenSurchar = 'El archivo contiene Rates + Surchargers';

            if ($type->values == 1) {
                $contenValuesSome = 'Las columnas valores solo contiene los valores';
                $ValuesSomeBol = true;

            } else if ($type->values == 2) {
                $contenValuesWithCurre = 'Las columnas de los valores, contienen los currency';
                $ValuesWithCurreBol = true;
            }
        } else if ($type->type == 1) {
            $rateBol = true;
            $contenRate = 'El archivo contiene solo Rates';
        }

        if ($data->DatCar) {
            $ValCarrierBol = true;
            $carrierObj = Carrier::find($data->carrier);
            $contenValuesCarrier = 'El archivo no contiene la columna Carrier. Carrier: ' . $carrierObj->name;
        }

        if ($data->DatDes) {
            $ValuesDestinyBol = true;
            $destinos = '';
            foreach ($data->destiny as $destiny) {
                $destinosObj = Harbor::find($destiny);
                $destinos = $destinos . $destinosObj->display_name . '.. ';
            }
            $contenValuesDestiny = 'El archivo no contiene la columna Destino. Destino: ' . $destinos;
        }

        if ($data->DatOri) {
            $ValuesOriginBol = true;
            $origenes = '';
            foreach ($data->origin as $origen) {
                $origenObj = Harbor::find($origen);
                $origenes = $origenes . '' . $origenObj->display_name . '...  ';
            }
            $contenValuesOrigin = 'El archivo no contiene la columna Origen. Origen: ' . $origenes;
        }

        if ($ValuesOriginBol == true || $ValuesDestinyBol == true || $ValCarrierBol == true) {
            $tarjetBol = true;
        }

        $colectionFinal = collect([]);

        $Contenido = [
            'namecontract' => $Ncontracts->namecontract,
            'numbercontract' => $Ncontracts->numbercontract,
            'validation' => $Ncontracts->validation,
            'company' => $Ncontracts->companyuser->name,
            'status' => $Ncontracts->status,
            'User' => $Ncontracts->user->name . ' ' . $Ncontracts->user->lastname,
            'created' => $Ncontracts->created,

            'surchargeBol' => $surchargeBol,
            'contenSurchar' => $contenSurchar,
            'rateBol' => $rateBol,
            'contenRate' => $contenRate,

            'ValuesSomeBol' => $ValuesSomeBol,
            'contenValuesSome' => $contenValuesSome,
            'ValuesWithCurreBol' => $ValuesWithCurreBol,
            'contenValuesWithCurre' => $contenValuesWithCurre,

            'ValCarrierBol' => $ValCarrierBol,
            'contenValuesCarrier' => $contenValuesCarrier,
            'ValuesDestinyBol' => $ValuesDestinyBol,
            'contenValuesDestiny' => $contenValuesDestiny,

            'ValuesOriginBol' => $ValuesOriginBol,
            'contenValuesOrigin' => $contenValuesOrigin,
            'tarjetBol' => $tarjetBol,

        ];

        $colectionFinal->push($Contenido);
        //dd($ColectionFinal);

        return view('Requests.DetailNewRequest', compact('colectionFinal'));
    }

    public function update(Request $request, $id)
    {
        //
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
            if ($Ncontract->username_load == 'Not assigned') {
                $Ncontract->username_load = \Auth::user()->name . ' ' . \Auth::user()->lastname;
            }

            if ($Ncontract->status == 'Processing') {
                if ($Ncontract->time_star_one == false) {
                    $Ncontract->time_star = $now2;
                    $Ncontract->time_star_one = true;
                }
            } elseif ($Ncontract->status == 'Review') {
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
                    $message = 'Your request N° ' . $Ncontract->id . ' was processed';
                    foreach ($users as $user) {
                        $user->notify(new N_general(\Auth::user(), $message));
                    }

                    $usercreador = User::find($Ncontract->user_id);
                    $message = "The importation " . $Ncontract->id . " was completed";
                    $usercreador->notify(new SlackNotification($message));
                    if (env('APP_VIEW') == 'operaciones') {
                        SendEmailRequestFclJob::dispatch($usercreador->toArray(), $id)->onQueue('operaciones');
                    } else {
                        SendEmailRequestFclJob::dispatch($usercreador->toArray(), $id);
                    }
                }
            }
            $Ncontract->save();

            if (strnatcasecmp($Ncontract->status, 'Pending') == 0) {
                $color = '#f81538';
            } else if (strnatcasecmp($Ncontract->status, 'Processing') == 0) {
                $color = '#5527f0';
            } else if (strnatcasecmp($Ncontract->status, 'Review') == 0) {
                $color = '#e07000';
            } else if (strnatcasecmp($Ncontract->status, 'Done') == 0) {
                $color = '#04950f';
            }
            return response()->json($data = ['data' => 1, 'status' => $Ncontract->status, 'color' => $color, 'request' => $Ncontract]);
        } catch (\Exception $e) {
            return response()->json($data = ['data' => 2]);
        }

    }

    public function destroy($id)
    {
        return 1;
    }

    public function destroyRequest($id)
    {
        try {
            $Ncontract = NewContractRequest::find($id);
            Storage::disk('FclRequest')->delete($Ncontract->namefile);
            $Ncontract->delete();
            return 1;
        } catch (\Exception $e) {
            return 2;
        }
    }

    // New Request Importation ----------------------------------------------------------
    public function LoadViewRequestImporContractFcl()
    {
        $harbor = harbor::all()->pluck('display_name', 'id');
        $carrier = carrier::all()->pluck('name', 'id');
        $direction = [null => 'Please Select'];
        $direction2 = Direction::all();
        $user = \Auth::user();
        foreach ($direction2 as $d) {
            //dd($direction2);
            $direction[$d['id']] = $d->name;
        }
        //dd($direction);
        return view('Requests.NewRequest', compact('harbor', 'carrier', 'user', 'direction'));
    }

    // Similar Contracts ----------------------------------------------------------------

    public function similarcontracts(Request $request, $id)
    {
        $contracts = Contract::select(['id',
            'name',
            'number',
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
                if (count($dds->direction) != 0) {
                    return $dds->direction->name;
                } else {
                    return '-------';
                }
            })
            ->make(true);
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

    public function getdataRequest($id)
    {
        $requestFc = NewContractRequest::find($id);
        $requestFc->load('Requestcarriers');
        $carriers = [];
        foreach ($requestFc->requestcarriers as $carrierF) {
            array_push($carriers, $carrierF->carrier_id);
        }
        return response()->json(['success' => true, 'data' => $requestFc->toArray(), 'carriers' => $carriers]);
    }

    // TEST Request Importation ----------------------------------------------------------
    public function test(Request $request)
    {
        $carrier = carrier::all()->pluck('name', 'id');
        $direction = [null => 'Please Select'];
        $direction2 = Direction::all();
        $user = \Auth::user();
        foreach ($direction2 as $d) {
            //dd($direction2);
            $direction[$d['id']] = $d->name;
        }
        //dd($direction);
        return view('RequestV2.Fcl.index', compact('carrier', 'user', 'direction'));
    }
}
