<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inland;
use App\GroupContainer;
use App\Direction;
use App\Company;
use App\Currency;
use App\Http\Resources\InlandResource;
use App\Container;
use App\InlandType;
use App\Harbor;
use App\InlandPort;
use App\Provider;
use Illuminate\Support\Facades\DB;

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
        $company_user_id = \Auth::user()->company_user_id;

        $equipments = GroupContainer::get()->map(function ($equipment) {
            return $equipment->only(['id', 'name']);
        });

        $directions = Direction::get()->map(function ($direction) {
            return $direction->only(['id', 'name']);
        });

        $harbors = Harbor::get()->map(function ($harbor) {
            return $harbor->only(['id', 'display_name']);
        });

        $currencies = Currency::get()->map(function ($currency) {
            return $currency->only(['id', 'alphacode']);
        });

        $types = InlandType::where('id', 1)->get()->map(function ($type) {
            return $type->only(['id', 'name']);
        });

        $companies = Company::where('company_user_id', '=', $company_user_id)->get()->map(function ($company) {
            return $company->only(['id', 'business_name']);
        });
        
        $providers = Provider::where('company_user_id', '=', $company_user_id)->get()->map(function ($providers) {
            return $providers->only(['id', 'name']);
        });

        $containers = Container::get();

        $data = [
            'equipments' => $equipments,
            'directions' => $directions,
            'types' => $types,
            'containers' => $containers,
            'currencies' => $currencies,
            'companies' => $companies,
            'harbors' => $harbors,
            'providers' => $providers,
        ];


        return response()->json(['data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $company_user_id = \Auth::user()->company_user_id;

        $data = $request->validate([
            'reference' => 'required',
            'type' => 'required',
            'direction' => 'required',
            'validity' => 'required',
            'expire' => 'required',
            'gp_container' => 'required',
            'ports' => 'required',
            'providers' => 'required',
            
        ]);
        

        $inland = Inland::create([
            'provider' => $data['reference'],
            'company_user_id' => $company_user_id,
            'type' => $data['type'],
            'direction_id' => $data['direction'],
            'validity' => $data['validity'],
            'expire' => $data['expire'],
            'status' => 'publish',
            'inland_type_id' => '1',
            'gp_container_id' => $data['gp_container'],
            'provider_id' => $data ['providers']
        ]);

        $inland->InlandPortsSync($data['ports']);

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
        $data = $request->validate([
            'reference' => 'required',
            'type' => 'required',
            'validity' => 'required',
            'expire' => 'required',
            'direction' => 'required',
            'gp_container' => 'required',
            'restrictions' => 'sometimes',
            'ports' => 'required',
            'providers' => 'required',
            
        ]);
        
        $status= $this->updateStatus($data['expire']);

        $inland->update([
            'provider' => $data['reference'],
            'direction_id' => $data['direction'],
            'validity' => $data['validity'],
            'expire' => $data['expire'],
            'status' => $status,
            'inland_type_id' => $data['type'],
            'gp_container_id' => $data['gp_container'],
            'provider_id' => $data ['providers']
        ]);

        $inland->InlandPortsSync($data['ports']);

        $inland->InlandRestrictionsSync($data['restrictions'] ?? []);

        return new InlandResource($inland);
    }

        public function updateStatus($data){
    
            $date= date('Y-m-d');
            $expire=date('Y-m-d', strtotime($data));
                    
            if($expire<=$date){
                $status='expired';
            }else{
                $status='publish';
            } 
                    
            return $status;
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Inland  $inland
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inland $inland)
    {
        $inland->delete();

        return response()->json(null, 204);
    }

    /**
     * Duplicate the specified resource.
     *
     * @param  \App\Inland  $inland
     * @return \Illuminate\Http\Response
     */
    public function duplicate(Inland $inland)
    {
        $new_inland = $inland->duplicate();

        return new InlandResource($new_inland);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Inland  $inland
     * @return \Illuminate\Http\Response
     */
    public function retrieve(Inland $inland)
    {
        return new InlandResource($inland);
    }

    /**
     * Remove all the resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(Request $request)
    {
        DB::table('inlands')->whereIn('id', $request->input('ids'))->delete();

        return response()->json(null, 204);
    }
}
