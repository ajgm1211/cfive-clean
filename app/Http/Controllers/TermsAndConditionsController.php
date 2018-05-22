<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\TermAndCondition;
use App\Harbor;

class TermsAndConditionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $terms = TermAndCondition::All();
        $data = $terms->where('user_id', Auth::user()->id);
        $objHarbor = new Harbor;
        $harbor = $objHarbor->all()->pluck('name', 'id');
        $harbor = Harbor::find($data[0]->port);
        

        $var = compact('data', 'harbor');
        //dd($data);
        return view('terms.list', compact('data', 'harbor'));
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
        $harbor = Harbor::all();
        $data = $harbor->pluck('name');
        //dd($data);
        return view('terms.add', ['array' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $term = new TermAndCondition();
        $term->name = $request->name;
        $term->port = $request->id + 1;
        $term->user_id = Auth::user()->id;
        $term->import = $request->import;
        $term->export = $request->export;
        $term->save();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully added new term.');
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
        $term = TermAndCondition::find($id);

        $term->dd();
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
