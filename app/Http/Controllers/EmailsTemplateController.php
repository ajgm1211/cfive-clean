<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\EmailTemplate;
use App\User;

class EmailsTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $template = EmailTemplate::All();
        $data = $template->where('user_id', Auth::user()->id);

        foreach($data as $i){
            $user = User::find(Auth::user()->id);    
            $i->user_id = $user->name;
        }

        return view('emails-template.list', compact('data'));
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

    public function add(){

        return view('emails-template.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $template = new EmailTemplate();
        $template->name = $request->name;
        $template->subject = $request->subject;
        $template->menssage = $request->menssage;
        $template->user_id = Auth::user()->id;
        $template->save();

        return redirect('mail-templates/list');
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $template = EmailTemplate::find($id);

        return view('emails-template.show', compact('template'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $template = EmailTemplate::find($id);

        return view('emails-template.edit', compact('template'));
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
        $template = EmailTemplate::find($id);
        $template->name = $requestForm['name'];
        $template->subject = $requestForm['subject'];
        $template->menssage = $requestForm['menssage'];
        
        $template->save();
        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You upgrade has been success ');
        return redirect()->route('emails-template.list');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $template = EmailTemplate::find($id);
        $template->delete();
        return $template;
    }

    public function destroyTemplate(Request $request,$id){
        $template = self::destroy($id);

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'You successfully delete : '.$template->name);
        return redirect()->route('emails-template.list');

    }

    public function destroymsg($id)
    {
        return view('emails-template.message' ,['id' => $id]);

    }
}
