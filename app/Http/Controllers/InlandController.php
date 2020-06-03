<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inland;
use App\Carrier;
use App\GroupContainer;
use App\Direction;
use App\Harbor;
use App\Currency;
use App\CalculationType;
use App\Http\Resources\InlandResource;
use App\Container;
use App\InlandPort;

class InlandController extends Controller
{
    public function index(Request $request)
    {
        return view('inlands.index');
    }

    public function list(Request $request)
    {
        $results = Inland::filterByCurrentCompany()->filter($request);

        return InlandResource::collection($results);
    }

    public function data(Request $request)
    {
        $equipments = GroupContainer::get()->map(function ($equipment) {
            return $equipment->only(['id', 'name']);
        });
        $directions = Direction::get()->map(function ($direction) {
            return $direction->only(['id', 'name']);
        });

        $currencies = Currency::get()->map(function ($currency) {
            return $currency->only(['id', 'alphacode']);
        });

        /* Example */
        $types = [
          [ 'id' => 1, 'name' => "Per KM", 'created_at' => null, 'updated_at' => null ],
          [ 'id' => 2, 'name' => "Per Locations", 'created_at' => null, 'updated_at' => null ]
        ];
        /* Example */

        $ports = Harbor::get()->map(function ($harbor) {
            return $harbor->only(['id', 'name']);
        });

        $containers = Container::get();

        $carriers = Carrier::get()->map(function ($carrier) {
            return $carrier->only(['id', 'name']);
        });

        $calculation_types = CalculationType::get()->map(function ($calculation) {
            return $calculation->only(['id', 'name']);
        });

        $data = [
          'ports' => $ports,
          'equipments' => $equipments,
          'directions' => $directions,
          'types' => $types,
          'containers' => $containers,
          'currencies' => $currencies,
          'carriers' => $carriers,
          'calculation_types' => $calculation_types
        ];


        return response()->json(['data' => $data ]);
    }
  

    public function store(Request $request)
    {






        $company_user_id = \Auth::user('web')->company_user_id;
        $data = $request->validate([
      'provider' => 'required',
      'validity' => 'required',
      'direction' => 'sometimes',
      'expire' => 'required',
      'status' => 'required',
      'type' => 'sometimes',
      'gp_container' => 'required',

  ]);

        $inland = Inland::create([
          'provider' => $data['provider'],
          'company_user_id' => $company_user_id,
          'type' => $data['direction']['id'],
          'validity' => $data['validity'],
          'expire' => $data['expire'],
          'status' => $data['status'],
          'gp_container_id' => $data['gp_container']
          
      ]);

      $ports = $request->input('ports');
      foreach($ports as $p => $value)
      {
        $inlandport = new InlandPort();
        $inlandport->port = $value['id'];
        $inlandport->inland()->associate($inland);
        $inlandport->save();
      }
    
        return new InlandResource($inland);
    }


        /**
     * Render edit view 
     *
     * @param  Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Inland $inland)
    {
        return view('inlands.edit');
    }

        /**
     * Display the specified resource.
     *
     * @param  \App\Contract  $contract
     * @return \Illuminate\Http\Response
     */
    public function retrieve(Inland $inland)
    {
        //$direction = Direction::where('id',$inland->type)->first();
        //$inland->type = $direction;        
        return new InlandResource($inland);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Inland $inland
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inland $inland)
    {
        return new InlandResource($inland);   
    }


    public function groupInlandContainer(Inland $inland)
    {        
        $container = Container::where('gp_container_id',$inland->gp_container_id)->get();
        return $container;
    }
}