<?php

namespace App\Http\Controllers;

use App\Carrier;
use App\Http\Requests\StoreCarriers;
use App\Jobs\SynchronImgCarrierJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;

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
                <a href="#" title="Edit Carrier" onclick="showModal(' . $carriers->id . ',1)">
                    <samp class="la la-edit" style="font-size:20px; color:#031B4E"></samp>
                </a>
                &nbsp;&nbsp;
                <a href="#" class="delete-carrier" data-id-carrier="' . $carriers->id . '" data-info="id:' . $carriers->id . ' Number Carrier: ' . $carriers->name . '"  title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>';
            })

            ->make();
    }

    public function store(StoreCarriers $request)
    {
        $file = $request->file('file');
        $nameImg = $file->getClientOriginalName();
        $fillbooll = Storage::disk('carriers')->put($nameImg, \File::get($file));

        if ($fillbooll) {
            $carrier = new Carrier();
            $carrier->name = $request->name;
            $carrier->image = $nameImg;
            $caracteres = ['*', '/', '.', '?', '"', 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, '{', '}', '[', ']', '+', '_', '|', '°', '!', '$', '%', '&', '(', ')', '=', '¿', '¡', ';', '>', '<', '^', '`', '¨', '~', ':'];

            foreach ($request->variation as $variation) {
                $variation = str_replace($caracteres, '', $variation);
                $arreglo[] = trim(strtolower($variation));
            }

            $type['type'] = $arreglo;
            $json = json_encode($type);
            $carrier->varation = $json;
            $carrier->save();
            // ProcessContractFile::dispatch($carrier->id, $nameImg, 'n/a', 'carrier');
            Storage::disk('s3_upload')->put('imgcarrier/' . $nameImg, \File::get($file), 'public');
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
        $decodejosn = json_decode($carrier->varation, true);
        $decodejosn = $decodejosn['type'];

        return view('carriers.Body-Modals.edit', compact('carrier', 'image', 'decodejosn'));
    }

    public function update(Request $request, $id)
    {
        $carrier = Carrier::find($id);

        $caracteres = ['*', '/', '.', '?', '"', 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, '{', '}', '[', ']', '+', '_', '|', '°', '!', '$', '%', '&', '(', ')', '=', '¿', '¡', ';', '>', '<', '^', '`', '¨', '~', ':'];

        if ($request->variation != null) {
            foreach ($request->variation as $variation) {
                $variation = str_replace($caracteres, '', $variation);
                $arreglo[] = trim(strtolower($variation));
            }
            $type['type'] = $arreglo;
            $json = json_encode($type);
            $carrier->varation = $json;

        }
        
        if(isset($request->file)){
            $file = $request->file('file');
            $nameImg = $file->getClientOriginalName();
            $carrier->image = $nameImg;
        }
        
        $carrier->name = $request->name;
        $carrier->update();

        if ($request->DatImag) {
            Storage::disk('carriers')->delete($carrier->image);

            //$fillbool = Storage::disk('carriers')->put($request->image, \File::get($file));
            $fillbool = Storage::disk('carriers')->put($nameImg, \File::get($file));
            if ($fillbool) {
                Storage::disk('s3_upload')->put('imgcarrier/' . $nameImg, \File::get($file),'public');
                // Storage::disk('s3_upload')->put('imgcarrier/'.$request->image, $file, 'public');
                // ProcessContractFile::dispatch($id, $request->image, 'n/a', 'carrier');
            }
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Your carrier was updated');

        return redirect()->route('managercarriers.index');
    }

    public function destroy($id)
    {
        try {
            $carrier = Carrier::find($id);
            Storage::disk('carriers')->delete($carrier->image);
            $carrier->delete();

            return response()->json(['success' => '1']);
        } catch (\Exception $e) {
            return response()->json(['success' => '2']);
        }
    }

    public function synchronous()
    {
        SynchronImgCarrierJob::dispatch();

        return redirect()->route('managercarriers.index');
    }
}
