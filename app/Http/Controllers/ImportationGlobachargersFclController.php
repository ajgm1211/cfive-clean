<?php

namespace App\Http\Controllers;

use Excel;
use PrvHarbor;
use App\Harbor;
use App\Carrier;
use App\Currency;
use Carbon\Carbon;
use App\Surcharge;
use App\CompanyUser;
use App\TypeDestiny;
use App\GlobalCharge;
use App\GlobalCharPort;
use App\CalculationType;
use App\GlobalCharCarrier;
use App\FailedGlobalcharge;
use App\FileTmpGlobalcharge;
use Illuminate\Http\Request;
use App\AccountImportationGlobalcharge;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ImportationGlobalchargeJob;

class ImportationGlobachargersFclController extends Controller
{


    // precarga la vista para importar rates o rates mas surchargers
    public function index()
    {
        $harbor         = Harbor::all()->pluck('display_name','id');
        $carrier        = Carrier::all()->pluck('name','id');
        $companysUser   = CompanyUser::all()->pluck('name','id');
        $typedestiny    = TypeDestiny::all()->pluck('description','id');
        return view('ImportationGlobalchargersFcl.index',compact('harbor','carrier','companysUser','typedestiny'));
    }

    // carga el archivo excel y verifica la cabecera para mostrar la vista con las columnas:
    public function UploadFileNewContract(Request $request){
        //dd($request->all());
        $now = new \DateTime();
        $now = $now->format('dmY_His');
        $carrierVal         = $request->carrier;
        $typedestinyVal     = $request->typedestiny;
        $validitydateVal    = $request->validitydate;
        $destinyArr         = $request->destiny;
        $originArr          = $request->origin;
        $CompanyUserId      = $request->CompanyUserId;
        $carrierBol         = false;
        $destinyBol         = false;
        $originBol          = false;
        $typedestinyBol     = false;
        $datevalidityBol    = false;
        $fortynorBol        = false;
        $fortyfiveBol       = false;
        $filebool           = false;

        $data           = collect([]);
        $typedestiny    = TypeDestiny::all()->pluck('description','id');
        $harbor         = harbor::all()->pluck('display_name','id');
        $carrier        = carrier::all()->pluck('name','id');

        $file           = $request->file('file');
        $ext            = strtolower($file->getClientOriginalExtension());
        $validator      = \Validator::make(
            array('ext' => $ext),
            array('ext' => 'in:xls,xlsx,csv')
        );
        $Contract_id;
        if ($validator->fails()) {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
            return redirect()->route('ImportationGlobalchargeFcl.index');
        }
        //obtenemos el nombre del archivo
        $nombre     = $file->getClientOriginalName();
        $nombre     = $now.'_'.$nombre;
        $filebool   = \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));

        if($filebool){
            $account   = new AccountImportationGlobalcharge();
            $account->name             = $request->name;
            $account->date             = $request->date;
            $account->status           = 'incomplete';
            $account->company_user_id  = $CompanyUserId;
            $account->save(); 

            $account_id = $account->id;
            $fileTmp    = new FileTmpGlobalcharge();
            $fileTmp->account_id = $account_id;
            $fileTmp->name_file   = $nombre;
            $fileTmp->save(); //*/
        } else {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'Error storage:link!!');
            return redirect()->route('ImportationGlobalchargeFcl.index');
        }


        $targetsArr =[ 
            0 => "Calculation Type",
            1 => "Charge",
            2 => "20'",
            3 => "40'",
            4 => "40'HC"
        ];

        // Datftynor Datftyfive - DatOri - DatDes - DatCar, hacen referencia a si fue marcado el checkbox

        if($request->Datftynor == true){
            array_push($targetsArr,"40'NOR");
        } else{
            $fortynorBol = true;
        }

        if($request->Datftyfive == true){
            array_push($targetsArr,"45'");
        } else {
            $fortyfiveBol = true;
        }

        /* si $statustypecurren es igual a 2, los currencys estan contenidos en la misma columna 
        con los valores, si es uno el currency viene en una colmna aparte        
        */

        $statustypecurren = $request->valuesCurrency;

        if($statustypecurren == 1){
            array_push($targetsArr,"Currency");
        }

        // ------- ORIGIN -------------------------
        if($request->DatOri == false){
            array_push($targetsArr,'Origin');
        }
        else{
            $originBol = true;
            $originArr;
        }

        // ------- DESTINY ------------------------
        if($request->DatDes == false){
            array_push($targetsArr,'Destiny');
        } else {
            $destinyArr;
            $destinyBol = true;
        }

        // ------- CARRIER ------------------------
        if($request->DatCar == false){
            array_push($targetsArr,'Carrier');
        } else {
            $carrierVal;
            $carrierBol = true;
        }

        // ------- TYPE DESTINY -------------------
        if($request->DatTypeDes == false){
            array_push($targetsArr,'Type Destiny');
        } else {
            $typedestinyVal;
            $typedestinyBol = true;
        }

        // ------- DATE VAIDITY -------------------
        if($request->DatDtValid == false){
            array_push($targetsArr,'Validity From');
            array_push($targetsArr,'Validity To');
        } else {
            $validitydateVal;
            $datevalidityBol = true;
        }

        $coordenates = collect([]);

        ini_set('memory_limit', '1024M');

        Excel::selectSheetsByIndex(0)
            ->Load(\Storage::disk('UpLoadFile')
                   ->url($nombre),function($reader) use($request,$coordenates) {
                       $reader->takeRows(2);
                       $reader->noHeading = true;
                       $reader->ignoreEmpty();

                       $read = $reader->first();
                       $columna= array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','Ñ','O','P','Q','R','S','T','U','V');
                       for($i=0;$i<count($reader->first());$i++){
                           $coordenates->push($columna[$i].' '.$read[$i]);
                       }
                   });

        $boxdinamy = [
            'existorigin'       => $originBol,
            'origin'            => $originArr,
            'existdestiny'      => $destinyBol,
            'destiny'           => $destinyArr,
            'existcarrier'      => $carrierBol,
            'carrier'           => $carrierVal,            
            'existtypedestiny'  => $typedestinyBol,
            'typedestiny'       => $typedestinyVal,

            'existdatevalidity' => $datevalidityBol,
            'validitydate'      => $validitydateVal,

            'account_id'        => $account_id,
            'date'              => $request->date,
            'name'              => $request->name,
            'existfortynor'     => $fortynorBol,
            'fortynor'          => 0,
            'existfortyfive'    => $fortyfiveBol,
            'fortyfive'         => 0,
            'fileName'          => $nombre,

        ];
        $data->push($boxdinamy);
        $countTarges = count($targetsArr);
        //dd($data);

        return view('ImportationGlobalchargersFcl.show',compact('harbor',
                                                                'data',
                                                                'carrier',
                                                                'targetsArr',
                                                                'account_id',
                                                                'coordenates',
                                                                'countTarges',
                                                                'CompanyUserId',
                                                                'statustypecurren',
                                                                'typedestiny'));
    }

    // Despachador a job de importacion
    public function create(Request $request)
    {
        $companyUserId = $request->CompanyUserId;
        $UserId =\Auth::user()->id;
        //dd($request->all());
        ImportationGlobalchargeJob::dispatch($request->all(),$companyUserId,$UserId); //NO BORRAR!!
        $id = $request['account_id'];
        return redirect()->route('ImportationGlobalchargeFcl.show',$id);
    }

    // Lista de cuentas importadas
    public function indexTwo(){
        $accounts = AccountImportationGlobalcharge::with('companyuser')->orderBy('id','desc')->get();
        //dd($accounts);
        return view('ImportationGlobalchargersFcl.indexAccount',compact('accounts'));
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        return view('ImportationGlobalchargersFcl.ProcessedInformation',compact('id'));
    }


    public function edit($id){
        
    }
    
    public function showviewfailedandgood($id,$tab)
    {

        $countfailglobal = FailedGlobalcharge::where('account_id','=',$id)->count();
        $countgoodglobal = GlobalCharge::where('account_importation_globalcharge_id','=',$id)->count();
        //dd('fallidos'.$countfailglobal);
        return view('ImportationGlobalchargersFcl.showview',compact('id','tab','countfailglobal','countgoodglobal'));
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function deleteAccounts($id,$select){
        $account = AccountImportationGlobalcharge::with('FileTmp')->find($id);
        if(count($account)>0){
            if(count($account->FileTmp)>0){
                Storage::disk('UpLoadFile')->delete($account->FileTmp->name_file);
            }
            $account->delete();
        }

        if($select == 1){
            return redirect()->route('ImportationGlobalchargeFcl.index');
        } elseif($select == 2){
            return redirect()->route('indextwo.globalcharge.fcl');			
        }

    }
}
