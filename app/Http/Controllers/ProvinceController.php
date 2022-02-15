<?php

namespace App\Http\Controllers;

use App\Country;
use App\Province;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProvinceController extends Controller
{
    public function index()
    {
        $data = Province::all();

        return view('provinces/index', compact('data'));
    }

    public function add()
    {
        $country = Country::pluck('name', 'id');

        return view('provinces/add', compact('country'));
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
        $prov = new Province($request->all());
        $prov->save();

        return redirect()->action('ProvinceController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $prov = Province::find($id);
        $country = Country::pluck('name', 'id');

        return view('provinces.edit', compact('prov', 'country'));
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
        $requestForm = $request->all();
        $prov = Province::find($id);
        $prov->update($requestForm);

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You upgrade has been success ');

        return redirect()->action('ProvinceController@index');
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
            $prov = Province::find($id);
            $prov->delete();

            return response()->json(['message' => 'Ok']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e]);
        }
    }
}
