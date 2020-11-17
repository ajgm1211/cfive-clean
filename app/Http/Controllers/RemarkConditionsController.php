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
use App\Country;
use App\Http\Requests\StoreRemark;
use App\RemarkCarrier;
use App\RemarkCountry;

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
        $harbors = Harbor::pluck('display_name', 'id');
        $carriers = Carrier::pluck('name', 'id');
        $languages = Language::pluck('name', 'id');
        $countries = Country::pluck('name', 'id');
        return view('remarks.add', compact('harbors', 'carriers', 'languages', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRemark $request)
    {
        $request->request->add(['user_id' => Auth::user()->id, 'company_user_id' => Auth::user()->company_user_id]);

        $remark = RemarkCondition::create($request->all());

        $ports = $request->ports;
        $carriers = $request->carriers;
        $countries = $request->countries;

        if (count($ports) >= 1) {
            $this->storeRelationships($ports, $remark->id, 'port');
        }

        if (count($carriers) >= 1) {
            $this->storeRelationships($carriers, $remark->id, 'carrier');
        }

        if (count($countries) >= 1) {
            $this->storeRelationships($countries, $remark->id, 'country');
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
        $selected_countries  = collect(@$remark->remarksCountries);
        $selected_countries  = $selected_countries->pluck('id', 'name');

        $harbors = harbor::pluck('display_name', 'id');
        $carriers = Carrier::pluck('name', 'id');
        $countries = Country::pluck('name', 'id');

        return view('remarks.show', compact('remark', 'countries', 'harbors', 'carriers', 'languages', 'selected_countries', 'selected_harbors', 'selected_carriers'));
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
        $harbors = harbor::pluck('display_name', 'id');
        $selected_carriers  = collect(@$remark->remarksCarriers);
        $selected_carriers  = $selected_carriers->pluck('id', 'name');
        $selected_countries  = collect(@$remark->remarksCountries);
        $selected_countries  = $selected_countries->pluck('id', 'name');
        $carriers = Carrier::pluck('name', 'id');
        $countries = Country::pluck('name', 'id');
        return view('remarks.edit', compact('remark', 'countries', 'harbors', 'selected_countries', 'selected_harbors', 'languages', 'carriers', 'selected_carriers'));
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

        $request->request->add(['user_id' => Auth::user()->id, 'company_user_id' => Auth::user()->company_user_id]);

        $remark = RemarkCondition::findOrFail($id)->update($request->all());
        
        $ports = $request->ports;
        $carriers = $request->carriers;
        $countries = $request->countries;

        if (count($ports) >= 1) {
            RemarkHarbor::where('remark_condition_id', $id)->delete();
            $this->storeRelationships($ports, $id, 'port');
        }

        if (count($carriers) >= 1) {
            RemarkCarrier::where('remark_condition_id', $id)->delete();
            $this->storeRelationships($carriers, $id, 'carrier');
        }

        if (count($countries) >= 1) {
            RemarkCountry::where('remark_condition_id', $id)->delete();
            $this->storeRelationships($countries, $id, 'country');
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Record updated successfully!');

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
        $request->session()->flash('message.content', 'Record ' . $remark->name . ' has been deleted!');
        return redirect()->route('remarks.list');
    }

    public function destroymsg($id)
    {
        return view('remarks/message', ['id' => $id]);
    }


    /**
     * storeRelationships
     *
     * @param  mixed $values
     * @param  mixed $remark
     * @param  mixed $type
     * @return void
     */
    public function storeRelationships($values, $id, $type)
    {
        switch ($type) {
            case 'carrier':
                foreach ($values as $value) {
                    RemarkCarrier::create([
                        'carrier_id' => $value,
                        'remark_condition_id'  => $id
                    ]);
                }
                break;
            case 'port':
                foreach ($values as $value) {
                    RemarkHarbor::create([
                        'port_id' => $value,
                        'remark_condition_id'  => $id
                    ]);
                }
                break;
            case 'country':
                foreach ($values as $value) {
                    RemarkCountry::create([
                        'country_id' => $value,
                        'remark_condition_id'  => $id
                    ]);
                }
                break;
        }
    }
}
