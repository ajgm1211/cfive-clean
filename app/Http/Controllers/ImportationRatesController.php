<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
        
        dd($request );
        $validator = \Validator::make($request->all(), [
            'file' => 'required|mimes:xls,xlsx,csv',
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
        \Storage::disk('local')->put($nombre, \File::get($file));
/*
        

        try {
            Excel::load(\Storage::disk('local')->url($nombre), function($reader) {

                /*  foreach ($reader->get() as $book) {

                    Archivo::create([
                        'codigo' => $book->codigo,
                        'nombre' => $book->nombre,
                        'estado' => $book->estado,
                        'municipio' => $book->municipio,
                        'parroquia' => $book->parroquia,
                        'descripcion' => $book->direccion,
                        'codigorf' => $book->cod_rf,
                        'radiobase' => $book->radio_base_donante,
                        'electores' => $book->electores,
                        'mesa' => $book->mesas,
                    ]);
                }*/
          /*  });

            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.contenido', 'El archivo ha sido subido con exito');
            return view('/archivo/crearArchivo');
        } catch (\Illuminate\Database\QueryException $e) {

            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.contenido', 'Se ha producido un error al cargar el archivo');
            return view('/archivo/crearArchivo');
        }
*/
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
