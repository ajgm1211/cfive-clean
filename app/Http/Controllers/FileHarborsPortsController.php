<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Country;
use App\Harbor;
use App\Harbor_copy;
use Illuminate\Support\Facades\Auth;
use Excel;
use Illuminate\Support\Facades\Log;


class FileHarborsPortsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return  view('contracts.UploadFile');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       $impr = Harbor_copy::where('varation', 'Abu Dhabi City')
                ->get();
       // $impr = Harbor_copy::all();

        dd($impr);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // try {
        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());

        $validator = \Validator::make(
            array('ext' => $ext),
            array('ext' => 'in:xls,xlsx,csv')
        );

        if ($validator->fails()) {
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'just archive with extension xlsx xls csv');
            return redirect()->route('UploadFile.index');
        }

        //obtenemos el nombre del archivo
        $nombre = $file->getClientOriginalName();


        $dd = \Storage::disk('UpLoadFile')->put($nombre,\File::get($file));
        //dd(\Storage::disk('UpLoadFile')->url($nombre));

        $errors=0;
        Excel::selectSheetsByIndex(0)->Load(\Storage::disk('UpLoadFile')->url($nombre),function($reader) use($errors,$request) {

            if($reader->get()->isEmpty() != true){
            } else{
                $request->session()->flash('message.nivel', 'danger');
                $request->session()->flash('message.content', 'The file is it empty');
                return redirect()->route('UploadFile.index');   
            }

            $country        = 'country';
            $portName       = 'port_name';
            $codeport       = 'uencode';
            $location       = 'location';
            $uencode2       = 'uencode2';
            $PNameV1        = 'port_name_variation_1';
            $PNameMSC       = 'msc_rates';
            $PNameMaersk    = 'maerks_rates';
            $PNameCosco     = 'cosco_rates';
            $PNamePorWPT    = 'port_with_pt';
            $PNameNamWPT    = 'name_with_port';
            $PNameNamWCi    = 'name_with_city';

            $i =0;
            $f =0;
            foreach ($reader->get() as $book) {
                $countryExist = Country::where('name','=',$book->country)->first();
                $i++;
                $type = array(
                    $book->$uencode2,
                    $book->$PNameV1,
                    $book->$PNameMSC,
                    $book->$PNameMaersk,
                    $book->$PNameCosco,
                    $book->$PNamePorWPT,
                    $book->$PNameNamWPT,
                    $book->$PNameNamWCi
                );
                $json = json_encode($type);
                
                if(empty($countryExist['id']) != true){   
                    $f++;
                    $prueba = Harbor_copy::create([
                        'name'          => $book->$portName, 
                        'code'          => $book->$codeport,
                        'coordinates'   => $book->$location,
                        'country_id'    => $countryExist['id'],
                        'varation'      => $json
                    ]);
                    dd($prueba);
                }
            }
            //dd($i.' '.$f);
        });
        /* }catch(\Exception $e){
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.content', 'Alert, Error of code');
            return redirect()->route('UploadFile.index');
        }*/

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
