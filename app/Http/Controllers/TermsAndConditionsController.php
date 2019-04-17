<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\TermAndCondition;
use App\Harbor;
use App\Carrier;
use App\TermsPort;
use App\CompanyUser;
use App\TermConditionCarrier;

class TermsAndConditionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $companyUser = CompanyUser::All();
        $company = $companyUser->where('id', Auth::user()->company_user_id)->pluck('name');
        $data = TermAndCondition::where('company_user_id', Auth::user()->company_user_id)->get();
        return view('terms.list', compact('data'));
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
        $harbors = Harbor::pluck('name','id');
        $carriers = Carrier::pluck('name','id');
        return view('terms.add', compact('harbors','carriers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->import!='' || $request->export!=''){
            $companyUser = CompanyUser::All();
            $company = Auth::user()->company_user_id;
            $term = new TermAndCondition();
            $term->name = $request->name;
            $term->user_id = Auth::user()->id;
            $term->import = $request->import;
            $term->export = $request->export;
            $term->company_user_id = $company;
            $term->save();

            $ports = $request->ports;
            $carriers = $request->carriers;

            foreach($ports as $i){
                $termsport = new TermsPort();
                $termsport->port_id = $i;
                $termsport->term()->associate($term);
                $termsport->save();
            }

            foreach($carriers as $carrier){
                TermConditionCarrier::create([
                    'carrier_id'        => $carrier,
                    'termcondition_id'  => $term->id
                ]);
            }


            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'Register completed successfully');
        }else{
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.title', 'Error!');
            $request->session()->flash('message.content', 'You must add terms to import or export');
        }
        return redirect('terms/list');
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
        $term = TermAndCondition::where('id',$id)->with('harbor','TermConditioncarriers')->first();
        $selected_harbors   = collect($term->harbor);
        $selected_harbors   = $selected_harbors->pluck('id','name');
        $selected_carriers  = collect($term->TermConditioncarriers);
        $selected_carriers  = $selected_carriers->pluck('carrier_id');
        //dd($selected_carriers);
        $harbors = harbor::all()->pluck('name','id');
        $carriers = Carrier::pluck('name','id');

        return view('terms.show', compact('term', 'harbors','carriers','selected_harbors','selected_carriers'));
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
        $term = TermAndCondition::where('id',$id)->with('harbor','TermConditioncarriers')->first();
        $selected_harbors = collect($term->harbor);
        $selected_harbors = $selected_harbors->pluck('id','name');
        $harbors = harbor::all()->pluck('name','id');
        $selected_carriers  = collect($term->TermConditioncarriers);
        $selected_carriers  = $selected_carriers->pluck('carrier_id');
        $carriers = Carrier::pluck('name','id');
        return view('terms.edit', compact('term', 'harbors', 'selected_harbors','carriers','selected_carriers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($request->import=='' || $request->export==''){
            $request->session()->flash('message.nivel', 'danger');
            $request->session()->flash('message.title', 'Error!');
            $request->session()->flash('message.content', 'You must add terms to import or export');
        }else{
            $term = TermAndCondition::find($id);
            $term->name = $request->name;
            $term->user_id = Auth::user()->id;
            $term->import = $request->import;
            $term->export = $request->export;
            $term->company_user_id = Auth::user()->company_user_id;
            $term->update();

            $ports = $request->ports;
            if($ports != ''){
                TermsPort::where('term_id',$id)->delete();

                foreach($ports as $i){
                    $termsport = new TermsPort();
                    $termsport->port_id = $i;
                    $termsport->term()->associate($term);
                    $termsport->save();
                }
            }
            
            $carriers = $request->carriers;
            if($carriers != ''){
                TermConditionCarrier::where('termcondition_id',$id)->delete();

                foreach($carriers as $carrier){
                TermConditionCarrier::create([
                    'carrier_id'        => $carrier,
                    'termcondition_id'  => $term->id
                ]);
            }
            }

            $request->session()->flash('message.nivel', 'success');
            $request->session()->flash('message.title', 'Well done!');
            $request->session()->flash('message.content', 'You upgrade has been success ');
        }
        return redirect()->route('terms.list');
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
            $term = TermAndCondition::find($id);
            $term->delete();

            return response()->json(['message' => 'Ok']);
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e]);
        }
    }

    public function destroyTerm(Request $request,$id){
        $term = self::destroy($id);

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully delete : '.$term->name);
        return redirect()->route('terms.list');

    }

    public function destroymsg($id)
    {
        return view('terms/message' ,['id' => $id]);

    }
}