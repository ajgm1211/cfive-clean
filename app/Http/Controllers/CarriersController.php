<?php

namespace App\Http\Controllers;

use App\Carrier;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Jobs\ProcessContractFile;
use App\Jobs\SynchronImgCarrierJob;
use Illuminate\Support\Facades\Storage;

class CarriersController extends Controller
{

    public function index()
    {
        return view('carriers.index');   
    }

    public function create()
    {
        $carriers = Carrier::all();
        return Datatables::of($carriers)
            ->addColumn('name', function ($carriers) {
                return $carriers->name;
            })
            ->addColumn('image', function ($carriers) {
                return $carriers->image;
            })
            ->addColumn('action', function ($carriers) {

                return '
                &nbsp;&nbsp;
                <a href="#" title="Edit Carrier" onclick="showModal('.$carriers->id.',1)">
                    <samp class="la la-edit" style="font-size:20px; color:#031B4E"></samp>
                </a>
                &nbsp;&nbsp;
                <a href="#" class="delete-carrier" data-id-carrier="'.$carriers->id.'" data-info="id:'.$carriers->id.' Number Carrier: '.$carriers->name.'"  title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>';
            })

            ->make();
    }

    public function store(Request $request)
    {
        $file       = $request->file('file');
        $fillbooll  = Storage::disk('carriers')->put($request->image,\File::get($file));
        if($fillbooll){   
            $carrier = new Carrier();
            $carrier->name  = $request->name;
            $carrier->image = $request->image;
            $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];

            foreach($request->variation as $variation){
                $variation = str_replace($caracteres,'',$variation);
                $arreglo[] =  trim(strtolower($variation));
            }

            $type['type']       = $arreglo;
            $json               = json_encode($type);
            $carrier->varation  = $json;
            $carrier->save();
            ProcessContractFile::dispatch($carrier->id,$request->image,'n/a','carrier');
        }
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Your carrier was created');
        return redirect()->route('managercarriers.index');
    }

    public function show($id)
    {
        return view('carriers.Body-Modals.add');
    }

    public function edit($id)
    {
        $carrier = Carrier::find($id);
        $image = Storage::disk('carriers')->url($carrier->image);
        //dd($image);
        $decodejosn = json_decode($carrier->varation,true);
        $decodejosn = $decodejosn['type'];
        return view('carriers.Body-Modals.edit',compact('carrier','image','decodejosn'));
    }

    public function update(Request $request, $id)
    {
        //dd($request->all());
        $carrier = Carrier::find($id);
        $carrier->name  = $request->name;
        if($request->DatImag){
            Storage::disk('carriers')->delete($carrier->image);
            $file   = $request->file('file');
            $fillbool = Storage::disk('carriers')->put($request->image,\File::get($file));
            if($fillbool)
                ProcessContractFile::dispatch($id,$request->image,'n/a','carrier');
        }

        $caracteres = ['*','/','.','?','"',1,2,3,4,5,6,7,8,9,0,'{','}','[',']','+','_','|','°','!','$','%','&','(',')','=','¿','¡',';','>','<','^','`','¨','~',':'];

        foreach($request->variation as $variation){
            $variation = str_replace($caracteres,'',$variation);
            $arreglo[] =  trim(strtolower($variation));
        }

        $type['type']       = $arreglo;
        $json               = json_encode($type);
        $carrier->varation  = $json;
        $carrier->image     = $request->image;
        $carrier->save();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Your carrier was updated');
        return redirect()->route('managercarriers.index');
    }

    public function destroy($id)
    {
        try{
            $carrier = Carrier::find($id);
            Storage::disk('carriers')->delete($carrier->image);
            $carrier->delete();
            return response()->json(['success' => '1']);
        } catch(\Exception $e){
            return response()->json(['success' => '2']);            
        }

    }

    public function synchronous(){
        SynchronImgCarrierJob::dispatch();
        return redirect()->route('managercarriers.index');
    }
}
