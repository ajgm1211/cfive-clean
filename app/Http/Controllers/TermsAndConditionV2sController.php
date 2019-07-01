<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\TermAndCondition;
use App\TermAndConditionV2;
use App\Harbor;
use App\Carrier;
use App\Language;
use App\TermsPort;
use App\CompanyUser;
use App\TermConditionCarrier;

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

    $languages = Language::pluck('name','id');
    return view('termsv2.add', compact('harbors','carriers','languages'));
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
      $companyUser        = CompanyUser::All();
      $company           = Auth::user()->company_user_id;
      $term                   = new TermAndConditionV2();
      $term->name             = $request->name;
      $term->type           =$request->type;
      $term->user_id          = Auth::user()->id;
      $term->import           = $request->import;
      $term->export           = $request->export;
      $term->company_user_id  = $company;
      $term->language_id      = $request->language;
      $term->save();
      /*
      $ports = $request->ports;
      $carriers = $request->carriers;
      if(count($ports) >= 1){
        foreach($ports as $i){
          $termsport = new TermsPort();
          $termsport->port_id = $i;
          $termsport->term()->associate($term);
          $termsport->save();
        }
      }
      if(count($carriers) >= 1){
        foreach($carriers as $carrier){
          TermConditionCarrier::create([
            'carrier_id'        => $carrier,
            'termcondition_id'  => $term->id
          ]);
        }
      }*/

      $request->session()->flash('message.nivel', 'success');
      $request->session()->flash('message.title', 'Well done!');
      $request->session()->flash('message.content', 'Register completed successfully');
    }else{
      $request->session()->flash('message.nivel', 'danger');
      $request->session()->flash('message.title', 'Error!');
      $request->session()->flash('message.content', 'You must add terms to import or export');
    }
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
    $term = TermAndConditionV2::where('id',$id)->with('language')->first();

    $languages = Language::pluck('name','id');
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
    $term = TermAndConditionV2::where('id',$id)->with('language')->first();
    $languages = Language::pluck('name','id');
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
  public function update(Request $request, $id)
  {
    if($request->import=='' || $request->export==''){
      $request->session()->flash('message.nivel', 'danger');
      $request->session()->flash('message.title', 'Error!');
      $request->session()->flash('message.content', 'You must add terms to import or export');
    }else{
      $term = TermAndConditionV2::find($id);
      $term->name         = $request->name;
      $term->user_id      = Auth::user()->id;
      $term->import       = $request->import;
      $term->export       = $request->export;
      $term->type       = $request->type;
      $term->language_id  = $request->language;
      $term->company_user_id = Auth::user()->company_user_id;
      $term->update();

      /*$ports = $request->ports;
      if(count($ports) >= 1){
        TermsPort::where('term_id',$id)->delete();

        foreach($ports as $i){
          $termsport = new TermsPort();
          $termsport->port_id = $i;
          $termsport->term()->associate($term);
          $termsport->save();
        }
      }

      $carriers = $request->carriers;

      TermConditionCarrier::where('termcondition_id',$id)->delete();
      if(count($carriers) >= 1){
        foreach($carriers as $carrier){
          TermConditionCarrier::create([
            'carrier_id'        => $carrier,
            'termcondition_id'  => $term->id
          ]);
        }
      }*/

      $request->session()->flash('message.nivel', 'success');
      $request->session()->flash('message.title', 'Well done!');
      $request->session()->flash('message.content', 'You upgrade has been success ');
    }
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
    return redirect()->route('termsv2.list');

  }

  public function destroymsg($id)
  {
    return view('termsv2/message' ,['id' => $id]);

  }

}
