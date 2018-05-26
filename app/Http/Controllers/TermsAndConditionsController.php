<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\TermAndCondition;
use App\Harbor;
use App\TermsPort;

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
        
        $tabla = Harbor::All();
        $terms_port = TermsPort::All();
        $aux = '';
        for($i = 0; $i < sizeof($data); $i++){
            $var = $terms_port->where('term_id', $data[$i]->id)->pluck('port_id');
            for($j = 0; $j < sizeof($var); $j++){
                $data[$i]->user_id = $aux . trim($tabla->where('id', $var[$j])->pluck('name'), "[..]");
                $aux = $data[$i]->user_id;
            }
            $aux = '';
        }

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
        $term->user_id = Auth::user()->id;
        $term->import = $request->import;
        $term->export = $request->export;
        $term->save();
        
        $ports = $request->ports;
        
        for($i = 0; $i < sizeof($ports); $i++){
            $termsport = new TermsPort();
            $termsport->port_id = $ports[$i] + 1;
            $termsport->term()->associate($term);
            $termsport->save();
        }
        
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
        $table_terms_port = TermsPort::All();
        $harbor = Harbor::All();
        $termsport = $table_terms_port->where('term_id', $term->id)->pluck('port_id');
        $cnt = 0;
        foreach($termsport as $tp){
            $ports[$cnt++] = $harbor->where('id', $tp)->pluck('name');
        }
        
        $harbor = Harbor::all();
        $array = $harbor->pluck('name');
        
        //dd($ports);

        return view('terms.edit', compact('array', 'term', 'ports'));
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
        $term = TermAndCondition::find($id);
        $termsPort = TermsPort::All();
        $ports = $termsPort->where('term_id', $id)->pluck('port_id')->toArray();
        $newPorts = $requestForm['ports'];
        $nps = [];
        
        foreach($newPorts as $np){
            array_push($nps, $np + 1);     
        }

        $var = array_diff($ports, $nps);
        //dd($var);

        
        $term->name = $requestForm['name'];
        $term->import = $requestForm['import'];
        $term->export = $requestForm['export'];
        //dd($term);
        $term->save();
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You upgrade has been success ');
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
        $term = TermAndCondition::find($id);
        $term->delete();
        return $term;
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
