<?php

namespace App\Http\Controllers;

use App\CalculationType;
use App\Container;
use App\Currency;
use App\GroupContainer;
use App\Harbor;
use App\Http\Requests\StoreSaleTermRequest;
use App\Http\Resources\SaleTermResource;
use App\SaleTermType;
use App\SaleTermV3;
use App\Surcharge;
use App\SaleTermCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleTermV3Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('saletermsv3.index');
    }

    public function list(Request $request)
    {
        $results = SaleTermV3::filterByCurrentCompany()->filter($request);

        return SaleTermResource::collection($results);
    }

    public function data(Request $request)
    {

        $equipments = GroupContainer::get()->map(function ($equipment) {
            return $equipment->only(['id', 'name']);
        });

        $harbors = Harbor::get()->map(function ($harbor) {
            return $harbor->only(['id', 'display_name']);
        });

        $types = SaleTermType::get()->map(function ($type) {
            return $type->only(['id', 'name']);
        });

        $currencies = Currency::get()->map(function ($currency) {
            return $currency->only(['id', 'alphacode']);
        });

        $calculation_types = CalculationType::get()->map(function ($currency) {
            return $currency->only(['id', 'name']);
        });

        $sale_term_codes = SaleTermCode::filterByCurrentCompany()->orderBy('name','asc')->get()->map(function ($currency) {
            return $currency->only(['id', 'name']);
        });

        $containers = Container::get();

        $data = [
            'types' => $types,
            'harbors' => $harbors,
            'equipments' => $equipments,
            'containers' => $containers,
            'currencies' => $currencies,
            'calculation_types' => $calculation_types,
            'sale_term_codes' => $sale_term_codes,
        ];


        return response()->json(['data' => $data]);
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
    public function store(StoreSaleTermRequest $request)
    {
        $request->request->add(['company_user_id' => \Auth::user()->company_user_id]);

        $sale_term = SaleTermV3::create($request->all() + ['company_user_id' => \Auth::user()->company_user_id]);

        return new SaleTermResource($sale_term);
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
     * Render edit view 
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, SaleTermV3 $saleterm)
    {
        return view('saletermsv3.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\SaleTermV3 $saleterm
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SaleTermV3 $saleterm)
    {
        $data = $request->validate([
            'name' => 'required',
            'type' => 'required',
            'port' => 'required',
            'group_container' => 'required',
        ]);

        $saleterm->update([
            'name' => $data['name'],
            'type_id' => $data['type'],
            'port_id' => $data['port'],
            'group_container_id' => $data['group_container']
        ]);

        return new SaleTermResource($saleterm);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SaleTermV3  $saleterm
     * @return \Illuminate\Http\Response
     */
    public function retrieve(SaleTermV3 $saleterm)
    {
        return new SaleTermResource($saleterm);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SaleTermV3 $saleterm)
    {
        $saleterm->delete();

        return response()->json(null, 204);
    }

    /**
     * Duplicate the specified resource.
     *
     * @param  \App\SaleTermV3  $saleterm
     * @return \Illuminate\Http\Response
     */
    public function duplicate(SaleTermV3 $saleterm)
    {
        $new_saleterm = $saleterm->duplicate();

        return new SaleTermResource($new_saleterm);
    }
    /**
     * Remove all the resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(Request $request)
    {
        DB::table('sale_term_v3s')->whereIn('id', $request->input('ids'))->delete();

        return response()->json(null, 204);
    }
}
