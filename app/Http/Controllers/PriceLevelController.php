<?php

namespace App\Http\Controllers;

use App\PriceLevel;
use App\PriceLevelGroup;
use App\Company;
use App\Currency;
use App\Container;
use App\Direction;
use App\CompanyGroup;
use App\CompanyUser;
use App\Http\Resources\PriceLevelResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\SearchTrait;


class PriceLevelController  extends Controller
{
    use SearchTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pricelevel.index');
    }

    //Retrieves all data needed for search processing and displaying
    public function data(Request $request)
    {
        $user = \Auth::user();
        //Querying each model used and mapping only necessary data
        $company_user_id = $user->company_user_id;

        $company_user = CompanyUser::where('id', $company_user_id)->first();

        $companies = Company::where('company_user_id', '=', $company_user_id)->get();
        
        $company_groups = CompanyGroup::where('company_user_id', '=', $company_user_id)->get();

        $currency = Currency::get()->map(function ($curr) {
            return $curr->only(['id', 'alphacode', 'rates', 'rates_eur']);
        });

        $common_currencies = Currency::whereIn('id', ['46', '149'])->get()->map(function ($curr) {
            return $curr->only(['id', 'alphacode', 'rates', 'rates_eur']);
        });

        $containers = Container::all();

        $directions = Direction::all();

        $price_levels = PriceLevel::where('company_user_id', $company_user_id)->get()->map(function ($price) {
            return $price->only(['id', 'name']);
        });

        //Collecting all data retrieved
        $data = compact(
            'user',
            'company_user_id',
            'company_user',
            'companies',
            'company_groups',
            'currency',
            'common_currencies',
            'containers',
            'directions',
            'price_levels',
        );

        return response()->json(['data' => $data]);
    }

    public function list(Request $request)
    {
        $results = PriceLevel::filterByCurrentCompany()->orderBy('id', 'asc')->filter($request);

        return $results;
        // return PriceLevelResource::collection($results);
    }

    
    public function get(Request $request, $id)
    {
        $currentPL = PriceLevel::find($id);

        return $currentPL;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'display_name' => 'required',
            'price_level_type' => 'required',
        ]);

        $company_user_id = \Auth::user()->company_user_id;

        $price_level = PriceLevel::create([
            'name' => $data['name'],
            'display_name' => $data['display_name'],
            'type' => $data['price_level_type'],
            'company_user_id' => $company_user_id,      
        ]);

        return new PriceLevelResource($price_level);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PriceLevel  $priceLevel
     * @return \Illuminate\Http\Response
     */
    public function edit(PriceLevel $price_level)
    {
        return view('pricelevel.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PriceLevel  $priceLevel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PriceLevel $price_level)
    {
        $fields = $request->input();

        if(array_key_exists('description',$fields)){
            $data = $request->validate([
                'description' => 'required',
            ]);

            $price_level->update([
                'description' => $data['description'],
            ]);
        }elseif(array_key_exists('companies',$fields) || array_key_exists('groups',$fields)){
            if(array_key_exists('companies',$fields)){
                $model = 'App\\Company';
                $model_type = 'companies';
            }elseif(array_key_exists('groups',$fields)){
                $model = 'App\\CompanyGroup';
                $model_type = 'groups';
            }

            $model_ids = $this->getIdsFromArray($request->input($model_type));
            $existing_relations = [];

            $price_level_groups = PriceLevelGroup::where([
                ['price_level_id',$price_level->id],
                ['group_type',$model]])
            ->get();

            foreach($price_level_groups as $group){
                if(!in_array($group->group_id,$model_ids)){
                    $group->delete();
                }else{
                    array_push($existing_relations,$group->group_id);
                }
            }
            
            $non_existing_relations = array_diff($model_ids,$existing_relations);

            foreach($non_existing_relations as $new_relation_id){
                if($model_type == 'companies'){
                    $new_model = Company::where('id',$new_relation_id)->first();
                }elseif($model_type == 'groups'){
                    $new_model = CompanyGroup::where('id',$new_relation_id)->first();
                }

                $new_relation = new PriceLevelGroup();
                $new_relation->price_level_id = $price_level->id;
                $new_relation->group()->associate($new_model)->save();
            }
        }else{
            $data = $request->validate([
                'name' => 'required',
                'display_name' => 'required',
                'price_level_type' => 'required',
            ]);

            $price_level->update([
                'name' => $data['name'],
                'display_name' => $data['display_name'],
                'type' => $data['price_level_type'],
            ]);
        }

        return new PriceLevelResource($price_level);
    }

    /**
     * Clone the specified resource in storage.
     *
     * @param  \App\PriceLevel  $priceLevel
     * @return \Illuminate\Http\Response
     */
    public function duplicate(PriceLevel $price_level)
    {
        $new_price_level = $price_level->duplicate();

        return new PriceLevelResource($new_price_level);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PriceLevel  $priceLevel
     * @return \Illuminate\Http\Response
     */
    public function destroy(PriceLevel $price_level)
    {
        $price_level->delete();

        return response()->json(['message' => 'Ok']);
    }

    /**
     * Mass remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(Request $request)
    {
        $toDestroy = PriceLevel::whereIn('id', $request->input('ids'))->get();

        foreach ($toDestroy as $td) {
            $this->destroy($td);
        }

        return response()->json(null, 204);
    }
}
