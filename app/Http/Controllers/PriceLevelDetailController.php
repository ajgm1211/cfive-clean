<?php

namespace App\Http\Controllers;

use App\PriceLevelDetail;
use App\PriceLevel;
use Illuminate\Http\Request;
use App\Http\Resources\PriceLevelDetailResource;


class PriceLevelDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @param  \App\PriceLevel  $price_level
     */
    public function list(Request $request, PriceLevel $price_level)
    {   
        $results = PriceLevelDetail::filterByPriceLevel($price_level->id)->filter($request);

        return PriceLevelDetailResource::collection($results);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, PriceLevel $price_level)
    {
        $data = $request->validate([
            'amount' => 'required',
            'currency' => 'required',
            'direction' => 'required',
            'price_level_apply' => 'required',
        ]);

        $price_level_detail = PriceLevelDetail::create([
            'amount' => $data['amount'],
            'currency_id' => $data['currency']['id'],
            'direction_id' => $data['direction']['id'],
            'price_level_apply_id' => $data['price_level_apply']['id'],
            'price_level_id' => $price_level->id, 
        ]);

        return new PriceLevelDetailResource($price_level_detail);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PriceLevelDetail  $priceLevelDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PriceLevelDetail $price_level_detail)
    {
        $data = $request->validate([
            'amount' => 'required',
            'currency' => 'required',
            'direction' => 'required',
            'price_level_apply' => 'required',
        ]);

        $price_level_detail->update([
            'amount' => $data['amount'],
            'currency_id' => $data['currency']['id'],
            'direction_id' => $data['direction']['id'],
            'price_level_apply_id' => $data['price_level_apply']['id'],
        ]);

        return new PriceLevelDetailResource($price_level_detail);
    }

    /**
     * Clone the specified resource in storage.
     *
     * @param  \App\PriceLevelDetail  $priceLevel
     * @return \Illuminate\Http\Response
     */
    public function duplicate(PriceLevelDetail $price_level_detail)
    {
        $price_level = $price_level_detail->price_level();

        $new_price_level_detail = $price_level_detail->duplicate($price_level);

        return new PriceLevelDetailResource($new_price_level_detail);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PriceLevelDetail  $priceLevelDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(PriceLevelDetail $price_level_detail)
    {
        $price_level_detail->delete();

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
        $toDestroy = PriceLevelDetail::whereIn('id', $request->input('ids'))->get();

        foreach ($toDestroy as $td) {
            $this->destroy($td);
        }

        return response()->json(null, 204);
    }
}
