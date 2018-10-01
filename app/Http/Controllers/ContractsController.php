<?php

namespace App\Http\Controllers;

use App\Company;
use App\ContractUserRestriction;
use App\ContractCompanyRestriction;
use Illuminate\Http\Request;
use App\Contract;
use App\Contact;
use App\Country;
use App\Carrier;
use App\Harbor;
use App\Rate;
use App\FailRate;
use App\Currency;
use App\CalculationType;
use App\LocalCharge;
use App\Surcharge;
use App\LocalCharCarrier;
use App\LocalCharPort;
use App\User;
use App\TypeDestiny;
use App\FailSurCharge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Excel;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UploadFileRateRequest;
use App\FileTmp;
use App\Jobs\ImportationRatesSurchargerJob;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;
use App\CompanyUser;
use App\ViewLocalCharges;
use App\ViewRates;
use App\ViewContractRates;

class ContractsController extends Controller
{

    public function index()
    {
        $arreglo = Contract::where('company_user_id','=',Auth::user()->company_user_id)->with('rates')->get();
        $contractG = Contract::where('company_user_id','=',Auth::user()->company_user_id)->get();


        return view('contracts/index', compact('arreglo','contractG'));
    }

    public function add()
    {
        $objcountry = new Country();
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objcalculation = new CalculationType();
        $objsurcharge = new Surcharge();
        $objtypedestiny = new TypeDestiny();

        $harbor = $objharbor->all()->pluck('display_name','id');
        $country = $objcountry->all()->pluck('name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $calculationT = $objcalculation->all()->pluck('name','id');
        $typedestiny = $objtypedestiny->all()->pluck('description','id');
        $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        $companies = Company::where('company_user_id', '=', \Auth::user()->company_user_id)->pluck('business_name','id');
        $contacts = Contact::whereHas('company', function ($query) {
            $query->where('company_user_id', '=', \Auth::user()->company_user_id);
        })->pluck('first_name','id');
        if(Auth::user()->type == 'company' ){
            $users =  User::whereHas('companyUser', function($q)
                                     {
                                         $q->where('company_user_id', '=', Auth::user()->company_user_id);
                                     })->pluck('Name','id');
        }
        if(Auth::user()->type == 'admin' || Auth::user()->type == 'subuser' ){
            $users =  User::whereHas('companyUser', function($q)
                                     {
                                         $q->where('company_user_id', '=', Auth::user()->company_user_id);
                                     })->pluck('Name','id');
        }

        return view('contracts.addT',compact('country','carrier','harbor','currency','calculationT','surcharge','typedestiny','companies','contacts','users'));

    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

        $contract = new Contract($request->all());
        $contract->company_user_id =Auth::user()->company_user_id;
        $validation = explode('/',$request->validation_expire);
        $contract->validity = $validation[0];
        $contract->expire = $validation[1];
        $contract->save();

        $details = $request->input('origin_id');
        $detailscharges = $request->input('localcurrency_id');
        $companies = $request->input('companies');
        $users = $request->input('users');
        // For Each de los rates
        $contador = 1;
        foreach($details as $key => $value)
        {


            $rates = new Rate();
            $rates->origin_port = $request->input('origin_id.'.$key);
            $rates->destiny_port = $request->input('destiny_id.'.$key);
            $rates->carrier_id = $request->input('carrier_id.'.$key);
            $rates->twuenty = $request->input('twuenty.'.$key);
            $rates->forty = $request->input('forty.'.$key);
            $rates->fortyhc = $request->input('fortyhc.'.$key);
            $rates->currency_id = $request->input('currency_id.'.$key);
            $rates->contract()->associate($contract);

            $rates->save();

        }


        // For Each de los localcharge

        foreach($detailscharges as $key2 => $value)
        {
            if(!empty($request->input('ammount.'.$key2))) {
                $localcharge = new LocalCharge();
                $localcharge->surcharge_id = $request->input('type.'.$key2);
                $localcharge->typedestiny_id = $request->input('changetype.'.$key2);
                $localcharge->calculationtype_id = $request->input('calculationtype.'.$key2);
                $localcharge->ammount = $request->input('ammount.'.$key2);
                $localcharge->currency_id = $request->input('localcurrency_id.'.$key2);
                $localcharge->contract()->associate($contract);
                $localcharge->save();

                $detailportOrig = $request->input('port_origlocal'.$contador);
                $detailportDest = $request->input('port_destlocal'.$contador);

                $detailcarrier = $request->input('localcarrier_id'.$contador);
                foreach($detailcarrier as $c => $value)
                {
                    $detailcarrier = new LocalCharCarrier();
                    $detailcarrier->carrier_id =$request->input('localcarrier_id'.$contador.'.'.$c);
                    $detailcarrier->localcharge()->associate($localcharge);
                    $detailcarrier->save();
                }
                foreach($detailportOrig as $orig => $value)
                {
                    foreach($detailportDest as $dest => $value)
                    {
                        $detailport = new LocalCharPort();
                        $detailport->port_orig = $request->input('port_origlocal'.$contador.'.'.$orig);
                        $detailport->port_dest = $request->input('port_destlocal'.$contador.'.'.$dest);
                        $detailport->localcharge()->associate($localcharge);
                        $detailport->save();
                    }

                }
                $contador++;
            }
        }

        if(!empty($companies)){
            foreach($companies as $key3 => $value)
            {
                $contract_company_restriction = new ContractCompanyRestriction();
                $contract_company_restriction->company_id=$value;
                $contract_company_restriction->contract_id=$contract->id;
                $contract_company_restriction->save();
            }
        }

        if(!empty($users)){
            foreach($users as $key4 => $value)
            {
                $contract_client_restriction = new ContractUserRestriction();
                $contract_client_restriction->user_id=$value;
                $contract_client_restriction->contract_id=$contract->id;
                $contract_client_restriction->save();
            }
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully add this contract.');

        return redirect()->action('ContractsController@index');

    }


    public function show($id)
    {
        //
    }



    // FUNCIONES PARA EL DATATABLE
    public function data($id){

        $localchar = new  ViewLocalCharges();
        $data = $localchar->select('id','surcharge','port_orig','port_dest','changetype','carrier','calculation_type','ammount','currency')->where('contract_id',$id);

        return \DataTables::of($data)
            ->addColumn('options', function ($data) {
                return " <a   class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test'  title='Edit '  onclick='AbrirModal(\"editLocalCharge\",$data[id])'>
          <i class='la la-edit'></i>
          </a>
            <a  data-local-id='$data[id]'    class='m_sweetalert_demo_8  m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='delete' >
          <i id='rm_l' class='la la-times-circle'></i>
        ";
            }) ->setRowId('id')->rawColumns(['options'])->make(true);
    }// local charges en edit

    public function dataRates($id){


        $rate = new  ViewRates();
        $data = $rate->select('id','port_orig','port_dest','carrier','twuenty','forty','fortyhc','currency')->where('contract_id',$id);

        return \DataTables::of($data)
            ->addColumn('options', function ($data) {
                return " <a   class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test' title='Edit'  onclick='AbrirModal(\"editRate\",$data[id])'>
          <i class='la la-edit'></i>
          </a>
             <a id='delete-rate' data-rate-id='$data[id]' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' title='Delete' >
                    <i  class='la la-times-circle'></i>
                    </a>

        ";
            }) ->setRowId('id')->rawColumns(['options'])->make(true);

    }

    public function contractRates(){
        $contractRate = new  ViewContractRates();
        $data = $contractRate->select('id','contract_id','name','number','validy','expire','status','port_orig','port_dest','carrier','twuenty','forty','fortyhc','currency')->where('company_user_id', Auth::user()->company_user_id);


        return \DataTables::of($data)

            ->addColumn('validity', function ($data) {
                return $data['validy'] ." / ".$data['expire'];
            })
            ->addColumn('options', function ($data) {
                return "<a href='contracts/".$data['contract_id']."/edit' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='Edit '>
                      <i class='la la-edit'></i>
                    </a>

                    <a href='#' id='delete-rate' data-rate-id='$data[id]' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill' title='Delete' >
                    <i  class='la la-times-circle'></i>
                    </a>

        ";
            }) ->setRowId('id')->rawColumns(['options'])->make(true);

    }

    public function contractTable(){

        $contractG = Contract::where('company_user_id','=',Auth::user()->company_user_id)->get();



        return \DataTables::collection($contractG)

            ->addColumn('options', function (Contract $contractG) {
                return "      <a href='contracts/".$contractG->id."/edit' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='Edit '>
                      <i class='la la-edit'></i>
                    </a>
                    <a  id='delete-contract' data-contract-id='$contractG->id' class='m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill'  title='Delete'>
                      <i class='la la-eraser'></i>
                    </a>

        ";
            }) ->setRowId('id')->rawColumns(['options'])->make(true);

    }



    public function edit(Request $request,$id)
    {
        $contracts = Contract::where('id',$id)->first();

        $objtypedestiny = new TypeDestiny();
        $objcountry = new Country();
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objcalculation = new CalculationType();
        $objsurcharge = new Surcharge();

        $harbor = $objharbor->all()->pluck('display_name','id');
        $country = $objcountry->all()->pluck('name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $calculationT = $objcalculation->all()->pluck('name','id');
        $typedestiny = $objtypedestiny->all()->pluck('description','id');
        $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        $company_restriction = ContractCompanyRestriction::where('contract_id',$contracts->id)->first();
        $user_restriction = ContractUserRestriction::where('contract_id',$contracts->id)->first();
        if(!empty($company_restriction)){
            $company = Company::where('id',$company_restriction->company_id)->select('id')->first();
        }
        if(!empty($user_restriction)){
            $user = User::where('id',$user_restriction->user_id)->select('id')->first();
        }
        $companies = Company::where('company_user_id', '=', \Auth::user()->company_user_id)->pluck('business_name','id');
        if(Auth::user()->type == 'company' ){
            $users =  User::whereHas('companyUser', function($q)
                                     {
                                         $q->where('company_user_id', '=', Auth::user()->company_user_id);
                                     })->pluck('Name','id');
        }
        if(Auth::user()->type == 'admin' || Auth::user()->type == 'subuser' ){
            $users =  User::whereHas('companyUser', function($q)
                                     {
                                         $q->where('company_user_id', '=', Auth::user()->company_user_id);
                                     })->pluck('Name','id');
        }
        //dd($contracts);
        if (!$request->session()->exists('activeS')) {
            $request->session()->flash('activeR', 'active');
        }

        return view('contracts.editT', compact('contracts','harbor','country','carrier','currency','calculationT','surcharge','typedestiny','company','companies','users','user','id'));
    }

    public function update(Request $request, $id)
    {
        $requestForm = $request->all();
        $contract = Contract::find($id);
        $validation = explode('/',$request->validation_expire);
        $contract->validity = $validation[0];
        $contract->expire = $validation[1];
        $contract->update($requestForm);

        $details = $request->input('origin_id');
        $detailscharges =  $request->input('localcurrency_id');//  $request->input('ammount');
        $companies = $request->input('companies');
        $users = $request->input('users');
        $contador = 1;
        // for each rates 
        foreach($details as $key => $value)
        {
            if(is_numeric($request->input('twuenty.'.$key))) {

                $rates = new Rate();
                $rates->origin_port = $request->input('origin_id.'.$key);
                $rates->destiny_port = $request->input('destiny_id.'.$key);
                $rates->carrier_id = $request->input('carrier_id.'.$key);
                $rates->twuenty = $request->input('twuenty.'.$key);
                $rates->forty = $request->input('forty.'.$key);
                $rates->fortyhc = $request->input('fortyhc.'.$key);
                $rates->currency_id = $request->input('currency_id.'.$key);
                $rates->contract()->associate($contract);
                $rates->save();

            }
        }

        // For Each de los localcharge

        foreach($detailscharges as $key2 => $value)
        {
            if(!empty($request->input('ammount.'.$key2))) {
                $localcharge = new LocalCharge();
                $localcharge->surcharge_id = $request->input('type.'.$key2);
                $localcharge->typedestiny_id  = $request->input('changetype.'.$key2);
                $localcharge->calculationtype_id = $request->input('calculationtype.'.$key2);
                $localcharge->ammount = $request->input('ammount.'.$key2);
                $localcharge->currency_id = $request->input('localcurrency_id.'.$key2);
                $localcharge->contract()->associate($contract);
                $localcharge->save();
                $detailportOrig = $request->input('port_origlocal'.$contador);
                $detailportDest = $request->input('port_destlocal'.$contador);
                $detailcarrier = $request->input('localcarrier_id'.$contador);
                $companies = $request->input('companies');
                foreach($detailcarrier as $c => $value)
                {
                    $detailcarrier = new LocalCharCarrier();
                    $detailcarrier->carrier_id =$request->input('localcarrier_id'.$contador.'.'.$c);
                    $detailcarrier->localcharge()->associate($localcharge);
                    $detailcarrier->save();
                }
                foreach($detailportOrig as $orig => $value)
                {
                    foreach($detailportDest as $dest => $value)
                    {


                        $detailport = new LocalCharPort();
                        $detailport->port_orig = $request->input('port_origlocal'.$contador.'.'.$orig);
                        $detailport->port_dest = $request->input('port_destlocal'.$contador.'.'.$dest);
                        $detailport->localcharge()->associate($localcharge);
                        $detailport->save();
                    }

                }
                $contador++;
            }
        }

        if(!empty($companies)){
            ContractCompanyRestriction::where('contract_id',$contract->id)->delete();

            foreach($companies as $key3 => $value)
            {
                $contract_company_restriction = new ContractCompanyRestriction();
                $contract_company_restriction->company_id=$value;
                $contract_company_restriction->contract_id=$contract->id;
                $contract_company_restriction->save();
            }
        }

        if(!empty($users)){
            ContractUserRestriction::where('contract_id',$contract->id)->delete();

            foreach($users as $key4 => $value)
            {
                $contract_client_restriction = new ContractUserRestriction();
                $contract_client_restriction->user_id=$value;
                $contract_client_restriction->contract_id=$contract->id;
                $contract_client_restriction->save();
            }

        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully update this contract.');
        return redirect()->action('ContractsController@index');

    }

    public function addRates($id){
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');

        return view('contracts.addRates', compact('harbor','carrier','currency','id'));
    }
    public function storeRates(Request $request,$id){
        $rates = new Rate();
        $request->request->add(['contract_id' => $id]);


        $rates->create($request->all());
        return redirect()->back()->with('ratesSave','true');
    }
    public function editRates($id){
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $rates = Rate::find($id);
        return view('contracts.editRates', compact('rates','harbor','carrier','currency'));
    }
    public function updateRates(Request $request, $id){
        $requestForm = $request->all();
        $rate = Rate::find($id);
        $rate->update($requestForm);
        return redirect()->back()->with('editRate','true');


    }

    public function addLocalChar($id){
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objcalculation = new CalculationType();
        $objsurcharge = new Surcharge();
        $objtypedestiny = new TypeDestiny();
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $calculationT = $objcalculation->all()->pluck('name','id');
        $typedestiny = $objtypedestiny->all()->pluck('description','id');
        $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        return view('contracts.addLocalCharge', compact('harbor','carrier','currency','calculationT','typedestiny','surcharge','id'));

    }
    public function storeLocalChar(Request $request,$id){
        $localcharge = new LocalCharge();
        $request->request->add(['contract_id' => $id]);
        $localcharge =  $localcharge->create($request->all());
        $detailportOrig = $request->input('port_origlocal');
        $detailportDest = $request->input('port_destlocal');
        $detailcarrier = $request->input('carrier_id');
        foreach($detailcarrier as $c => $value)
        {
            $detailcarrier = new LocalCharCarrier();
            $detailcarrier->carrier_id =$value;
            $detailcarrier->localcharge()->associate($localcharge);
            $detailcarrier->save();
        }
        foreach($detailportOrig as $orig => $valueOrig)
        {
            foreach($detailportDest as $dest => $valueDest)
            {
                $detailport = new LocalCharPort();
                $detailport->port_orig =$valueOrig;
                $detailport->port_dest =$valueDest;
                $detailport->localcharge()->associate($localcharge);
                $detailport->save();
            }
        }
        return redirect()->back()->with('localcharSave','true')->with('activeS','active');
    }
    public function editLocalChar($id){
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objtypedestiny = new TypeDestiny();
        $objcalculation = new CalculationType();
        $objsurcharge = new Surcharge();

        $calculationT = $objcalculation->all()->pluck('name','id');
        $typedestiny = $objtypedestiny->all()->pluck('description','id');
        $surcharge = $objsurcharge->where('company_user_id','=',Auth::user()->company_user_id)->pluck('name','id');
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $localcharges = LocalCharge::find($id);
        return view('contracts.editLocalCharge', compact('localcharges','harbor','carrier','currency','calculationT','typedestiny','surcharge'));
    }
    public function updateLocalChar(Request $request, $id)
    {
        $localC = LocalCharge::find($id);

        $localC->surcharge_id = $request->input('surcharge_id');
        $localC->typedestiny_id  = $request->input('changetype');
        $localC->calculationtype_id = $request->input('calculationtype_id');
        $localC->ammount = $request->input('ammount');
        $localC->currency_id = $request->input('currency_id');
        $localC->update();

        $detailportOrig = $request->input('port_origlocal');
        $detailportDest = $request->input('port_destlocal');
        $carrier = $request->input('carrier_id');
        $deleteCarrier = LocalCharCarrier::where("localcharge_id",$id);
        $deleteCarrier->delete();
        $deletePort = LocalCharPort::where("localcharge_id",$id);
        $deletePort->delete();

        foreach($detailportOrig as $orig => $valueOrig)
        {
            foreach($detailportDest as $dest => $valueDest)
            {
                $detailport = new LocalCharPort();
                $detailport->port_orig = $valueOrig;
                $detailport->port_dest = $valueDest;
                $detailport->localcharge_id = $id;
                $detailport->save();
            }

        }
        foreach($carrier as $key)
        {
            $detailcarrier = new LocalCharCarrier();
            $detailcarrier->carrier_id = $key;
            $detailcarrier->localcharge_id = $id;
            $detailcarrier->save();
        }
        return redirect()->back()->with('localchar','true')->with('activeS','active');
    }


    public function destroy($id)
    {
        $rate = Rate::find($id);
        $rate->delete();
        return $rate;
    }

    public function deleteContract($id){

        $contract = Contract::find($id);
        if(isset($contract->rates)){
            if(isset($contract->localcharges)){
                return response()->json(['message' => count($contract->rates),'local' => count($contract->localcharges) ]);
            }else{
                return response()->json(['message' => count($contract->rates),'local' => 0]);
            }
        }
        return response()->json(['message' => 'SN','local' => 0]);
    }
    public function destroyContract($id){

        try { 

            $FileTmp = FileTmp::where('contract_id',$id)->first();
            if(count($FileTmp) > 0){
                Storage::Delete($FileTmp->name_file);
                $FileTmp->delete();
            }

            $contract = Contract::find($id);
            $contract->delete();

            return response()->json(['message' => 'Ok']);
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e]);
        }


    }

    public function destroyLocalCharges($id)
    {
        $local = LocalCharge::find($id);
        $local->forceDelete();
    }

    public function destroyRates(Request $request,$id)
    {

        $rate = Rate::find($id);
        $rate->forceDelete();
        return $rate;

    }

    public function destroymsg($id)
    {
        return view('contracts/message' ,['rate_id' => $id]);
    }


    public function UploadFileRateForContract(Request $request){
        $requestobj = $request;
        $nombre='';
        try {

            $now = new \DateTime();
            $now = $now->format('dmY_His');
            $file = $requestobj->file('file');
            $ext = strtolower($file->getClientOriginalExtension());
            $validator = \Validator::make(
                array('ext' => $ext),
                array('ext' => 'in:xls,xlsx,csv')
            );
            if ($validator->fails()) {
                $requestobj->session()->flash('message.nivel', 'danger');
                $requestobj->session()->flash('message.content', 'just archive with extension xlsx xls csv');
                return redirect()->route('contracts.edit',$requestobj->contract_id);
            }
            //obtenemos el nombre del archivo
            $nombre = $file->getClientOriginalName();
            $nombre = $now.'_'.$nombre;
            $dd = \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));
            //dd(\Storage::disk('UpLoadFile')->url($nombre));
            $contract = $requestobj->contract_id;
            $errors=0;
            Excel::Load(\Storage::disk('UpLoadFile')->url($nombre),function($reader) use($contract,$errors,$requestobj) {

                $originResul  = '';
                $destinResul  = '';
                $currencResul = '';

                if($reader->get()->isEmpty() != true){
                    Rate::where('contract_id','=',$contract)
                        ->delete();
                    FailRate::where('contract_id','=',$contract)
                        ->delete();
                } else{
                    $requestobj->session()->flash('message.nivel', 'danger');
                    $requestobj->session()->flash('message.content', 'The file is it empty');
                    return redirect()->route('contracts.edit',$contract);   
                }
                foreach ($reader->get() as $book) {
                    $originVdul = '';
                    $destinationVdul = '';

                    $carrier = Carrier::where('name','=',$book->carrier)->first();
                    $twuenty = "20";
                    $forty = "40";
                    $fortyhc = "40hc";
                    $origin = "origin";
                    $destination = "destiny";
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////

                    $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];

                    $originResul = str_replace($caracteres,'',strtolower($book->$origin));

                    $originExitsDul = Harbor::where('varation->type','like','%'.$originResul.'%')
                        ->get();
                    if(count($originExitsDul) == 1){
                        $origB=true;
                        foreach($originExitsDul as $originRc){
                            $originVdul = $originRc['id'];
                        }
                    }

                    $destinResul = str_replace($caracteres,'',strtolower($book->$destination));

                    $destinationExitsDul = Harbor::where('varation->type','like','%'.$destinResul.'%')
                        ->get();
                    if(count($destinationExitsDul) == 1){
                        $destiB=true;
                        foreach($destinationExitsDul as $destinationRc){
                            $destinationVdul = $destinationRc['id'];
                        }
                    }


                    /////////////////////////////////////////////////////////////////////////////////////////////////////////
                    $duplicate =  Rate::where('origin_port','=',$originVdul)
                        ->where('destiny_port','=',$destinationVdul)
                        ->where('carrier_id','=',$carrier['id'])
                        ->where('contract_id','=',$contract)
                        ->count();
                    if($duplicate <= 0){
                        $originResul  = '';
                        $destinResul  = '';
                        $currencResul = '';
                        $carrierResul = '';
                        $origB=false;
                        $destiB=false;
                        $carriB=false;
                        $twuentyB=false;
                        $fortyB=false;
                        $fortyhcB=false;
                        $curreB=false;
                        $originV;
                        $destinationV;
                        $carrierV;
                        $twuentyV;
                        $fortyV;
                        $fortyhcV;
                        $currencyV;
                        $values = true;

                        $currencResul = str_replace($caracteres,'',$book->currency);
                        $currenc = Currency::where('alphacode','=',$currencResul)->first();

                        $carrierResul = str_replace($caracteres,'',$book->carrier);
                        $carrier = Carrier::where('name','=',$carrierResul)->first();

                        $originResul = str_replace($caracteres,'',strtolower($book->$origin));
                        $originExits = Harbor::where('varation->type','like','%'.$originResul.'%')
                            ->get();
                        if(count($originExits) == 1){
                            $origB=true;
                            foreach($originExits as $originRc){
                                $originV = $originRc['id'];
                            }
                        }else{
                            $originV = $book->$origin.'_E_E';
                        }

                        $destinResul = str_replace($caracteres,'',strtolower($book->$destination));
                        $destinationExits = Harbor::where('varation->type','like','%'.$destinResul.'%')
                            ->get();
                        if(count($destinationExits) == 1){
                            $destiB=true;
                            foreach($destinationExits as $destinationRc){
                                $destinationV = $destinationRc['id'];
                                // dd($destinationV);
                            }
                        }else{
                            $destinationV = $book->$destination.'_E_E';
                        }
                        if(empty($carrier->id) != true){
                            $carriB=true;
                            $carrierV = $carrier->id;
                        }else{
                            $carrierV = $book->carrier.'_E_E';
                        }
                        //////
                        if(empty($book->$twuenty) != true || (int)$book->$twuenty == 0){
                            $twuentyB=true;
                            $twuentyV = (int)$book->$twuenty;
                        }
                        else{
                            $twuentyV = $book->$twuenty.'_E_E';
                        }
                        /////
                        if(empty($book->$forty) != true || (int)$book->$forty == 0){
                            $fortyB=true;
                            $fortyV = (int)$book->$forty;
                        }
                        else{
                            $fortyV = $book->$forty.'_E_E';
                        }
                        /////
                        if(empty($book->$fortyhc) != true || (int)$book->$fortyhc == 0){
                            $fortyhcB=true;
                            $fortyhcV = (int)$book->$fortyhc;
                        }
                        else{
                            $fortyhcV = $book->$fortyhc.'_E_E';
                        }

                        if((int)$book->$twuenty == 0
                           && (int)$book->$forty == 0
                           && (int)$book->$fortyhc == 0){
                            $values = false;
                        }

                        if(empty($currenc->id) != true){
                            $curreB=true;
                            $currencyV =  $currenc->id;
                        }
                        else{
                            $currencyV = $book->currency.'_E_E';
                        }
                        if( $origB == true && $destiB == true
                           && $carriB == true && $twuentyB == true
                           && $fortyB == true && $fortyhcB == true
                           && $curreB == true
                           && $values == true) {
                            Rate::create([
                                'origin_port'   => $originV,
                                'destiny_port'  => $destinationV,
                                'carrier_id'    => $carrierV,
                                'contract_id'   => $contract,
                                'twuenty'       => $twuentyV,
                                'forty'         => $fortyV,
                                'fortyhc'       => $fortyhcV,
                                'currency_id'   => $currencyV,
                            ]);
                        }
                        else{
                            if($origB == true){
                                $originV = $book->$origin;
                            }
                            if($destiB == true){
                                $destinationV = $book->$destination;
                            }
                            if($curreB == true){
                                $currencyV = $book->currency;
                            }
                            if($carriB == true){
                                $carrierV = $book->carrier;
                            }
                            if( empty($book->$origin) == true
                               && empty($book->$destination) == true
                               && empty($book->carrier) == true
                               && empty($book->currency) == true
                               && empty($book->$twuenty) == true
                               && empty($book->$forty) == true
                               && empty($book->$fortyhc) == true ) {
                            }else{
                                $duplicateFail =  FailRate::where('origin_port','=',$originV)
                                    ->where('destiny_port','=',$destinationV)
                                    ->where('carrier_id','=',$carrierV)
                                    ->where('contract_id','=',$contract)
                                    ->count();
                                if($duplicateFail <= 0){
                                    if((int)$book->$twuenty == 0
                                       && (int)$book->$forty == 0
                                       && (int)$book->$fortyhc == 0){

                                    }else {
                                        FailRate::create([
                                            'origin_port'   => $originV,
                                            'destiny_port'  => $destinationV,
                                            'carrier_id'    => $carrierV,
                                            'contract_id'   => $contract,
                                            'twuenty'       => $twuentyV,
                                            'forty'         => $fortyV,
                                            'fortyhc'       => $fortyhcV,
                                            'currency_id'   => $currencyV,
                                        ]);
                                        $errors++;
                                    }
                                }
                            }
                        }
                    }
                } //***
                if($errors > 0){
                    $requestobj->session()->flash('message.content', 'You successfully added the rate ');
                    $requestobj->session()->flash('message.nivel', 'danger');
                    $requestobj->session()->flash('message.title', 'Well done!');
                    if($errors == 1){
                        $requestobj->session()->flash('message.content', $errors.' fee is not charged correctly');
                    }else{
                        $requestobj->session()->flash('message.content', $errors.' Rates did not load correctly');
                    }
                }
                else{
                    $requestobj->session()->flash('message.nivel', 'success');
                    $requestobj->session()->flash('message.title', 'Well done!');
                }
            });
            Storage::delete($nombre);
            Rate::onlyTrashed()->where('contract_id','=',$contract)
                ->forceDelete();
            FailRate::onlyTrashed()->where('contract_id','=',$contract)
                ->forceDelete();
            return redirect()->route('Failed.Rates.Developer.For.Contracts',[$contract,1]);
            //dd($res);*/
        } catch (\Illuminate\Database\QueryException $e) {
            Storage::delete($nombre);
            Rate::onlyTrashed()->where('contract_id','=',$contract)
                ->restore();
            FailRate::onlyTrashed()->where('contract_id','=',$contract)
                ->restore();
            $requestobj->session()->flash('message.nivel', 'danger');
            $requestobj->session()->flash('message.content', 'There was an error loading the file');
            return redirect()->route('contracts.edit',$requestobj->contract_id);
        }
    }

    public function UploadFileSubchargeForContract(Request $request){
        //dd($request);
        $requestobj = $request;
        $nombre='';
        try {
            $now = new \DateTime();
            $now = $now->format('dmY_His');
            $file = $requestobj->file('file');
            $ext = strtolower($file->getClientOriginalExtension());
            $validator = \Validator::make(
                array('ext' => $ext),
                array('ext' => 'in:xls,xlsx,csv')
            );
            if ($validator->fails()) {
                $requestobj->session()->flash('message.nivel', 'danger');
                $requestobj->session()->flash('message.content', 'just archive with extension xlsx xls csv');
                return redirect()->route('contracts.edit',$requestobj->contract_id);
            }
            //obtenemos el nombre del archivo
            $nombre = $file->getClientOriginalName();
            $nombre = $now.'_'.$nombre;
            $dd = \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));
            $contract = $request->contract_id;
            $errors=0;
            Excel::Load(\Storage::disk('UpLoadFile')->url($nombre),function($reader) use($contract,$errors,$requestobj) {
                if($reader->get()->isEmpty() != true){
                    LocalCharge::where('contract_id','=',$contract)
                        ->delete();
                    FailSurCharge::where('contract_id','=',$contract)
                        ->delete();
                } else{
                    $requestobj->session()->flash('message.nivel', 'danger');
                    $requestobj->session()->flash('message.content', 'The file is it empty');
                    return redirect()->route('contracts.edit',$contract);   
                }
                $i=1;
                $SurcharExist;
                $SurcharcarrierExist;
                $SurcharPortExist;
                foreach ($reader->get() as $book) {
                    $surchargeBook        = "surcharge";
                    $originBook           = "origin";
                    $destinationBook      = "destination";
                    $carrierBook          = "carrier";
                    $calculationtypeBook  = "calculation_type";
                    $amountBook           = "amount";
                    $currencyBook         = "currency";
                    $surchargeBol       = false;
                    $carrierBol         = false;
                    $calculationtypeBol = false;
                    $currencyBol        = false;
                    $ammountBol         = false;
                    $originBol          = false;
                    $destinationBol     = false;
                    $originVar      = $book->$originBook;
                    $destinationVar = $book->$destinationBook;
                    $ammountVar     = (int)$book->$amountBook;
                    $destinytypeVar = 3;
                    $surchargeVar       ="";
                    $carrierVar         ="";
                    $calculationtypeVar ="";
                    $currencyVar        ="";
                    $SurcharExist       = "";
                    $SurcharcarrierExist = "";
                    $SurcharPortExist = "";
                    $SurcharBootPortExist = "";
                    $surcharge = Surcharge::where('name','=',$book->$surchargeBook)->where('company_user_id','=',\Auth::user()->company_user_id)->first();
                    $calculationtype = CalculationType::where('name','like','%'.$book->$calculationtypeBook.'%')->first();
                    $originExits = Harbor::where('varation->type','like','%'.strtolower($originVar).'%')
                        ->get();
                    if(count($originExits) == 1){
                        $originBol=true;
                        foreach($originExits as $originRc){
                            $originVar = $originRc['id'];
                        }
                    }else{
                        $originVar = $originVar.'_E_E';
                    }
                    $destinationExits = Harbor::where('varation->type','like','%'.strtolower($destinationVar).'%')
                        ->get();
                    if(count($destinationExits) == 1){
                        $destinationBol=true;
                        foreach($destinationExits as $destinationRc){
                            $destinationVar = $destinationRc['id'];
                        }
                    }else{
                        $destinationVar = $destinationVar.'_E_E';
                    }
                    if(empty($surcharge) != true){
                        $surchargeBol = true;
                        $surchargeVar = $surcharge['id'];
                    }
                    else{
                        $surchargeVar = $book->$surchargeBook.'_E_E';
                    }

                    $carrier = Carrier::where('name','=',$book->$carrierBook)->first();
                    if(empty($carrier) != true){
                        $carrierBol = true;
                        $carrierVar = $carrier['id'];
                    }
                    else{
                        $carrierVar = $book->$carrierBook.'_E_E';
                    }

                    if(empty($calculationtype) != true){
                        $calculationtypeBol = true;
                        $calculationtypeVar = $calculationtype['id'];
                    }
                    else{
                        $calculationtypeVar = $book->$calculationtypeBook.'_E_E';
                    }

                    $currency = Currency::where('alphacode','=',$book->$currencyBook)->first();
                    if(empty($currency) != true){
                        $currencyBol = true;
                        $currencyVar = $currency['id'];
                    }
                    else{
                        $currencyVar = $book->$currencyBook.'_E_E';
                    }
                    if(empty($ammountVar) != true){
                        $ammountBol = true;
                    }
                    else{
                        $ammountVar = $ammountVar.'_E_E';
                    }
                    if($surchargeBol == true 
                       && $carrierBol == true 
                       && $calculationtypeBol == true 
                       && $currencyBol == true 
                       && $originBol == true
                       && $destinationBol == true
                       && $ammountBol == true){ 
                        $SurcharFull = LocalCharge::where('surcharge_id','=',$surchargeVar)
                            ->where('typedestiny_id','=',$destinytypeVar)
                            ->where('contract_id','=',$contract)
                            ->where('calculationtype_id','=',$calculationtypeVar)
                            ->where('ammount','=',$ammountVar)
                            ->where('currency_id','=',$currencyVar)
                            ->whereHas('localcharcarriers',function($q) use($carrierVar){
                                $q->where('carrier_id','=',$carrierVar);
                            })
                            ->whereHas('localcharports', function($k) use($originVar,$destinationVar){
                                $k->where('port_orig','=',$originVar);
                            })->get();
                        if($SurcharFull->isEmpty() != true){
                            //  echo 'existe sur y port orig <br>';
                            foreach($SurcharFull as $Surchar){
                                $existportdest = LocalCharge::where('surcharge_id','=',$surchargeVar)
                                    ->where('typedestiny_id','=',$destinytypeVar)
                                    ->where('contract_id','=',$contract)
                                    ->where('calculationtype_id','=',$calculationtypeVar)
                                    ->where('ammount','=',$ammountVar)
                                    ->where('currency_id','=',$currencyVar)
                                    ->whereHas('localcharcarriers',function($q) use($carrierVar){
                                        $q->where('carrier_id','=',$carrierVar);
                                    })
                                    ->whereHas('localcharports', function($k) use($originVar,$destinationVar){
                                        $k->where('port_orig','=',$originVar)->where('port_dest','=',$destinationVar);
                                    })->get();
                                if($existportdest->isEmpty()){
                                    //echo 'No existe puertto destno <br>';
                                    foreach($SurcharFull as $Surchar){
                                        LocalCharPort::create([
                                            'port_orig'        => $originVar,
                                            'port_dest'        => $destinationVar,
                                            'localcharge_id'   => $Surchar['id']
                                        ]);  
                                    }
                                } else{
                                    //echo 'existe valor<br>';
                                }
                            }
                        } else {
                            // echo 'vacio<br>';
                            $idlocalchar = LocalCharge::create([
                                'surcharge_id'        => $surchargeVar,
                                'typedestiny_id'      => $destinytypeVar,
                                'contract_id'         => $contract,
                                'calculationtype_id'  => $calculationtypeVar,
                                'ammount'             => $ammountVar,
                                'currency_id'         => $currencyVar,
                            ]);
                            LocalCharPort::create([
                                'port_orig'        => $originVar,
                                'port_dest'        => $destinationVar,
                                'localcharge_id'   => $idlocalchar->id
                            ]);
                            LocalCharCarrier::create([
                                'carrier_id'      => $carrierVar,
                                'localcharge_id'  => $idlocalchar->id
                            ]);
                        }
                    } else {
                        if($surchargeBol == true){
                            $surchargeVar = $book->$surchargeBook;
                        }
                        if($carrierBol == true){
                            $carrierVar = $book->$carrierBook;
                        }
                        if($calculationtypeBol == true){
                            $calculationtypeVar = $book->$calculationtypeBook;
                        }
                        if($currencyBol == true){
                            $currencyVar = $book->$currencyBook;
                        }
                        if($originBol == true){
                            $originVar = $book->$originBook;
                        }
                        if($destinationBol == true){
                            $destinationVar = $book->$destinationBook;
                        }
                        if($ammountBol == true){
                            $ammountVar = $book->$amountBook;
                        }

                        FailSurCharge::create([
                            'surcharge_id'       => $surchargeVar,
                            'port_orig'          => $originVar,
                            'port_dest'          => $destinationVar,
                            'typedestiny_id'     => $destinytypeVar,
                            'contract_id'        => $contract,
                            'calculationtype_id' => $calculationtypeVar,
                            'ammount'            => $ammountVar,
                            'currency_id'        => $currencyVar,
                            'carrier_id'         => $carrierVar
                        ]); //*/
                        $errors++;
                    }
                    if($errors > 0){
                        $requestobj->session()->flash('message.content', 'You successfully added the rate ');
                        $requestobj->session()->flash('message.nivel', 'danger');
                        $requestobj->session()->flash('message.title', 'Well done!');
                        if($errors == 1){
                            $requestobj->session()->flash('message.content', $errors.' Subcharge is not charged correctly');
                        }else{
                            $requestobj->session()->flash('message.content', $errors.' Subcharge did not load correctly');
                        }
                    }
                    else{
                        $requestobj->session()->flash('message.nivel', 'success');
                        $requestobj->session()->flash('message.title', 'Well done!');
                    }
                    $i++;
                }
            });
            Storage::delete($nombre);
            LocalCharge::onlyTrashed()->where('contract_id','=',$contract)
                ->forceDelete();
            FailSurCharge::onlyTrashed()->where('contract_id','=',$contract)
                ->forceDelete();
            return redirect()->route('Failed.Surcharge.F.C.D',[$contract,1]);
        } catch (\Illuminate\Database\QueryException $e) {
            Storage::delete($nombre);
            LocalCharge::onlyTrashed()->where('contract_id','=',$contract)
                ->restore();
            FailSurCharge::onlyTrashed()->where('contract_id','=',$contract)
                ->restore();
            $requestobj->session()->flash('message.nivel', 'danger');
            $requestobj->session()->flash('message.content', 'There was an error loading the file');
            return redirect()->route('contracts.edit',$requestobj->contract_id);
        }
    }


    public function LoadViewImporContractFcl(){
        $harbor         = harbor::all()->pluck('display_name','id');
        $carrier        = carrier::all()->pluck('name','id');
        $companysUser   = CompanyUser::all()->pluck('name','id');
        return view('contracts.ImporContractFcl',compact('harbor','carrier','companysUser'));
    }
    public function UploadFileNewContract(Request $request){
        //dd($request);
        $now = new \DateTime();
        $now = $now->format('dmY_His');
        $type = $request->type;
        $carrierVal     = $request->carrier;
        $destinyArr     = $request->destiny;
        $originArr      = $request->origin;
        $CompanyUserId  = $request->CompanyUserId;
        $carrierBol = false;
        $destinyBol = false;
        $originBol  = false;
        $data= collect([]);
        $harbor  = harbor::all()->pluck('display_name','id');
        $carrier = carrier::all()->pluck('name','id');
        // try {
        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $validator = \Validator::make(
            array('ext' => $ext),
            array('ext' => 'in:xls,xlsx,csv')
        );
        $Contract_id;
        if ($validator->fails()) {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
            return redirect()->route('contracts.edit',$request->contract_id);
        }
        //obtenemos el nombre del archivo
        $nombre = $file->getClientOriginalName();
        $nombre = $now.'_'.$nombre;
        \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));
        $contract     = new Contract();
        $contract->name             = $request->name;
        $contract->number           = $request->number;
        $validity                   = explode('/',$request->validation_expire);
        $contract->validity         = $validity[0];
        $contract->expire           = $validity[1];
        $contract->status           = 'incomplete';
        $contract->company_user_id  = $CompanyUserId;
        $contract->save(); //*/
        $Contract_id = $contract->id;
        $fileTmp = new FileTmp();
        $fileTmp->contract_id = $Contract_id;
        $fileTmp->name_file   = $nombre;
        $fileTmp->save();

        $statustypecurren = $request->valuesCurrency;
        if($type == 1){
            $targetsArr =[ 0 => 'Currency', 1 => "20'", 2 => "40'", 3 => "40'HC"];
        }else if($type == 2){
            if($statustypecurren == 1){
                $targetsArr =[ 0 => 'Currency', 1 => "20'", 2 => "40'", 3 => "40'HC", 4 => "Calculation Type", 5 => "Charge"];
            } else if($statustypecurren == 2){
                $targetsArr =[ 0 => "20'", 1 => "40'", 2 => "40'HC", 3 => "Calculation Type", 4 => "Charge"];
            }
        }
        if($request->DatOri == false){
            array_push($targetsArr,'Origin');
        }
        else{
            $originBol = true;
            $originArr;
        }
        if($request->DatDes == false){
            array_push($targetsArr,'Destiny');
        } else {
            $destinyArr;
            $destinyBol = true;
        }
        if($request->DatCar == false){
            array_push($targetsArr,'Carrier');
        } else {
            $carrierVal;
            $carrierBol = true;
        }
        //dd($targetsArr);
        //  dd($data);
        $coordenates = collect([]);
        Excel::selectSheetsByIndex(0)
            ->Load(\Storage::disk('UpLoadFile')
                   ->url($nombre),function($reader) use($request,$coordenates) {
                       $reader->noHeading = true;
                       $reader->ignoreEmpty();
                       foreach($reader->get() as $read){
                           // dd($read);
                           $columna= array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','Ñ','O','P','Q','R','S','T','U','V');
                           for($i=0;$i<count($read);$i++){
                               $coordenates->push($columna[$i].' '.$read[$i]);
                           }
                           break;
                       }

                   });
        $boxdinamy = [
            'existorigin'   => $originBol,
            'origin'        => $originArr,
            'existdestiny'  => $destinyBol,
            'destiny'       => $destinyArr,
            'existcarrier'  => $carrierBol,
            'carrier'       => $carrierVal,
            'Contract_id'   => $Contract_id,
            'number'        => $request->number,
            'name'          => $request->name,
            'fileName'      => $nombre,
            'validatiion'   => $request->validation_expire,
        ];
        $data->push($boxdinamy);
        $countTarges = count($targetsArr);
        //dd($data);
        return view('contracts.ContractFclProcess',compact('harbor','carrier','coordenates','targetsArr','data','countTarges','type','statustypecurren','CompanyUserId'));
        /*}catch(\Exception $e){
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'Error with the archive');
            return redirect()->route('importaion.fcl');
        }//*/
    }
    public function ProcessContractFcl(Request $request){
        //dd($request);
        $requestobj = $request;
        try{
            $errors = 0;
            Excel::selectSheetsByIndex(0)
                ->Load(\Storage::disk('UpLoadFile')
                       ->url($requestobj ->FileName),function($reader) use($requestobj,$errors) {
                           $reader->noHeading = true;
                           //$reader->ignoreEmpty();
                           $currency   = "Currency";
                           $twenty     = "20'";
                           $forty      = "40'";
                           $fortyhc    = "40'HC";
                           $origin     = "origin";
                           $originExc  = "Origin";
                           $destiny    = "destiny";
                           $destinyExc = "Destiny";
                           $carrier    = "Carrier";

                           $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];

                           $i = 1;
                           foreach($reader->get() as $read){
                               $carrierVal  = '';
                               $originVal   = '';
                               $destinyVal  = '';
                               $origenFL    = '';
                               $destinyFL   = '';
                               $currencyVal = '';
                               $twentyVal   = '';
                               $fortyVal    = '';
                               $fortyhcVal  = '';
                               $originResul  = '';
                               $destinResul  = '';
                               $currencResul = '';
                               $carrierResul = '';

                               $originBol       = false;
                               $origExiBol      = false;
                               $destinyBol      = false;
                               $destiExitBol    = false;
                               $carriExitBol    = false;
                               $curreExiBol     = false;
                               $twentyExiBol    = false;
                               $fortyExiBol     = false;
                               $fortyhcExiBol   = false;
                               $carriBol        = false;
                               $values          = true;
                               if($i != 1){
                                   // 0 => 'Currency', 1 => "20'", 2 => "40'", 3 => "40'HC"
                                   //--------------- CARRIER -----------------------------------------------------------------
                                   if($requestobj->existcarrier == 1){
                                       $carriExitBol = true;
                                       $carriBol     = true;
                                       $carrierVal = $requestobj->carrier; // cuando se indica que no posee carrier 
                                   } else {
                                       $carrierVal = $read[$requestobj->Carrier]; // cuando el carrier existe en el excel
                                       $carrierResul = str_replace($caracteres,'',$carrierVal);
                                       $carrier = Carrier::where('name','=',$carrierResul)->first();
                                       if(empty($carrier->id) != true){
                                           $carriExitBol = true;
                                           $carrierVal = $carrier->id;
                                       }else{
                                           $carrierVal = $carrierVal.'_E_E';
                                       }
                                   }
                                   //--------------- ORIGEN MULTIPLE O SIMPLE ------------------------------------------------
                                   if($requestobj->existorigin == 1){
                                       $originBol = true;
                                       $origExiBol = true; //segundo boolean para verificar campos errados
                                       $randons = $requestobj->$origin;
                                   } else {
                                       // dd($read[$requestobj->$originExc]);
                                       $originVal = $read[$requestobj->$originExc];// hacer validacion de puerto en DB
                                       $originResul = str_replace($caracteres,'',strtolower($originVal));
                                       $originExits = Harbor::where('varation->type','like','%'.$originResul.'%')
                                           ->get();
                                       if(count($originExits) == 1){
                                           $origExiBol = true;
                                           foreach($originExits as $originRc){
                                               $originVal = $originRc['id'];
                                           }
                                       } else{
                                           $originVal = $originVal.'_E_E';
                                       }
                                   }
                                   //---------------- DESTINO MULTIPLE O SIMPLE -----------------------------------------------
                                   if($requestobj->existdestiny == 1){
                                       $destinyBol = true;
                                       $destiExitBol = true; //segundo boolean para verificar campos errados
                                       $randons = $requestobj->$destiny;
                                   } else {
                                       $destinyVal = $read[$requestobj->$destinyExc];// hacer validacion de puerto en DB
                                       $destinResul = str_replace($caracteres,'',strtolower($destinyVal));
                                       $destinationExits = Harbor::where('varation->type','like','%'.$destinResul.'%')
                                           ->get();
                                       if(count($destinationExits) == 1){
                                           $destiExitBol = true;
                                           foreach($destinationExits as $destinationRc){
                                               $destinyVal = $destinationRc['id'];
                                           }
                                       }else{
                                           $destinyVal = $destinyVal.'_E_E';
                                       }
                                   }
                                   //---------------- CURRENCY ---------------------------------------------------------------
                                   $currencResul = str_replace($caracteres,'',$read[$requestobj->$currency]);
                                   $currenc = Currency::where('alphacode','=',$currencResul)->first();
                                   if(empty($currenc->id) != true){
                                       $curreExiBol = true;
                                       $currencyVal =  $currenc->id;
                                   }
                                   else{
                                       $currencyVal = $read[$requestobj->$currency].'_E_E';
                                   }
                                   //dd($currencyVal);
                                   //---------------- 20' ---------------------------------------------------------------
                                   if(empty($read[$requestobj->$twenty]) != true || (int)$read[$requestobj->$twenty] == 0){
                                       $twentyExiBol = true;
                                       $twentyVal = (int)$read[$requestobj->$twenty];
                                   }
                                   else{
                                       $twentyVal = $read[$requestobj->$twenty].'_E_E';
                                   }
                                   //---------------- 40' ---------------------------------------------------------------
                                   if(empty($read[$requestobj->$forty]) != true || (int)$read[$requestobj->$forty] == 0){
                                       $fortyExiBol = true;
                                       $fortyVal = (int)$read[$requestobj->$forty];
                                   }
                                   else{
                                       $fortyVal = $read[$requestobj->$forty].'_E_E';
                                   }
                                   //---------------- 40'HC -------------------------------------------------------------
                                   if(empty($read[$requestobj->$fortyhc]) != true || (int)$read[$requestobj->$fortyhc] == 0){
                                       $fortyhcExiBol = true;
                                       $fortyhcVal = (int)$read[$requestobj->$fortyhc];
                                   }
                                   else{
                                       $fortyhcVal = $read[$requestobj->$fortyhc].'_E_E';
                                   }

                                   if((int)$read[$requestobj->$twenty] == 0
                                      && (int)$read[$requestobj->$forty] == 0
                                      && (int)$read[$requestobj->$fortyhc] == 0){
                                       $values = false;
                                   }

                                   if( $origExiBol == true 
                                      && $destiExitBol  == true
                                      && $carriExitBol  == true 
                                      && $twentyExiBol  == true 
                                      && $fortyExiBol   == true 
                                      && $twentyExiBol  == true 
                                      && $fortyhcExiBol == true
                                      && $values == true ){
                                       //good rates
                                       if($originBol == true || $destinyBol == true){
                                           foreach($randons as  $rando){
                                               //insert por arreglo de puerto
                                               if($originBol == true ){
                                                   $originVal = $rando;
                                               } else {
                                                   $destinyVal = $rando;
                                               }
                                               Rate::create([
                                                   'origin_port'   => $originVal,
                                                   'destiny_port'  => $destinyVal,
                                                   'carrier_id'    => $carrierVal,
                                                   'contract_id'   => $requestobj->Contract_id,
                                                   'twuenty'       => $twentyVal,
                                                   'forty'         => $fortyVal,
                                                   'fortyhc'       => $fortyhcVal,
                                                   'currency_id'   => $currencyVal
                                               ]);
                                           } 
                                       }else {
                                           // fila por puerto, sin expecificar origen ni destino manualmente
                                           Rate::create([
                                               'origin_port'   => $originVal,
                                               'destiny_port'  => $destinyVal,
                                               'carrier_id'    => $carrierVal,
                                               'contract_id'   => $requestobj->Contract_id,
                                               'twuenty'       => $twentyVal,
                                               'forty'         => $fortyVal,
                                               'fortyhc'       => $fortyhcVal,
                                               'currency_id'   => $currencyVal
                                           ]);
                                       }
                                   } else {
                                       // fail rates
                                       if($carriExitBol == true){
                                           if($carriBol == true){
                                               $carrier = Carrier::find($requestobj->carrier); 
                                               $carrierVal = $carrier['name'];  
                                           }else{
                                               $carrier = Carrier::where('name','=',$read[$requestobj->Carrier])->first(); 
                                               $carrierVal = $carrier['name']; 
                                           }
                                       }
                                       if($curreExiBol == true){
                                           $currencyVal = $read[$requestobj->$currency];
                                       }
                                       if( $twentyExiBol == true){
                                           if(empty($read[$requestobj->$twenty]) == true){
                                               $twentyVal = 0;
                                           } else{
                                               $twentyVal = $read[$requestobj->$twenty];
                                           }
                                       }

                                       //---------------------------------------------------
                                       if( $fortyExiBol == true){
                                           if(empty($read[$requestobj->$forty]) == true){
                                               $fortyVal = 0;
                                           } else{
                                               $fortyVal = $read[$requestobj->$forty];
                                           }
                                       }
                                       //---------------------------------------------------
                                       if( $fortyhcExiBol == true){
                                           if(empty($read[$requestobj->$fortyhc]) == true){
                                               $fortyhcVal = 0;
                                           } else{
                                               $fortyhcVal = $read[$requestobj->$fortyhc];
                                           }
                                       }

                                       if((int)$read[$requestobj->$twenty] == 0
                                          && (int)$read[$requestobj->$forty] == 0
                                          && (int)$read[$requestobj->$fortyhc] == 0){

                                       } else {

                                           if($originBol == true || $destinyBol == true){
                                               foreach($randons as  $rando){
                                                   //insert por arreglo de puerto
                                                   if($originBol == true ){
                                                       $originerr = Harbor::find($rando);
                                                       $originVal = $originerr['name'];
                                                       if($destiExitBol == true){    
                                                           $destinyVal = $read[$requestobj->$destinyExc];
                                                       }
                                                   } else {
                                                       $destinyerr = Harbor::find($rando);
                                                       $destinyVal = $destinyerr['name'];
                                                       if($origExiBol == true){
                                                           $originVal = $read[$requestobj->$originExc];                                      
                                                       }
                                                   }
                                                   FailRate::create([
                                                       'origin_port'   => $originVal,
                                                       'destiny_port'  => $destinyVal,
                                                       'carrier_id'    => $carrierVal,
                                                       'contract_id'   => $requestobj->Contract_id,
                                                       'twuenty'       => $twentyVal,
                                                       'forty'         => $fortyVal,
                                                       'fortyhc'       => $fortyhcVal,
                                                       'currency_id'   => $currencyVal,
                                                   ]);
                                               }
                                           } else {
                                               if($origExiBol == true){
                                                   $originExits = Harbor::find($originVal);
                                                   $originVal = $originExits->name;                                       
                                               }
                                               if($destiExitBol == true){  
                                                   $destinyExits = Harbor::find($destinyVal);
                                                   $destinyVal = $destinyExits->name;
                                               }
                                               FailRate::create([
                                                   'origin_port'   => $originVal,
                                                   'destiny_port'  => $destinyVal,
                                                   'carrier_id'    => $carrierVal,
                                                   'contract_id'   => $requestobj->Contract_id,
                                                   'twuenty'       => $twentyVal,
                                                   'forty'         => $fortyVal,
                                                   'fortyhc'       => $fortyhcVal,
                                                   'currency_id'   => $currencyVal,
                                               ]); //*/
                                               $errors++;
                                           }
                                       }
                                       //*/
                                   }
                               }
                               $i++;
                           }

                           Storage::delete($requestobj->FileName);
                           FileTmp::where('contract_id','=',$requestobj->Contract_id)->delete();
                           if($errors > 0){
                               $requestobj->session()->flash('message.content', 'You successfully added the rate ');
                               $requestobj->session()->flash('message.nivel', 'danger');
                               $requestobj->session()->flash('message.title', 'Well done!');
                               if($errors == 1){
                                   $requestobj->session()->flash('message.content', $errors.' fee is not charged correctly');
                               }else{
                                   $requestobj->session()->flash('message.content', $errors.' Rates did not load correctly');
                               }
                           }
                           else{
                               $requestobj->session()->flash('message.nivel', 'success');
                               $requestobj->session()->flash('message.title', 'Well done!');
                           }
                       });
            $contract = new Contract();
            $contract = Contract::find($request->Contract_id);
            $contract->status = 'publish';
            $contract->update();
            return redirect()->route('Failed.Rates.Developer.For.Contracts',[$requestobj->Contract_id,1]);

        } catch(\Illuminate\Database\QueryException $e){

            Storage::delete($request->FileName);
            FileTmp::where('contract_id','=',$requestobj->Contract_id)->delete();
            $contractobj = new Contract();
            $contractobj = Contract::find($requestobj->Contract_id);
            $contractobj->delete();

            $requestobj->session()->flash('message.nivel', 'danger');
            $requestobj->session()->flash('message.content', 'There was an error loading the file');
            return redirect()->route('importaion.fcl');
        }
    }

    public function ProcessContractFclRatSurch(Request $request){
        $companyUserId = $request->CompanyUserId;
        $UserId =\Auth::user()->id;
        ImportationRatesSurchargerJob::dispatch($request->all(),$companyUserId,$UserId);
        return redirect()->route('redirect.Processed.Information');
    }
    public function redirectProcessedInformation(){
        return view('.contracts.ProcessedInformation');
    }
    public function failRatesSurchrgesForNewContracts($id){

        $objharbor          = new Harbor();
        $objcurrency        = new Currency();
        $objcarrier         = new Carrier();
        $objsurcharge       = new Surcharge();
        $objtypedestiny     = new TypeDestiny();
        $objCalculationType = new CalculationType();
        $objsurcharge       = new Surcharge();

        $typedestiny           = $objtypedestiny->all()->pluck('description','id');
        $surchargeSelect       = $objsurcharge->all()->pluck('name','id');
        $carrierSelect         = $objcarrier->all()->pluck('name','id');
        $harbor                = $objharbor->all()->pluck('display_name','id');
        $currency              = $objcurrency->all()->pluck('alphacode','id');
        $calculationtypeselect = $objCalculationType->all()->pluck('name','id');
        $typedestiny           = $objtypedestiny->all()->pluck('description','id');
        $surchargeSelect       = $objsurcharge->where('company_user_id','=', \Auth::user()->company_user_id)->pluck('name','id');
        $calculationtypeselect = $objCalculationType->all()->pluck('name','id');

        //------------------------------- Rates ---------------------------------------------------------------

        $countrates = Rate::with('carrier','contract')->where('contract_id','=',$id)->count();
        $countfailrates = FailRate::where('contract_id','=',$id)->count();

        $rates = Rate::with('carrier','contract','port_origin','port_destiny')->where('contract_id','=',$id)->get();
        $failratesFor = FailRate::where('contract_id','=',$id)->get();

        $originV;
        $destinationV;
        $carrierV;
        $currencyV;
        $originA;
        $destinationA;
        $carrierA;
        $currencyA;
        $twuentyA;
        $fortyA;
        $fortyhcA;
        $failrates = collect([]);

        foreach( $failratesFor as $failrate){
            $carrAIn;
            $pruebacurre = "";
            $classdorigin='color:green';
            $classddestination='color:green';
            $classcarrier='color:green';
            $classcurrency='color:green';
            $classtwuenty ='color:green';
            $classforty ='color:green';
            $classfortyhc ='color:green';
            $originA =  explode("_",$failrate['origin_port']);
            //dd($originA);
            $destinationA = explode("_",$failrate['destiny_port']);
            $carrierA = explode("_",$failrate['carrier_id']);
            $currencyA = explode("_",$failrate['currency_id']);
            $twuentyA = explode("_",$failrate['twuenty']);
            $fortyA = explode("_",$failrate['forty']);
            $fortyhcA = explode("_",$failrate['fortyhc']);
            $originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
                ->first();
            $originAIn = $originOb['id'];
            $originC   = count($originA);
            if($originC <= 1){
                $originA = $originOb['name'];
            } else{
                $originA = $originA[0].' (error)';
                $classdorigin='color:red';
            }
            $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
                ->first();
            $destinationAIn = $destinationOb['id'];
            $destinationC   = count($destinationA);
            if($destinationC <= 1){
                $destinationA = $destinationOb['name'];
            } else{
                $destinationA = $destinationA[0].' (error)';
                $classddestination='color:red';
            }
            $twuentyC   = count($twuentyA);
            if($twuentyC <= 1){
                $twuentyA = $twuentyA[0];
            } else{
                $twuentyA = $twuentyA[0].' (error)';
                $classtwuenty='color:red';
            }
            $fortyC   = count($fortyA);
            if($fortyC <= 1){
                $fortyA = $fortyA[0];
            } else{
                $fortyA = $fortyA[0].' (error)';
                $classforty='color:red';
            }
            $fortyhcC   = count($fortyhcA);
            if($fortyhcC <= 1){
                $fortyhcA = $fortyhcA[0];
            } else{
                $fortyhcA = $fortyhcA[0].' (error)';
                $classfortyhc='color:red';
            }
            $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
            $carrAIn = $carrierOb['id'];
            $carrierC = count($carrierA);
            if($carrierC <= 1){
                //dd($carrierAIn);
                $carrierA = $carrierA[0];
            }
            else{
                $carrierA = $carrierA[0].' (error)';
                $classcarrier='color:red';
            }
            $currencyC = count($currencyA);
            if($currencyC <= 1){
                $currenc = Currency::where('alphacode','=',$currencyA[0])->first();
                $pruebacurre = $currenc['id'];
                $currencyA = $currencyA[0];
            }
            else{
                $currencyA = $currencyA[0].' (error)';
                $classcurrency='color:red';
            }        
            $colec = ['rate_id'         =>  $failrate->id,
                      'contract_id'     =>  $id,
                      'origin_portLb'   =>  $originA,
                      'origin_port'     =>  $originAIn,   
                      'destiny_portLb'  =>  $destinationA,
                      'destiny_port'    =>  $destinationAIn,     
                      'carrierLb'       =>  $carrierA,
                      'carrierAIn'      =>  $carrAIn,
                      'twuenty'         =>  $twuentyA,      
                      'forty'           =>  $fortyA,      
                      'fortyhc'         =>  $fortyhcA,  
                      'currency_id'     =>  $currencyA,
                      'currencyAIn'     =>  $pruebacurre,
                      'classorigin'     =>  $classdorigin,
                      'classdestiny'    =>  $classddestination,
                      'classcarrier'    =>  $classcarrier,
                      'classtwuenty'    =>  $classtwuenty,
                      'classforty'      =>  $classforty,
                      'classfortyhc'    =>  $classfortyhc,
                      'classcurrency'   =>  $classcurrency
                     ];
            $pruebacurre = "";
            $carrAIn = "";
            $failrates->push($colec);
        }

        //------------------------------- Surcharge -----------------------------------------------------------

        $countfailsurcharge = FailSurCharge::where('contract_id','=',$id)->count();
        $countgoodsurcharge = LocalCharge::where('contract_id','=',$id)->count();

        $goodsurcharges     = LocalCharge::where('contract_id','=',$id)->with('currency','calculationtype','surcharge','typedestiny','localcharcarriers.carrier','localcharports.portOrig','localcharports.portDest')->get();
        $failsurchargeS = FailSurCharge::where('contract_id','=',$id)->get();

        $failsurchargecoll = collect([]);
        foreach($failsurchargeS as $failsurcharge){
            $classdorigin           =  'color:green';
            $classddestination      =  'color:green';
            $classtypedestiny       =  'color:green';
            $classcarrier           =  'color:green';
            $classsurcharger        =  'color:green';
            $classcalculationtype   =  'color:green';
            $classammount           =  'color:green';
            $classcurrency          =  'color:green';
            $surchargeA         =  explode("_",$failsurcharge['surcharge_id']);
            $originA            =  explode("_",$failsurcharge['port_orig']);
            $destinationA       =  explode("_",$failsurcharge['port_dest']);
            $calculationtypeA   =  explode("_",$failsurcharge['calculationtype_id']);
            $ammountA           =  explode("_",$failsurcharge['ammount']);
            $currencyA          =  explode("_",$failsurcharge['currency_id']);
            $carrierA           =  explode("_",$failsurcharge['carrier_id']);
            $originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
                ->first();
            $originAIn = $originOb['id'];
            $originC   = count($originA);
            if($originC <= 1){
                $originA = $originOb['name'];
            } else{
                $originA = $originA[0].' (error)';
                $classdorigin='color:red';
            }
            $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
                ->first();
            $destinationAIn = $destinationOb['id'];
            $destinationC   = count($destinationA);
            if($destinationC <= 1){
                $destinationA = $destinationOb['name'];
            } else{
                $destinationA = $destinationA[0].' (error)';
                $classddestination='color:red';
            }
            $surchargeOb = Surcharge::where('name','=',$surchargeA[0])->where('company_user_id','=',\Auth::user()->company_user_id)->first();
            $surcharAin  = $surchargeOb['id'];
            $surchargeC = count($surchargeA);
            if($surchargeC <= 1){
                $surchargeA = $surchargeA[0];
            }
            else{
                $surchargeA         = $surchargeA[0].' (error)';
                $classsurcharger    = 'color:red';
            }
            $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
            $carrAIn = $carrierOb['id'];
            $carrierC = count($carrierA);
            if($carrierC <= 1){
                $carrierA = $carrierA[0];
            }
            else{
                $carrierA       = $carrierA[0].' (error)';
                $classcarrier   ='color:red';
            }
            $calculationtypeOb  = CalculationType::where('name','=',$calculationtypeA[0])->first();
            $calculationtypeAIn = $calculationtypeOb['id'];
            $calculationtypeC   = count($calculationtypeA);
            if($calculationtypeC <= 1){
                $calculationtypeA = $calculationtypeA[0];
            }
            else{
                $calculationtypeA       = $calculationtypeA[0].' (error)';
                $classcalculationtype   = 'color:red';
            }
            $ammountC = count($ammountA);
            if($ammountC <= 1){
                $ammountA = $failsurcharge->ammount;
            }
            else{
                $ammountA       = $ammountA[0].' (error)';
                $classammount   = 'color:red';
            }

            $currencyOb   = Currency::where('alphacode','=',$currencyA[0])->first();
            $currencyAIn  = $currencyOb['id'];
            $currencyC    = count($currencyA);
            if($currencyC <= 1){
                $currencyA = $currencyA[0];
            }
            else{
                $currencyA      = $currencyA[0].' (error)';
                $classcurrency  = 'color:red';
            }

            $typedestinyLB    = TypeDestiny::where('description','=',$failsurcharge['typedestiny_id'])->first();

            $destinyLB        = Harbor::where('id','=',$destinationA[0])->first();
            ////////////////////////////////////////////////////////////////////////////////////
            $arreglo = [
                'failSrucharge_id'      => $failsurcharge->id,
                'surchargelb'           => $surchargeA,
                'surcharge_id'          => $surcharAin,
                'origin_portLb'         => $originA,
                'origin_port'           => $originAIn,
                'destiny_portLb'        => $destinationA,
                'destiny_port'          => $destinationAIn, 
                'carrierlb'             => $carrierA,
                'carrier_id'            => $carrAIn,
                'typedestinylb'         => $typedestinyLB['description'],
                'typedestiny'           => 3,
                'ammount'               => $ammountA,
                'calculationtypelb'     => $calculationtypeA,
                'calculationtype'       => $calculationtypeAIn,
                'currencylb'            => $currencyA,
                'currency_id'           => $currencyAIn,
                'classsurcharge'        => $classsurcharger,
                'classorigin'           => $classdorigin,
                'classdestiny'          => $classddestination,
                'classtypedestiny'      => $classtypedestiny,
                'classcarrier'          => $classcarrier,
                'classcalculationtype'  => $classcalculationtype,
                'classammount'          => $classammount,
                'classcurrency'         => $classcurrency,
            ];
            //dd($arreglo);
            $failsurchargecoll->push($arreglo);
        }
        //dd($failsurchargecoll);
        //------------------------------------ Return ---------------------------------------------------------

        return  view('contracts.FailRatesSurchargerNewC',compact('rates',
                                                                 'failrates',
                                                                 'countfailrates',
                                                                 'countrates',
                                                                 'goodsurcharges',
                                                                 'failsurchargecoll',
                                                                 'countfailsurcharge',
                                                                 'countgoodsurcharge',
                                                                 'typedestiny',
                                                                 'surchargeSelect',
                                                                 'carrierSelect',
                                                                 'harbor',
                                                                 'currency',
                                                                 'calculationtypeselect',
                                                                 'id'
                                                                )); //*/
    }

    public function LoadViewRequestImporContractFcl(){
        $harbor         = harbor::all()->pluck('display_name','id');
        $carrier        = carrier::all()->pluck('name','id');
        $user   = \Auth::user();
        return view('contracts.Requests.NewRequest',compact('harbor','carrier','user'));
    }

    // esta version esta en desarrollo y pruebas --------------------------------------
    public function FailedRatesDeveloper($id,$tab){
        //$id se refiere al id del contracto
        $countrates = Rate::with('carrier','contract')->where('contract_id','=',$id)->count();
        $countfailrates = FailRate::where('contract_id','=',$id)->count();
        return view('contracts.TestFailRates2',compact('countfailrates','countrates','id','tab'));

    }

    public function FailedRatesDeveloperLoad($id,$selector){
        //$id se refiere al id del contracto
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $objcarrier = new Carrier();


        $rates = Rate::with('carrier','contract','port_origin','port_destiny','currency')->where('contract_id','=',$id)->get();
        //dd($rates);


        $originV;
        $destinationV;
        $carrierV;
        $currencyV;
        $originA;
        $destinationA;
        $carrierA;
        $currencyA;
        $twuentyA;
        $fortyA;
        $fortyhcA;
        $failrates = collect([]);
        $ratescol = collect([]);
        if($selector == 1){
            $failratesFor = FailRate::where('contract_id','=',$id)->get();
            foreach( $failratesFor as $failrate){
                $carrAIn;
                $pruebacurre = "";
                $originA        = explode("_",$failrate['origin_port']);
                $destinationA   = explode("_",$failrate['destiny_port']);
                $carrierA       = explode("_",$failrate['carrier_id']);
                $currencyA      = explode("_",$failrate['currency_id']);
                $twuentyA       = explode("_",$failrate['twuenty']);
                $fortyA         = explode("_",$failrate['forty']);
                $fortyhcA       = explode("_",$failrate['fortyhc']);

                $originOb       = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
                    ->first();
                //$originAIn = $originOb['id'];
                $originC   = count($originA);
                if($originC <= 1){
                    $originA = $originOb['name'];
                } else{
                    $originA = $originA[0].' (error)';
                    $classdorigin='color:red';
                }

                $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
                    ->first();
                //$destinationAIn = $destinationOb['id'];
                $destinationC   = count($destinationA);
                if($destinationC <= 1){
                    $destinationA = $destinationOb['name'];
                } else{
                    $destinationA = $destinationA[0].' (error)';
                }

                $twuentyC   = count($twuentyA);
                if($twuentyC <= 1){
                    $twuentyA = $twuentyA[0];
                } else{
                    $twuentyA = $twuentyA[0].' (error)';
                }

                $fortyC   = count($fortyA);
                if($fortyC <= 1){
                    $fortyA = $fortyA[0];
                } else{
                    $fortyA = $fortyA[0].' (error)';
                }

                $fortyhcC   = count($fortyhcA);
                if($fortyhcC <= 1){
                    $fortyhcA = $fortyhcA[0];
                } else{
                    $fortyhcA = $fortyhcA[0].' (error)';
                }

                $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
                //$carrAIn = $carrierOb['id'];
                $carrierC = count($carrierA);
                if($carrierC <= 1){
                    //dd($carrierAIn);
                    $carrierA = $carrierA[0];
                }
                else{
                    $carrierA = $carrierA[0].' (error)';
                }
                $currencyC = count($currencyA);
                if($currencyC <= 1){
                    $currenc = Currency::where('alphacode','=',$currencyA[0])->first();
                    //$pruebacurre = $currenc['id'];
                    $currencyA = $currencyA[0];
                }
                else{
                    $currencyA = $currencyA[0].' (error)';
                }        
                $colec = ['id'              =>  $failrate->id,
                          'contract_id'     =>  $id,
                          'origin_portLb'   =>  $originA,       //
                          'destiny_portLb'  =>  $destinationA,  // 
                          'carrierLb'       =>  $carrierA,      //
                          'twuenty'         =>  $twuentyA,      //    
                          'forty'           =>  $fortyA,        //  
                          'fortyhc'         =>  $fortyhcA,      //
                          'currency_id'     =>  $currencyA,     //
                          'operation'       =>  '1'
                         ];

                $pruebacurre = "";
                $carrAIn = "";
                $failrates->push($colec);

            }
            return DataTables::of($failrates)->addColumn('action', function ( $failrate) {
                return '<a href="#" class="" onclick="showModalsavetorate('.$failrate['id'].','.$failrate['operation'].')"><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-FailRate" data-id-failrate="'.$failrate['id'].'" class=""><i class="la la-remove"></i></a>';
            })
                ->editColumn('id', 'ID: {{$id}}')->toJson();



        } else if($selector == 2){


            foreach($rates as $rate){
                $originRate     = '';
                $detinyRate     = '';
                $carrierRate    = '';
                $currencyRate   = '';

                $originRate     = $rate['port_origin']['name'];
                $detinyRate     = $rate['port_destiny']['name'];
                $carrierRate    = $rate['carrier']['name'];
                $currencyRate   = $rate->Currency->alphacode;

                $colec = ['id'              =>  $rate->id,
                          'contract_id'     =>  $id,            //
                          'origin_portLb'   =>  $originRate,    //
                          'destiny_portLb'  =>  $detinyRate,    //
                          'carrierLb'       =>  $carrierRate,   //
                          'twuenty'         =>  $rate->twuenty, //    
                          'forty'           =>  $rate->forty,   //  
                          'fortyhc'         =>  $rate->fortyhc, //
                          'currency_id'     =>  $currencyRate,  //
                          'operation'       =>  '2'
                         ];
                $ratescol->push($colec);
            }
            return DataTables::of($ratescol)->addColumn('action', function ($ratescol) {
                return '
                <a href="#" onclick="showModalsavetorate('.$ratescol['id'].','.$ratescol['operation'].')" class=""><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-Rate" data-id-rate="'.$ratescol['id'].'" class=""><i class="la la-remove"></i></a>';
            })
                ->editColumn('id', 'ID: {{$id}}')->toJson();
        }
    }
    public function EditRatesGood($id){
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $rates = Rate::find($id);
        return view('contracts.Body-Modals.GoodEditRates', compact('rates','harbor','carrier','currency'));
    }
    public function EditRatesFail($id){
        $objcarrier = new Carrier();
        $objharbor = new Harbor();
        $objcurrency = new Currency();
        $harbor = $objharbor->all()->pluck('display_name','id');
        $carrier = $objcarrier->all()->pluck('name','id');
        $currency = $objcurrency->all()->pluck('alphacode','id');
        $failrate = FailRate::find($id);

        $originV;
        $destinationV;
        $carrierV;
        $currencyV;
        $originA;
        $destinationA;
        $carrierA;
        $currencyA;
        $twuentyA;
        $fortyA;
        $fortyhcA;

        $carrAIn;
        $pruebacurre = "";
        $classdorigin='color:green';
        $classddestination='color:green';
        $classcarrier='color:green';
        $classcurrency='color:green';
        $classtwuenty ='color:green';
        $classforty ='color:green';
        $classfortyhc ='color:green';
        $originA =  explode("_",$failrate['origin_port']);
        //dd($originA);
        $destinationA = explode("_",$failrate['destiny_port']);
        $carrierA = explode("_",$failrate['carrier_id']);
        $currencyA = explode("_",$failrate['currency_id']);
        $twuentyA = explode("_",$failrate['twuenty']);
        $fortyA = explode("_",$failrate['forty']);
        $fortyhcA = explode("_",$failrate['fortyhc']);
        $originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
            ->first();
        $originAIn = $originOb['id'];
        $originC   = count($originA);
        if($originC <= 1){
            $originA = $originOb['name'];
        } else{
            $originA = $originA[0].' (error)';
            $classdorigin='color:red';
        }
        $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
            ->first();
        $destinationAIn = $destinationOb['id'];
        $destinationC   = count($destinationA);
        if($destinationC <= 1){
            $destinationA = $destinationOb['name'];
        } else{
            $destinationA = $destinationA[0].' (error)';
            $classddestination='color:red';
        }
        $twuentyC   = count($twuentyA);
        if($twuentyC <= 1){
            $twuentyA = $twuentyA[0];
        } else{
            $twuentyA = $twuentyA[0].' (error)';
            $classtwuenty='color:red';
        }
        $fortyC   = count($fortyA);
        if($fortyC <= 1){
            $fortyA = $fortyA[0];
        } else{
            $fortyA = $fortyA[0].' (error)';
            $classforty='color:red';
        }
        $fortyhcC   = count($fortyhcA);
        if($fortyhcC <= 1){
            $fortyhcA = $fortyhcA[0];
        } else{
            $fortyhcA = $fortyhcA[0].' (error)';
            $classfortyhc='color:red';
        }
        $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
        $carrAIn = $carrierOb['id'];
        $carrierC = count($carrierA);
        if($carrierC <= 1){
            //dd($carrierAIn);
            $carrierA = $carrierA[0];
        }
        else{
            $carrierA = $carrierA[0].' (error)';
            $classcarrier='color:red';
        }
        $currencyC = count($currencyA);
        if($currencyC <= 1){
            $currenc = Currency::where('alphacode','=',$currencyA[0])->first();
            $pruebacurre = $currenc['id'];
            $currencyA = $currencyA[0];
        }
        else{
            $currencyA = $currencyA[0].' (error)';
            $classcurrency='color:red';
        }        
        $failrates = ['rate_id'         =>  $failrate->id,
                      'contract_id'     =>  $failrate->contract_id,
                      'origin_port'     =>  $originAIn,   
                      'destiny_port'    =>  $destinationAIn,     
                      'carrierAIn'      =>  $carrAIn,
                      'twuenty'         =>  $twuentyA,      
                      'forty'           =>  $fortyA,      
                      'fortyhc'         =>  $fortyhcA,  
                      'currencyAIn'     =>  $pruebacurre,
                      'classorigin'     =>  $classdorigin,
                      'classdestiny'    =>  $classddestination,
                      'classcarrier'    =>  $classcarrier,
                      'classtwuenty'    =>  $classtwuenty,
                      'classforty'      =>  $classforty,
                      'classfortyhc'    =>  $classfortyhc,
                      'classcurrency'   =>  $classcurrency
                     ];
        $pruebacurre = "";
        $carrAIn = "";
        //dd($rates);
        return view('contracts.Body-Modals.FailEditRates', compact('failrates','harbor','carrier','currency'));
    }
    public function CreateRates(Request $request, $id){



        $return = Rate::create([
            "origin_port"  => $request->origin_port,
            "destiny_port" => $request->destiny_port,
            "carrier_id"   => $request->carrier_id,
            "contract_id"  => $request->contract_id,
            "twuenty"      => $request->twuenty,
            "forty"        => $request->forty,
            "fortyhc"      => $request->fortyhc,
            "currency_id"  => $request->currency_id
        ]);
        $failrate = FailRate::find($id);
        $failrate->forceDelete();
        $request->session()->flash('message.content', 'Updated Rate' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        return redirect()->route('Failed.Rates.Developer.For.Contracts',[$request->contract_id,1]);
    }
    public function UpdateRatesD(Request $request, $id){
        //dd($request->all());

        $rate = Rate::find($id);
        $rate->origin_port  =  $request->origin_id;
        $rate->destiny_port =  $request->destiny_id;
        $rate->carrier_id   =  $request->carrier_id;
        $rate->contract_id  =  $request->contract_id;
        $rate->twuenty      =  $request->twuenty;
        $rate->forty        =  $request->forty;
        $rate->fortyhc      =  $request->fortyhc;
        $rate->currency_id  =  $request->currency_id;
        $rate->update();

        $request->session()->flash('message.content', 'Updated Rate' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $tab = 0;
        return redirect()->route('Failed.Rates.Developer.For.Contracts',[$request->contract_id,$tab]);
    }
    public function DestroyRatesF($id){
        try{
            $failRate = FailRate::find($id);
            $failRate->forceDelete();
            return 1;
        }catch(\Exception $e){
            return 2;
        }
    }
    public function DestroyRatesG($id){
        try{
            $Rate = Rate::find($id);
            $Rate->delete();
            return 1;
        }catch(\Exception $e){
            return 2;
        }
    }

    //-------------------------------------------------

    public function FailedSurchargeDeveloper($id,$tab){
        //$id se refiere al id del contracto
        $countfailsurcharge = FailSurCharge::where('contract_id','=',$id)->count();
        $countgoodsurcharge = LocalCharge::where('contract_id','=',$id)->count();
        return view('contracts.SurchargersFailOF',compact('countfailsurcharge','countgoodsurcharge','id','tab'));

    }

    public function FailSurchargeLoad($id,$selector){

        if($selector == 1){
            $objharbor          = new Harbor();
            $objcurrency        = new Currency();
            $objcarrier         = new Carrier();
            $objsurcharge       = new Surcharge();
            $objtypedestiny     = new TypeDestiny();
            $objCalculationType = new CalculationType();
            $typedestiny           = $objtypedestiny->all()->pluck('description','id');
            $surchargeSelect       = $objsurcharge->where('company_user_id','=', \Auth::user()->company_user_id)->pluck('name','id');
            $carrierSelect         = $objcarrier->all()->pluck('name','id');
            $harbor                = $objharbor->all()->pluck('display_name','id');
            $currency              = $objcurrency->all()->pluck('alphacode','id');
            $calculationtypeselect = $objCalculationType->all()->pluck('name','id');
            $failsurchargeS = FailSurCharge::where('contract_id','=',$id)->get();
            $failsurchargecoll = collect([]);
            foreach($failsurchargeS as $failsurcharge){
                $classdorigin           =  'color:green';
                $classddestination      =  'color:green';
                $classtypedestiny       =  'color:green';
                $classcarrier           =  'color:green';
                $classsurcharger        =  'color:green';
                $classcalculationtype   =  'color:green';
                $classammount           =  'color:green';
                $classcurrency          =  'color:green';
                $surchargeA         =  explode("_",$failsurcharge['surcharge_id']);
                $originA            =  explode("_",$failsurcharge['port_orig']);
                $destinationA       =  explode("_",$failsurcharge['port_dest']);
                $calculationtypeA   =  explode("_",$failsurcharge['calculationtype_id']);
                $ammountA           =  explode("_",$failsurcharge['ammount']);
                $currencyA          =  explode("_",$failsurcharge['currency_id']);
                $carrierA           =  explode("_",$failsurcharge['carrier_id']);
                $originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
                    ->first();
                $originAIn = $originOb['id'];
                $originC   = count($originA);
                if($originC <= 1){
                    $originA = $originOb['name'];
                } else{
                    $originA = $originA[0].' (error)';
                    $classdorigin='color:red';
                }
                $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
                    ->first();
                $destinationAIn = $destinationOb['id'];
                $destinationC   = count($destinationA);
                if($destinationC <= 1){
                    $destinationA = $destinationOb['name'];
                } else{
                    $destinationA = $destinationA[0].' (error)';
                    $classddestination='color:red';
                }
                $surchargeOb = Surcharge::where('name','=',$surchargeA[0])->where('company_user_id','=',\Auth::user()->company_user_id)->first();
                $surcharAin  = $surchargeOb['id'];
                $surchargeC = count($surchargeA);
                if($surchargeC <= 1){
                    $surchargeA = $surchargeA[0];
                }
                else{
                    $surchargeA         = $surchargeA[0].' (error)';
                    $classsurcharger    = 'color:red';
                }
                $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
                $carrAIn = $carrierOb['id'];
                $carrierC = count($carrierA);
                if($carrierC <= 1){
                    $carrierA = $carrierA[0];
                }
                else{
                    $carrierA       = $carrierA[0].' (error)';
                    $classcarrier   ='color:red';
                }
                $calculationtypeOb  = CalculationType::where('name','=',$calculationtypeA[0])->first();
                $calculationtypeAIn = $calculationtypeOb['id'];
                $calculationtypeC   = count($calculationtypeA);
                if($calculationtypeC <= 1){
                    $calculationtypeA = $calculationtypeA[0];
                }
                else{
                    $calculationtypeA       = $calculationtypeA[0].' (error)';
                    $classcalculationtype   = 'color:red';
                }
                $ammountC = count($ammountA);
                if($ammountC <= 1){
                    $ammountA = $failsurcharge->ammount;
                }
                else{
                    $ammountA       = $ammountA[0].' (error)';
                    $classammount   = 'color:red';
                }
                $currencyOb   = Currency::where('alphacode','=',$currencyA[0])->first();
                $currencyAIn  = $currencyOb['id'];
                $currencyC    = count($currencyA);
                if($currencyC <= 1){
                    $currencyA = $currencyA[0];
                }
                else{
                    $currencyA      = $currencyA[0].' (error)';
                    $classcurrency  = 'color:red';
                }
                $typedestinyLB    = TypeDestiny::where('description',$failsurcharge['typedestiny_id'])->first();

                ////////////////////////////////////////////////////////////////////////////////////
                $arreglo = [
                    'id'                    => $failsurcharge->id,
                    'surchargelb'           => $surchargeA,
                    'origin_portLb'         => $originA,
                    'destiny_portLb'        => $destinationA,
                    'carrierlb'             => $carrierA,
                    'typedestinylb'         => $typedestinyLB['description'],
                    'ammount'               => $ammountA,
                    'calculationtypelb'     => $calculationtypeA,
                    'currencylb'            => $currencyA,
                    'classsurcharge'        => $classsurcharger,
                    'classorigin'           => $classdorigin,
                    'classdestiny'          => $classddestination,
                    'classtypedestiny'      => $classtypedestiny,
                    'classcarrier'          => $classcarrier,
                    'classcalculationtype'  => $classcalculationtype,
                    'classammount'          => $classammount,
                    'classcurrency'         => $classcurrency,
                    'operation'             => 1
                ];
                //dd($arreglo);
                $failsurchargecoll->push($arreglo);

            }
            //dd($failsurchargecoll);
            return DataTables::of($failsurchargecoll)->addColumn('action', function ( $failsurchargecoll) {
                return '<a href="#" class="" onclick="showModalsavetosurcharge('.$failsurchargecoll['id'].','.$failsurchargecoll['operation'].')"><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-Fail-Surcharge" data-id-failSurcharge="'.$failsurchargecoll['id'].'" class=""><i class="la la-remove"></i></a>';
            })
                ->editColumn('id', 'ID: {{$id}}')->toJson();

        }else if($selector == 2){
            $goodsurcharges     = LocalCharge::where('contract_id','=',$id)->with('currency','calculationtype','surcharge','typedestiny','localcharcarriers.carrier','localcharports.portOrig','localcharports.portDest')->get();
            $surchargecollection = collect([]);
            foreach($goodsurcharges as $surcharge){
                $origin             = '';
                $destiny            = '';
                $surchargeName      = '';
                $typedestiny        = '';
                $calculationtype    = '';
                $ammount            = '';
                $carrier            = '';
                $currency           = '';
                //dd($surcharge->localcharports);
                $PuertosOrigins = $surcharge->localcharports->pluck('port_orig')->unique();
                foreach($PuertosOrigins as $PortOrigin){
                    $OriginObj = Harbor::find($PortOrigin);
                    $origin = $origin.$OriginObj->name.'.';
                }
                //dd($surcharge->localcharports);
                $PuertosDestins = $surcharge->localcharports->pluck('port_dest')->unique();
                foreach($PuertosDestins as $PortDestiny){
                    $DestinyObj = Harbor::find($PortDestiny);
                    $destiny = $destiny.$DestinyObj->name.'. ';
                }

                $carrierArre = $surcharge->localcharcarriers->pluck('carrier')->pluck('name');
                foreach($carrierArre as $carrierName){
                    $carrier = $carrier.$carrierName.'. ';
                }

                $surchargeName   = $surcharge->surcharge['name'];
                $typedestiny     = $surcharge->typedestiny->description;
                $calculationtype = $surcharge->calculationtype->name;
                $ammount         = $surcharge->ammount;
                $currency        = $surcharge->currency->alphacode;
                $arreglo = [
                    'id'                => $surcharge->id,
                    'surchargelb'       => $surchargeName,
                    'origin_portLb'     => $origin,
                    'destiny_portLb'    => $destiny,
                    'carrierlb'         => $carrier,
                    'typedestinylb'     => $typedestiny,
                    'ammount'           => $ammount,
                    'calculationtypelb' => $calculationtype,
                    'currencylb'        => $currency,
                    'operation'         => 2
                ];

                $surchargecollection->push($arreglo);
            }
            //dd($surchargecollection);
            return DataTables::of($surchargecollection)->addColumn('action', function ( $surchargecollection) {
                return '<a href="#" class="" onclick="showModalsavetosurcharge('.$surchargecollection['id'].','.$surchargecollection['operation'].')"><i class="la la-edit"></i></a>
                &nbsp;
                <a href="#" id="delete-Surcharge" data-id-Surcharge="'.$surchargecollection['id'].'" class=""><i class="la la-remove"></i></a>';
            })
                ->editColumn('id', 'ID: {{$id}}')->toJson();
        }
    }

    public function EditSurchargersGood($id){
        $objharbor          = new Harbor();
        $objcurrency        = new Currency();
        $objcarrier         = new Carrier();
        $objsurcharge       = new Surcharge();
        $objtypedestiny     = new TypeDestiny();
        $objCalculationType = new CalculationType();

        $typedestiny           = $objtypedestiny->all()->pluck('description','id');
        $surchargeSelect       = $objsurcharge->where('company_user_id','=', \Auth::user()->company_user_id)->pluck('name','id');
        $carrierSelect         = $objcarrier->all()->pluck('name','id');
        $harbor                = $objharbor->all()->pluck('display_name','id');
        $currency              = $objcurrency->all()->pluck('alphacode','id');
        $calculationtypeselect = $objCalculationType->all()->pluck('name','id');

        $goodsurcharges  = LocalCharge::with('currency','calculationtype','surcharge','typedestiny','localcharcarriers.carrier','localcharports.portOrig','localcharports.portDest')->find($id);
        //dd($goodsurcharges);
        return view('contracts.Body-Modals.GoodEditSurcharge', compact('goodsurcharges',
                                                                       'harbor',
                                                                       'carrierSelect',
                                                                       'currency',
                                                                       'surchargeSelect',
                                                                       'typedestiny',
                                                                       'calculationtypeselect'));
    }
    public function EditSurchargersFail($id){
        $objharbor          = new Harbor();
        $objcurrency        = new Currency();
        $objcarrier         = new Carrier();
        $objsurcharge       = new Surcharge();
        $objtypedestiny     = new TypeDestiny();
        $objCalculationType = new CalculationType();
        $typedestiny           = $objtypedestiny->all()->pluck('description','id');
        $surchargeSelect       = $objsurcharge->where('company_user_id','=', \Auth::user()->company_user_id)->pluck('name','id');
        $carrierSelect         = $objcarrier->all()->pluck('name','id');
        $harbor                = $objharbor->all()->pluck('display_name','id');
        $currency              = $objcurrency->all()->pluck('alphacode','id');
        $calculationtypeselect = $objCalculationType->all()->pluck('name','id');

        $failsurcharge = FailSurCharge::find($id);

        $classdorigin           =  'color:green';
        $classddestination      =  'color:green';
        $classtypedestiny       =  'color:green';
        $classcarrier           =  'color:green';
        $classsurcharger        =  'color:green';
        $classcalculationtype   =  'color:green';
        $classammount           =  'color:green';
        $classcurrency          =  'color:green';
        $surchargeA         =  explode("_",$failsurcharge['surcharge_id']);
        $originA            =  explode("_",$failsurcharge['port_orig']);
        $destinationA       =  explode("_",$failsurcharge['port_dest']);
        $calculationtypeA   =  explode("_",$failsurcharge['calculationtype_id']);
        $ammountA           =  explode("_",$failsurcharge['ammount']);
        $currencyA          =  explode("_",$failsurcharge['currency_id']);
        $carrierA           =  explode("_",$failsurcharge['carrier_id']);

        $originOb  = Harbor::where('varation->type','like','%'.strtolower($originA[0]).'%')
            ->first();
        $originAIn = $originOb['id'];
        $originC   = count($originA);
        if($originC <= 1){
            //$originA = $originOb['name'];
        } else{
            //$originA = $originA[0].' (error)';
            $classdorigin='color:red';
        }
        $destinationOb  = Harbor::where('varation->type','like','%'.strtolower($destinationA[0]).'%')
            ->first();
        $destinationAIn = $destinationOb['id'];
        $destinationC   = count($destinationA);
        if($destinationC <= 1){
            //$destinationA = $destinationOb['name'];
        } else{
            //$destinationA = $destinationA[0].' (error)';
            $classddestination='color:red';
        }

        $surchargeOb = Surcharge::where('name','=',$surchargeA[0])->where('company_user_id','=',\Auth::user()->company_user_id)->first();
        $surcharAin  = $surchargeOb['id'];
        $surchargeC = count($surchargeA);
        if($surchargeC <= 1){
            //$surchargeA = $surchargeA[0];
        }
        else{
            //$surchargeA         = $surchargeA[0].' (error)';
            $classsurcharger    = 'color:red';
        }
        $carrierOb =   Carrier::where('name','=',$carrierA[0])->first();
        $carrAIn = $carrierOb['id'];
        $carrierC = count($carrierA);
        if($carrierC <= 1){
            //$carrierA = $carrierA[0];
        }
        else{
            //$carrierA       = $carrierA[0].' (error)';
            $classcarrier   ='color:red';
        }
        $calculationtypeOb  = CalculationType::where('name','=',$calculationtypeA[0])->first();
        $calculationtypeAIn = $calculationtypeOb['id'];
        $calculationtypeC   = count($calculationtypeA);
        if($calculationtypeC <= 1){
            //$calculationtypeA = $calculationtypeA[0];
        }
        else{
            //$calculationtypeA       = $calculationtypeA[0].' (error)';
            $classcalculationtype   = 'color:red';
        }
        $ammountC = count($ammountA);
        if($ammountC <= 1){
            $ammountA = $failsurcharge['ammount'];
        }
        else{
            $ammountA       = $ammountA[0].' (error)';
            $classammount   = 'color:red';
        }
        $currencyOb   = Currency::where('alphacode','=',$currencyA[0])->first();
        $currencyAIn  = $currencyOb['id'];
        $currencyC    = count($currencyA);
        if($currencyC <= 1){
            // $currencyA = $currencyA[0];
        }
        else{
            $currencyA      = $currencyA[0].' (error)';
            $classcurrency  = 'color:red';
        }
        $typedestinyLB    = TypeDestiny::find($failsurcharge['typedestiny_id']);


        ////////////////////////////////////////////////////////////////////////////////////
        $failsurchargeArre = [
            'id'                    => $failsurcharge['id'],
            'surcharge'             => $surcharAin,
            'origin_port'           => $originAIn,
            'destiny_port'          => $destinationAIn,
            'carrier'               => $carrAIn,
            'contract_id'           => $failsurcharge['contract_id'],
            'typedestiny'           => $typedestinyLB['id'],
            'ammount'               => $ammountA,
            'calculationtype'       => $calculationtypeAIn,
            'currency'              => $currencyAIn,
            'classsurcharge'        => $classsurcharger,
            'classorigin'           => $classdorigin,
            'classdestiny'          => $classddestination,
            'classtypedestiny'      => $classtypedestiny,
            'classcarrier'          => $classcarrier,
            'classcalculationtype'  => $classcalculationtype,
            'classammount'          => $classammount,
            'classcurrency'         => $classcurrency,
        ];
        //dd($arreglo);


        //dd($failsurchargeArre);
        return view('contracts.Body-Modals.FailEditSurcharge', compact('failsurchargeArre',
                                                                       'harbor',
                                                                       'carrierSelect',
                                                                       'currency',
                                                                       'surchargeSelect',
                                                                       'typedestiny',
                                                                       'calculationtypeselect'));
    }
    public function CreateSurchargers(Request $request, $id){
        //dd($request->all());

        $surchargeVar       = $request->surcharge_id;
        $originVarArr       = $request->port_origlocal;
        $destinationVarArr  = $request->port_destlocal;
        $typedestinyVar     = $request->changetype;
        $carrierVarArr      = $request->carrier_id;
        $calculationtypeVar = $request->calculationtype_id;
        $ammountVar         = $request->ammount;
        $currencyVar        = $request->currency_id;
        $contractVar        = $request->contract_id;

        $failSurcharge = new FailSurCharge();
        $failSurcharge = FailSurCharge::find($id);
        $SurchargeId = LocalCharge::create([
            'surcharge_id'          => $surchargeVar,
            'typedestiny_id'        => $typedestinyVar,
            'contract_id'           => $contractVar,
            'calculationtype_id'    => $calculationtypeVar,
            'ammount'               => $ammountVar,
            'currency_id'           => $currencyVar
        ]);
        foreach($originVarArr as $originVar){
            foreach($destinationVarArr as $destinationVar){
                LocalCharPort::create([
                    'port_orig'         => $originVar,
                    'port_dest'         => $destinationVar,
                    'localcharge_id'    => $SurchargeId->id
                ]);
            }
        }
        foreach($carrierVarArr as $carrierVar){
            LocalCharCarrier::create([
                'carrier_id'        => $carrierVar,
                'localcharge_id'    => $SurchargeId->id  
            ]);
        }
        $failSurcharge->forceDelete();
        $request->session()->flash('message.content', 'Surcharge Updated' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        return redirect()->route('Failed.Surcharge.F.C.D',[$request->contract_id,1]);

    }
    public function UpdateSurchargersD(Request $request, $id){
        //dd($request->all());

        $surchargeVar          =  $request->surcharge_id; // id de la columna surchage_id
        $contractVar           =  $request->contract_id;
        $originVarArr          =  $request->port_origlocal;
        $destinationVarArr     =  $request->port_destlocal;
        $typedestinyVar        =  $request->changetype;
        $calculationtypeVar    =  $request->calculationtype_id;
        $ammountVar            =  $request->ammount;
        $currencyVar           =  $request->currency_id;
        $carrierVarArr         =  $request->carrier_id;

        $SurchargeId = new LocalCharge();
        $SurchargeId  = LocalCharge::find($id);
        $SurchargeId->surcharge_id          = $surchargeVar;
        $SurchargeId->typedestiny_id        = $typedestinyVar;
        $SurchargeId->contract_id           = $contractVar;
        $SurchargeId->calculationtype_id    = $calculationtypeVar;
        $SurchargeId->ammount               = $ammountVar;
        $SurchargeId->currency_id           = $currencyVar;
        $SurchargeId->update();
        LocalCharPort::where('localcharge_id','=',$SurchargeId->id)->forceDelete();
        foreach($originVarArr as $originVar){
            foreach($destinationVarArr as $destinationVar){
                LocalCharPort::create([
                    'port_orig'         => $originVar,
                    'port_dest'         => $destinationVar,
                    'localcharge_id'    => $SurchargeId->id
                ]); //
            }
        }
        LocalCharCarrier::where('localcharge_id','=',$SurchargeId->id)->forceDelete();
        foreach($carrierVarArr as $carrierVar){
            LocalCharCarrier::create([
                'carrier_id'        => $carrierVar,
                'localcharge_id'    => $SurchargeId->id  
            ]); //
        }

        $request->session()->flash('message.content', 'Surcharge Updated' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        return redirect()->route('Failed.Surcharge.F.C.D',[$request->contract_id,0]);
    }
    public function DestroySurchargersF($id){
        try{
            $failsurCharge = FailSurCharge::find($id);
            $failsurCharge->forceDelete();
            return 1;
        }catch(\Exception $e){
            return 2;
        }
    }
    public function DestroySurchargersG($id){
        try{
            $surchargers = LocalCharge::find($id);
            $surchargers->delete();
            return 1;
        }catch(\Exception $e){
            return 2;
        }
    }
}