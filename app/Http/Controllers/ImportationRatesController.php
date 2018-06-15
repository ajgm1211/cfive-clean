<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TmpRate;
use Excel;
use Illuminate\Support\Facades\Log;

class ImportationRatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('contracts.UploadFile');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //dd($request );
        $validator = \Validator::make($request->all(), [
            'file' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('UploadFileRates.index')
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('file');
        //obtenemos el nombre del archivo
        $nombre = $file->getClientOriginalName();

        $dd = \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));
        //dd(\Storage::disk('UpLoadFile')->url($nombre));





        try {
            $res = Excel::selectSheetsByIndex(1)->load(\Storage::disk('UpLoadFile')->url($nombre), function($reader) {
                config(['excel.import.startRow' => 6]);
                //$reader->skipRows(1);
                 foreach ($reader->get() as $book) {
                // The firstname getter will correspond with a cell coordinate set inside the config
                $firstname = $book->Receipt;
                log::info($firstname);
                 }

            });

            /*$res = Excel::selectSheetsByIndex(0)->load(\Storage::disk('UpLoadFile')->url($nombre), function($reader){

                  foreach ($reader->get() as $book) {

                      log::info($book->name);

                     /* if($book->Charge == 'BAS'){
                          log::info($book->Charge);
                      }*/



            /*   TmpRate::create([
                        'PortOrigin'        => $book->    ,
                        'PortDestination'   => $book->    ,
                        'Carrier'           => $book->    ,
                        'Rate20'            => $book->    ,
                        'Rate40'            => $book->    ,
                        'Rate40HC'          => $book->    ,
                        'codigorf'          => $book->    ,
                        'Currency'          => $book->    ,
                    ]);*/
            /*    }
         });*/
            dd($res);

            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.contenido', 'El archivo ha sido subido con exito');
            //return view('/archivo/crearArchivo');
        } catch (\Illuminate\Database\QueryException $e) {

            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.contenido', 'Se ha producido un error al cargar el archivo');
            return view('/archivo/crearArchivo');
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
