<?php

namespace App\Http\Controllers;

use App\Carrier;
use App\CompanyUser;
use App\Harbor;
use App\Http\Requests\StoreTermsAndConditions;
use App\Language;
use App\TermAndCondition;
use App\TermAndConditionV2;
use App\TermConditionCarrier;
use App\TermsPort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TermsAndConditionV2sController extends Controller
{
    public function index()
    {
        $companyUser = CompanyUser::All();
        $company = $companyUser->where('id', Auth::user()->company_user_id)->pluck('name');
        $data = TermAndConditionV2::where('company_user_id', Auth::user()->company_user_id)->with('language')->get();

        //dd($data);
        return view('termsv2.list', compact('data'));
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

    public function add()
    {
        $languages = Language::pluck('name', 'id');

        return view('termsv2.add', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTermsAndConditions $request)
    {
        $request->request->add(['company_user_id' => Auth::user()->company_user_id, 'user_id' => Auth::user()->id]);

        TermAndConditionV2::create($request->all());

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Record saved successfully');

        return redirect('termsv2/list');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = obtenerRouteKey($id);
        $term = TermAndConditionV2::where('id', $id)->with('language')->first();

        $languages = Language::pluck('name', 'id');
        /*  $selected_harbors   = collect($term->harbor);
        $selected_harbors   = $selected_harbors->pluck('id','name');
        $selected_carriers  = collect($term->TermConditioncarriers);
        $selected_carriers  = $selected_carriers->pluck('carrier_id');*/
        //dd($selected_carriers);

        return view('termsv2.show', compact('term', 'languages'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = obtenerRouteKey($id);
        $term = TermAndConditionV2::where('id', $id)->with('language')->first();
        $languages = Language::pluck('name', 'id');
        /*$selected_harbors = collect($term->harbor);
        $selected_harbors = $selected_harbors->pluck('id','name');
        $harbors = harbor::all()->pluck('name','id');
        $selected_carriers  = collect($term->TermConditioncarriers);
        $selected_carriers  = $selected_carriers->pluck('carrier_id');
        $carriers = Carrier::pluck('name','id');*/

        return view('termsv2.edit', compact('term', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) //simplifcar
    {
        $term = TermAndConditionV2::find($id);
        $term->fill($request->all())->save();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Record updated successfully ');

        return redirect()->route('termsv2.list');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $term = TermAndConditionV2::find($id);
            $term->delete();

            return response()->json(['message' => 'Ok']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e]);
        }
    }

    public function destroyTerm(Request $request, $id)
    {
        $term = self::destroy($id);

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully delete : '.$term->name);

        return redirect()->route('termsv2.list');
    }

    public function destroymsg($id)
    {
        return view('termsv2/message', ['id' => $id]);
    }
}
