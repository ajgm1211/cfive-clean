<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurcharge;
use App\SaleTerm;
use App\Surcharge;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Datatables;
use App\Http\Traits\EntityTrait;

class SurchargesController extends Controller
{
    use EntityTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $is_admin = false;
        if (Auth::user()->hasRole(['administrator', 'data_entry'])) {
            $is_admin = true;
            //$data = Surcharge::where('company_user_id','=',Auth::user()->company_user_id)->orWhere('company_user_id',null)->with('companyUser')->get();
        } else {
            $data = Surcharge::where('company_user_id', '=', Auth::user()->company_user_id)->with('companyUser')->get();
        }
        $saleterms = SaleTerm::where('company_user_id', '=', Auth::user()->company_user_id)->get();
        if ($is_admin) {
            return view('surcharges/indexAdmin');
        } else {
            return view('surcharges/index', ['surcharges' => $data, 'saleterms' => $saleterms]);
        }
    }

    public function loadDatatables(Request $request, $identofocador)
    {
        if ($identofocador == 1) {
            //$surchargers = Surcharge::where('company_user_id','=',Auth::user()->company_user_id)->orWhere('company_user_id',null)->with('companyUser')->get();
            $data_collection = DB::select('call surcharge_list_proc(' . Auth::user()->company_user_id . ')');
            $data_collection = collect($data_collection);
            return Datatables::of($data_collection)
                ->addColumn('action', function ($data_collection) {
                    $buttons = '<a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  onclick="AbrirModal(\'edit\',' . $data_collection->id . ')" title="Edit "><i class="la la-edit"></i></a>
                    <a href="#" id="delete-surcharge" data-surcharge-id="' . $data_collection->id . '" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete" ><i class="la la-eraser"></i></a>';
                    return $buttons;
                })->make();
        } elseif ($identofocador == 2) {
            $saleterms = SaleTerm::where('company_user_id', '=', Auth::user()->company_user_id)->get();
            return Datatables::of($saleterms)
                ->addColumn('action', function ($saleterms) {
                    $buttons = '<a href="#" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  onclick="AbrirModalSaleTerm(\'edit\',' . $saleterms->id . ')" title="Edit "><i class="la la-edit"></i></a>
                    <button id="delete-saleterm" data-saleterm-id="' . $saleterms->id . '" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Delete "><i class="la la-eraser"></i>                               </button>';
                    return $buttons;
                })->make();
        }
    }
    public function add()
    {
        $is_admin = false;
        $sale_terms = SaleTerm::where('company_user_id', '=', Auth::user()->company_user_id)->pluck('name', 'id');
        if (Auth::user()->hasRole(['administrator', 'data_entry'])) {
            $is_admin = true;
        }
        $decodejosn = [];

        return view('surcharges/add', compact('sale_terms', 'is_admin', 'decodejosn'));
    }

    public function create()
    {
        //
    }

    public function store(StoreSurcharge $request)
    {
        // dd($request->all());
        $request->validated();

        $surcharge = new Surcharge();
        $surcharge->name = $request->name;
        $surcharge->description = $request->description;
        $surcharge->sale_term_id = $request->sale_term_id;
        $surcharge->variation = strtolower(json_encode(['type' => $request->variation]));
        $surcharge->options => json_encode(['is_api' => false]);
        if (!Auth::user()->hasRole(['administrator', 'data_entry'])) {
            $surcharge->company_user_id = Auth::user()->company_user_id;
        }

        if ($request->key_name && $request->key_value) {
            $options_array = [];

            $options_key = $this->processArray($request->key_name);
            $options_value = $this->processArray($request->key_value);

            $options_array = json_encode(array_combine($options_key, $options_value));

            $surcharge->options = $options_array;
        }elseif($request->options){
            $surcharge->options = $request->options;
        }

        $surcharge->save();

        if ($request->ajax()) {
            return $surcharge;
        }

        return redirect()->action('SurchargesController@index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $surcharges = Surcharge::find($id);
        $decodejosn = json_decode($surcharges->variation, true);
        $decodejosn = $decodejosn['type'];
        $sale_terms = SaleTerm::where('company_user_id', '=', Auth::user()->company_user_id)->pluck('name', 'id');
        if (Auth::user()->hasRole(['administrator', 'data_entry'])) {
            $is_admin = true;
        }

        //dd($surcharges,$decodejosn,$decodejosn,$sale_terms,$is_admin);
        return view('surcharges.edit', compact('surcharges', 'decodejosn', 'is_admin', 'sale_terms'));
    }

    public function update(Request $request, $id)
    {
        $requestForm = $request->all();
        $surcharges = Surcharge::find($id);
        $surcharges->name = $request->name;
        $surcharges->description = $request->description;
        $surcharges->sale_term_id = $request->sale_term_id;
        $surcharges->variation = strtolower(json_encode(['type' => $request->variation]));

        if ($request->key_name && $request->key_value) {
            $options_array = [];

            $options_key = $this->processArray($request->key_name);
            $options_value = $this->processArray($request->key_value);

            $options_array = json_encode(array_combine($options_key, $options_value));

            $surcharges->options = $options_array;
        }

        $surcharges->update();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Record updated successfully');

        return redirect()->action('SurchargesController@index');
    }

    public function destroy($id)
    {
        try {
            $surcharge = Surcharge::find($id);
            $surcharge->delete();

            return response()->json(['message' => 'Ok']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e]);
        }
    }

    public function destroySubcharge(Request $request, $id)
    {
        try {
            $user = self::destroy($id);
            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'Record deleted successfully');

            return redirect()->action('SurchargesController@index');
        } catch (\Illuminate\Database\QueryException $e) {
            $request->session()->flash('message.nivel', 'warning');
            $request->session()->flash('message.title', 'I\'m Sorry!');
            $request->session()->flash('message.content', 'You can not delete the charge, it belongs to a contract');

            return redirect()->action('SurchargesController@index');
        }
    }

    public function destroymsg($id)
    {
        return view('surcharges/message', ['surcharge_id' => $id]);
    }
}
