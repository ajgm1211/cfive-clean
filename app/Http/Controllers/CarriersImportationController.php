<?php

namespace App\Http\Controllers;

use App\Carrier;
use App\AutoImportation;
use App\NewContractRequest;
use Illuminate\Http\Request;
use App\CarrierautoImportation;
use Yajra\Datatables\Datatables;
use App\Jobs\SelectionAutoImportJob;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ForwardRequestAutoImportJob;
use App\SurchergerForCarrier as FiltroSucharger;

class CarriersImportationController extends Controller
{

    public function index()
    {
        return view('importationCarrier.index');
    }

    public function create()
    {   
        $autoImports = AutoImportation::with('carriersAutoImportation.carrier')->get();
        return DataTables::of($autoImports)
            ->editColumn('name', function ($autoImport){ 
                return $autoImport->name;
            })
            ->editColumn('carriers', function ($autoImport){ 
                return str_replace(['[',']','"'],'',$autoImport->carriersAutoImportation->pluck('carrier')->pluck('name'));
            })
            ->addColumn('status', function ($autoImport){ 
                $value = null;
                if($autoImport->status){
                    $value = '<span style="color:#0b790b"><li>Active</li></span>';
                } else {
                    $value = '<span style="color:red"><li>Innactive</li></span>';
                }
                return $value;
            })
            ->addColumn('action', function ( $autoImport) {
                return '
                    <a  id="edit_l" onclick="AbrirModal('."'carrier-import-edit'".','.$autoImport->id.')" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                    <i class="la la-edit"></i>
                    </a>

                    <a  id="delete-company" data-delete="'.$autoImport->id.'"  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" >
											<i id="rm_l'.$autoImport->id.'" class="la la-times-circle"></i>
										</a>

                    ';
            })
            ->rawColumns(['checkbox','action'])
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function add(){
        $carriers = Carrier::pluck('name','id');
        //dd($companies);
        return view('importationCarrier.add',compact('carriers'));
    }

    public function store(Request $request)
    {
        $name       = $request->input('name');
        $carrier_id = $request->input('carrier_id');
        $status     = $request->input('status');

        $carrier  = AutoImportation::whereHas('carriersAutoImportation',function($query) use($carrier_id) {
            $query->whereIn('carrier_id',$carrier_id);
        })->get();

        if(count($carrier) == 0){
            $autoimport = new AutoImportation();
            $autoimport->name   =  $name;
            $autoimport->status =  $status;
            $autoimport->save();
            foreach($carrier_id as $carrier_run){
                $carrier = new CarrierautoImportation();
                $carrier->carrier_id = $carrier_run;
                $carrier->auto_importation_id = $autoimport->id;
                $carrier->save();
            }
            $request->session()->flash('message.content', 'Carriers or Carrier registered' );
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
        } else {
            $request->session()->flash('message.content', 'Carriers or Carrier already registered' );
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.title', 'Well done!');
        }
        //dd($request->all());
        return back();
    }

    public function edit($id)
    {
        $autoimport = AutoImportation::find($id);
        $autoimport = $autoimport->load('carriersAutoImportation');
        $carriers = Carrier::pluck('name','id');
        //dd($autoimport->carriersAutoImportation->pluck('carrier_id'));
        return view('importationCarrier.edit',compact('autoimport','carriers'));
    }

    public function update(Request $request, $id)
    {
        //dd($request->all());
        $name       = $request->input('name');
        $carrier_id = $request->input('carrier_id');
        $status     = $request->input('status');

        $autoimport = AutoImportation::find($id);
        $autoimport->name   = $name;
        $autoimport->status = $status;
        $autoimport->save();
        CarrierautoImportation::where('auto_importation_id',$autoimport->id)->delete();
        foreach($carrier_id as $carrier_run){
            $carrier = new CarrierautoImportation();
            $carrier->carrier_id = $carrier_run;
            $carrier->auto_importation_id = $autoimport->id;
            $carrier->save();
        }
        $request->session()->flash('message.content', 'Company Updated' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        return back();
    }

    public function destroy($id)
    {
        try{
            $autoimport = AutoImportation::find($id);
            $autoimport->delete();
            return response()->json(['success' => '1']);
        } catch(\Exception $e){
            return response()->json(['success' => '2']);
        }
    }

    // Surchergers -------------------------------------------------------------------------------------------------------------

    public function indexFiltro()
    {
        return view('importationCarrier.show');
    }

    public function show($id){

    }

    public function show2()
    {
        $filtrosSurch = FiltroSucharger::all();
        return DataTables::of($filtrosSurch)
            ->editColumn('name', function ($filtroSur){ 
                return $filtroSur->name;
            })
            ->addColumn('action', function ( $filtroSur) {
                return '
                    <a  id="edit_l" onclick="AbrirModal('."'surchar-edit'".','.$filtroSur->id.')" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                    <i class="la la-edit"></i>
                    </a>

                    <a  id="delete-surcharger" data-delete="'.$filtroSur->id.'"  class="m_sweetalert_demo_8 m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="delete" >
											<i id="" class="la la-times-circle"></i>
										</a>
                    ';
            })
            ->rawColumns(['checkbox','action'])
            ->editColumn('id', '{{$id}}')->toJson();
    }

    public function addFiltro($id){
        //dd($companies);
        return view('importationCarrier.addSurcharge');
    }

    public function storeFiltro(Request $request){
        //dd($request->all());
        $name            = $request->input('surcharger');
        $filtro_obj = FiltroSucharger::where('name',$name)->get();
        if(count($filtro_obj) == 0){
            $filtro = new FiltroSucharger();
            $filtro->name = $name;
            $filtro->save();
            $request->session()->flash('message.content', 'Surcharger filter registered' );
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
        } else {
            $request->session()->flash('message.content', 'Surcharger filter already registered' );
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.title', 'Well done!');
        }
        return back();
    }

    public function editFiltro($id)
    {
        $filtro = FiltroSucharger::find($id);
        return view('importationCarrier.editSurcharger',compact('filtro','companies'));
    }

    public function UpdateFiltro(Request $request,$id){
        $filtro = FiltroSucharger::find($id);
        $filtro->name = $request->name;
        $filtro->save();
        $request->session()->flash('message.content', 'Surcharger filter updated' );
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        return back();

    }

    public function DestroyFiltro($id){
        try{
            $filtro = FiltroSucharger::find($id);
            $filtro->delete();            
            return response()->json(['success' => '1']);
        } catch(\Exception $e){
            return response()->json(['success' => '2']);
        }
    }

    // LIST FOR AUTOMATIC IMPORTER --------------------------------------------------------------------------------------------

    public function ShowModalForward(){
        return view('importationCarrier.forwardrequest');
    }
    public function forwardRequest(Request $request){
        $response   = null;
        $between    = null;
        try{
            $between    = explode('/',$request->between);
            $dateStart  = trim($between[0]);
            $dateEnd    = trim($between[1]);
            ForwardRequestAutoImportJob::dispatch($dateStart,$dateEnd,'fcl');
            $response   = 1;
        } catch(Exception $e){
            $response   = 2;
        }
        return response()->json(['success' => $response]);
    }

    // TEST -------------------------------------------------------------------------------------------------------------------
    public function test(){
        $dateStart  = '2019-08-01';
        $dateEnd    = '2019-08-21';
        $requets    = NewContractRequest::whereBetween('created',[$dateStart,$dateEnd])->get();
        foreach($requets as $requet){
            $existsS3 = Storage::disk('s3_upload')->exists('Request/FCL/'.$requet->namefile);
            if($existsS3 == true && $requet->status == $status){
                SelectionAutoImportJob::dispatch($requet->id,'fcl');
            } else{
                $existsLocal = Storage::disk('FclRequest')->exists($requet->namefile);
                if($existsLocal){
                    $name       = $requet->namefile;
                    $s3         = \Storage::disk('s3_upload');
                    //$file       = \Storage::disk('FclRequest')->get($file);
                    $file       = File::get(storage_path('app/public/Request/Fcl/'.$name));
                    $s3 = $s3->put('Request/FCL/'.$name, $file, 'public');
                    if($s3 == true && $requet->status == $status){
                        SelectionAutoImportJob::dispatch($requet->id,'fcl');
                    }
                }
            }
        }
    }
}
