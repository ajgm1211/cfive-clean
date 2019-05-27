<?php

namespace App\Http\Controllers;

use App\Carrier;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Storage;

class CarriersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('carriers.index');   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
                <!--
                &nbsp;&nbsp;
                <a href="#" class="eliminarrequest" data-id-request="'.$carriers->id.'" data-info="id:'.$carriers->id.' Number Contract: '.$carriers->name.'"  title="Delete" >
                    <samp class="la la-trash" style="font-size:20px; color:#031B4E"></samp>
                </a>-->';
            })

            ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file       = $request->file('file');
        $fillbooll  = Storage::disk('carriers')->put($request->image,\File::get($file));
        if($fillbooll){   
            $carrier = new Carrier();
            $carrier->name  = $request->name;
            $carrier->image = $request->image;
            $carrier->save();
        }
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Your carrier was created');
        return redirect()->route('managercarriers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('carriers.Body-Modals.add');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $carrier = Carrier::find($id);
        $image = Storage::disk('carriers')->url($carrier->image);
        //dd($image);
        return view('carriers.Body-Modals.edit',compact('carrier','image'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //dd($request->all());
        $carrier = Carrier::find($id);
        $carrier->name  = $request->name;
        if($request->DatImag){
            Storage::disk('carriers')->delete($carrier->image);
            $file   = $request->file('file');
            Storage::disk('carriers')->put($request->image,\File::get($file));
        }
        $carrier->image = $request->image;
        $carrier->save();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.content', 'Your carrier was updated');
        return redirect()->route('managercarriers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
