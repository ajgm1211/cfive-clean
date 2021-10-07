<?php

namespace App\Http\Controllers;

use App\PriceLevel;
use App\Http\Resources\PriceLevelResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PriceLevelController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pricelevel.index');
    }

    function list(Request $request)
    {
        $results = PriceLevel::filterByCurrentCompany()->filter($request);

        return PriceLevelResource::collection($results);
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
        if(isset($request->input('desciption'))){
            $data = $request->validate([
                'description' => 'required',
            ]);

            $price_level->update([
                'description' => $data['description'],
            ]);
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
