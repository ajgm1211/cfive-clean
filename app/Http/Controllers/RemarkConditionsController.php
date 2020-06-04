<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\RemarkCondition;
use App\Harbor;
use App\Carrier;
use App\Language;
use App\RemarkHarbor;
use App\CompanyUser;
use App\Http\Requests\StoreRemark;
use App\RemarkCarrier;

class RemarkConditionsController extends Controller
{
    public function index()
    {

        $companyUser = CompanyUser::All();
        $company = $companyUser->where('id', Auth::user()->company_user_id)->pluck('name');
        $data = RemarkCondition::where('company_user_id', Auth::user()->company_user_id)->with('language')->get();


        return view('remarks.list', compact('data'));
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
        $harbors = Harbor::pluck('name', 'id');
        $carriers = Carrier::pluck('name', 'id');
        $languages = Language::pluck('name', 'id');
        return view('remarks.add', compact('harbors', 'carriers', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRemark $request)
    {
        //$request->validated();
        
        $companyUser        = CompanyUser::All();
        $company           = Auth::user()->company_user_id;
        $remark                   = new RemarkCondition();
        $remark->name             = $request->name;
        $remark->user_id          = Auth::user()->id;
        $remark->import           = $request->import;
        $remark->export           = $request->export;
        $remark->company_user_id  = $company;
        $remark->language_id      = $request->language;
        $remark->save();

        $ports = $request->ports;
        $carriers = $request->carriers;
        if (count($ports) >= 1) {
            foreach ($ports as $i) {
                $remarksport = new RemarkHarbor();
                $remarksport->port_id = $i;
                $remarksport->remark()->associate($remark);
                $remarksport->save();
            }
        }
        if (count($carriers) >= 1) {
            foreach ($carriers as $carrier) {
                RemarkCarrier::create([
                    'carrier_id'        => $carrier,
                    'remark_condition_id'  => $remark->id
                ]);
            }
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register completed successfully');

        return redirect('remarks/list');
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
        $remark = RemarkCondition::where('id', $id)->with('remarksHarbors', 'remarksCarriers', 'language')->first();


        $languages = Language::pluck('name', 'id');
        $selected_harbors   = collect($remark->remarksHarbors);
        $selected_harbors   = $selected_harbors->pluck('id', 'name');
        $selected_carriers  = collect($remark->remarksCarriers);
        $selected_carriers  = $selected_carriers->pluck('id', 'name');

        $harbors = harbor::all()->pluck('name', 'id');
        $carriers = Carrier::pluck('name', 'id');

        return view('remarks.show', compact('remark', 'harbors', 'carriers', 'languages', 'selected_harbors', 'selected_carriers'));
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
        $remark = RemarkCondition::where('id', $id)->with('remarksHarbors', 'remarksCarriers', 'language')->first();
        $languages = Language::pluck('name', 'id');
        $selected_harbors = collect(@$remark->remarksHarbors);
        $selected_harbors = $selected_harbors->pluck('id', 'name');
        $harbors = harbor::all()->pluck('name', 'id');
        $selected_carriers  = collect(@$remark->remarksCarriers);
        $selected_carriers  = $selected_carriers->pluck('id', 'name');
        $carriers = Carrier::pluck('name', 'id');
        return view('remarks.edit', compact('remark', 'harbors', 'selected_harbors', 'languages', 'carriers', 'selected_carriers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreRemark $request, $id)
    {
        $request->validated();

        $remark = RemarkCondition::findOrFail($id);
        $remark->name         = $request->name;
        $remark->user_id      = Auth::user()->id;
        $remark->import       = $request->import;
        $remark->export       = $request->export;
        $remark->language_id  = $request->language;
        $remark->company_user_id = Auth::user()->company_user_id;
        $remark->update();

        $ports = $request->ports;
        if (count($ports) >= 1) {
            RemarkHarbor::where('remark_condition_id', $id)->delete();

            foreach ($ports as $i) {
                $remarksport = new RemarkHarbor();
                $remarksport->port_id = $i;
                $remarksport->remark()->associate($remark);
                $remarksport->save();
            }
        }

        $carriers = $request->carriers;

        RemarkCarrier::where('remark_condition_id', $id)->delete();
        if (count($carriers) >= 1) {
            foreach ($carriers as $carrier) {
                RemarkCarrier::create([
                    'carrier_id'        => $carrier,
                    'remark_condition_id'  => $remark->id
                ]);
            }
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You upgrade has been success ');

        return redirect()->route('remarks.list');
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
            $remark = RemarkCondition::find($id);
            $remark->delete();

            return response()->json(['message' => 'Ok']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e]);
        }
    }

    public function destroyTerm(Request $request, $id)
    {
        $remark = self::destroy($id);

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully delete : ' . $remark->name);
        return redirect()->route('remarks.list');
    }

    public function destroymsg($id)
    {
        return view('remarks/message', ['id' => $id]);
    }
}
