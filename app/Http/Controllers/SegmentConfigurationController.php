<?php

namespace App\Http\Controllers;

use DB;
use App\QuoteSegmentType;
use Illuminate\Http\Request;
use App\CompanyUserQuoteSegment;
use App\Http\Resources\CompanyUserQuoteSegmentResource;
use App\Http\Resources\QuoteSegmentTypeResource;

class SegmentConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('settings.configuration_segments.index');
    }

    /**
     * Show all data related to company_user_id for a resource.
     *
     * @return \Illuminate\Http\Response
     */
    
     public function data(Request $request)
    {
        $user = \Auth::user();
        //Querying each model used and mapping only necessary data
        $company_user_id = $user->company_user_id;

        $setting_segments = CompanyUserQuoteSegment::where('company_user_id', $company_user_id)->get();

        //Collecting all data retrieved
        $data = compact(
            'setting_segments'
        );

        return response()->json(['data' => $data]);
    }

    public function list(Request $request)
    {
        $results = CompanyUserQuoteSegment::filterByCurrentCompany()->with('quoteSegmentType')->orderBy('id', 'desc')->filter($request);

        return CompanyUserQuoteSegmentResource::collection($results);
    }

    public function types(Request $request)
    {
        $resultsQuoteSegmentType = QuoteSegmentType::whereNotIn('id', $request->all())->get();

        return QuoteSegmentTypeResource::collection($resultsQuoteSegmentType);
    }
    
    public function retrieve(Request $request, CompanyUserQuoteSegment $companyUserQuoteSegment)
    {
        return new CompanyUserQuoteSegmentResource($companyUserQuoteSegment);
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
        $data = $request->validate([
            'segment_id' => 'required',
            'quote_segment_type_id' => 'required',
        ]);

        $companyUserQuoteSegment = CompanyUserQuoteSegment::create(array_merge($request->all(), ['company_user_id' => \Auth::user()->company_user_id]));

        return new CompanyUserQuoteSegmentResource($companyUserQuoteSegment);
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompanyUserQuoteSegment $companyUserQuoteSegment)
    {
        $companyUserSegmentForUpdate = $request->all();
        $results = array();
        try {
            DB::beginTransaction();
                if ($companyUserQuoteSegment) {
                    
                    foreach ($companyUserSegmentForUpdate['segments'] as $key => $value) {
                        $updatedCompanyUserQuoteSegment = CompanyUserQuoteSegment::find($value['id']);
                        $updatedCompanyUserQuoteSegmentResult = $updatedCompanyUserQuoteSegment->fill($value);
                        $results += [$key => new CompanyUserQuoteSegmentResource($updatedCompanyUserQuoteSegmentResult)]; 
                        $updatedCompanyUserQuoteSegment->save();
                    }
                }
            DB::commit();
            $collection_result =  collect($results);
            return CompanyUserQuoteSegmentResource::collection($collection_result);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyUserQuoteSegment $companyUserQuoteSegment)
    {
        $companyUserQuoteSegment->delete();

        return response()->json(['message' => 'Ok']);
    }

    public function destroyAll(Request $request)
    {
        $toDestroy = CompanyUserQuoteSegment::whereIn('id', $request->input('ids'))->get();
        foreach ($toDestroy as $td) {
            $this->destroy($td);
        }

        return response()->json(null, 204);
    }
}
