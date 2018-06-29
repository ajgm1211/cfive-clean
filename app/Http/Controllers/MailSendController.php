<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\EmailTemplate;
use App\User;
use App\CompanyUser;
use App\MergeTag;

class MailSendController extends Controller
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
        $template = EmailTemplate::All();
        $data = $template->where('company', $company);
        
        foreach($data as $i){
            $user = User::find($i->user_id);    
            $i->user_id = $user->name;
        }

        return view('mail.list', compact('data'));
    }

    public function createHtmlTag($data){

        $tag ='<!DOCTYPE html>
            <html>
                <head>
                </head>
                <body>
                    <p>
                        <strong>Nombre:</strong> '.$data->client_name.'<br/>'.
                        '<strong>Company:</strong> '.$data->company_name.'<br/>'.
                        '<strong>Quote Number:</strong> '.$data->quote_number.'<br/>'.
                        '<strong>Origin:</strong> '.$data->origin.'<br/>'.
                        '<strong>Destination:</strong> '.$data->destination.'<br/>'.
                        '<strong>Total:</strong> '.$data->quote_total.'<br/>'.
                    '</p>
                </body>
            </html>';

        return $tag;
    }

    public function send($id){

        $mail = EmailTemplate::find($id);
        $mergeTag = MergeTag::All();
        $array = $mergeTag->where('user_name', Auth::user()->name);

        $templates = [];
        foreach ($array as $arr)
        {
            $templates[] = [
                'title' => $arr->tag_name,
                'content' => self::createHtmlTag($arr)
            ];
        }

        return view('mail.toSend', compact('mail', 'templates'));
    }

    public function ready(Request $request, $id){
        
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
        //
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
        //
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
        $to = $request->to;
        $subj = $request->subjct;
        $body = $request->menssage;

        Mail::send([], [], function ($message) use ($body, $to, $subj) {
            $message->to($to)
              ->subject($subj)
              ->setBody($body, 'text/html');
          });
        
        
          $request->session()->flash('message.nivel', 'success');
          $request->session()->flash('message.title', 'Well done!');
          $request->session()->flash('message.content', 'Your mail has been send');
          return redirect()->route('mail.list');
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
