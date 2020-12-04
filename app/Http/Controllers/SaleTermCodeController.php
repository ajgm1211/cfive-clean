<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleTermCode;
use App\Http\Resources\SaleTermCodeResource;
use App\SaleTermCode;
use Illuminate\Http\Request;

class SaleTermCodeController extends Controller
{
    function list(Request $request)
    {
        $results = SaleTermCode::filterByCurrentCompany()->filter($request);
        return SaleTermCodeResource::collection($results);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SaleTermCode  $charge
     * @return \Illuminate\Http\Response
     */
    public function retrieve(SaleTermCode $charge)
    {
        return new SaleTermCodeResource($charge);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSaleTermCode $request)
    {
        $code = SaleTermCode::create($request->all() + ['company_user_id' => \Auth::user()->company_user_id]);

        return new SaleTermCodeResource($code);
    }

    /**
     * Duplicate the specified resource.
     *
     * @param  \App\SaleTermCode $saletermcode
     * @return \Illuminate\Http\Response
     */
    public function duplicate(SaleTermCode $code)
    {
        $new_code = $code->duplicate();

        return new SaleTermCodeResource($new_code);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\SaleTermCode $code
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SaleTermCode $code)
    {
        $data = $request->validate([
            'name' => 'required',
        ]);
        
        $code->update([
            'name' => $data['name'],
            'description' => $request->description,
        ]);

        return new SaleTermCodeResource($code);
    }

    /**
     * Remove specific the resource from DB.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        SaleTermCode::find($id)->delete();

        return response()->json(null, 204);
    }
}
