<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\EmailTemplate;
use App\CompanyUser;
use App\MergeTag;
use App\Contact;
use App\Company;
use App\QuoteV2;
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

        $templates = EmailTemplate::where('company_user_id',\Auth::user()->company_user_id)->get();

        return view('emails-template.list', compact('templates'));
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

    public function add(){

        $companyUser = CompanyUser::All();
        $company = $companyUser->where('id', Auth::user()->company_user_id)->pluck('name');
        $mergeTag = MergeTag::All();
        $array = $mergeTag->where('user_name', Auth::user()->name);
        //dd($mergeTag);
        $templates = [];
        foreach ($array as $arr)
        {
            $templates[] = [
                'title' => $arr->tag_name,
                'content' => self::createHtmlTag($arr)
            ];
        }

        //dd($templates);
        return view('emails-template.add', compact('templates'));
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
        $template->company_user_id = Auth::user()->company_user_id;
        $template->save();

        return redirect('templates');

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
        $id  = obtenerRouteKey($id);
        $template = EmailTemplate::find($id);
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

        return view('emails-template.edit', compact('template', 'templates'));
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
        return redirect()->route('templates.index');
    }

    /**
     * Preview template
     *
     * @param  $request
     * @return \Illuminate\Http\Response
     */
    public function preview(Request $request)
    {
        //$template = EmailTemplate::find(2);
        $template = EmailTemplate::find($request->id);
        $data             = $request->data;
        //$data             = '{"quote_bool":"false","company_id":2,"contact_id":2,"quote_id":""}';
        $quote            = '';
        $quote_id         = '';
        $contact_id       = '';
        $company_user_id  = '';

        $data = json_decode($data);

        if($data->quote_bool == 'true'){
            $quote         = QuoteV2::find($data->quote_id);
            $quote_id      = $quote->id;
            $contact_id    = $quote->contact_id;
            $company_id    = $quote->company_id;
        } else {
            $quote_id      = '<label style="color:red;">¿ID?</label>';
            $company_id    = $data->company_id;
            $contact_id    = $data->contact_id;
        }

        if($company_id){
            $company = Company::find($company_id);
        }
        if($contact_id){
            $contact = Contact::find($contact_id);          
        }

        $body = str_replace('{First Name}',@$contact->first_name,$template->menssage);
        $body = str_replace('{Last Name}',@$contact->last_name,$body);
        $body = str_replace('{Company Name}',@$company->business_name,$body);

        return response()->json(['id'=>$template->id,'subject'=>$template->subject,'message'=>$body]);
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
        return redirect()->route('templates.index');

    }

    public function destroymsg($id)
    {
        return view('emails-template.message' ,['id' => $id]);

    }
}
