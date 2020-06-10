<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection as Collection;
use App\Contact;
use App\Company;
use App\Http\Requests\StoreContact;
use App\Http\Resources\ContactResource;
use App\Http\Traits\EntityTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ContactController extends Controller
{
    use EntityTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company_user_id = \Auth::user()->company_user_id;

        $query = Contact::whereHas('company', function ($query) {
            $query->where('company_user_id', '=', \Auth::user()->company_user_id);
        })->Company();

        if ($request->paginate) {
            $contacts = $query->paginate($request->paginate);
        } else {
            $contacts = $query->take($request->size)->get();
        }

        if (\Auth::user()->hasRole('subuser')) {
            $companies = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($q) {
                $q->where('user_id', \Auth::user()->id);
            })->orwhere('owner', \Auth::user()->id)->pluck('business_name', 'id');
        } else {
            $companies = Company::where('company_user_id', '=', $company_user_id)->pluck('business_name', 'id');
        }

        if ($request->ajax()) {
            return $contacts;
            //return (new ContactResource($contacts));
        }

        return view('contacts/index', ['contacts' => $contacts, 'companies' => $companies]);
    }

    public function add()
    {
        $company_user_id = \Auth::user()->company_user_id;
        if (\Auth::user()->hasRole('subuser')) {
            $companies = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($q) {
                $q->where('user_id', \Auth::user()->id);
            })->orwhere('owner', \Auth::user()->id)->pluck('business_name', 'id');
        } else {
            $companies = Company::where('company_user_id', '=', $company_user_id)->pluck('business_name', 'id');
        }

        return view('contacts.add', ['companies' => $companies]);
    }

    public function addWithModal()
    {
        $company_user_id = \Auth::user()->company_user_id;
        if (\Auth::user()->hasRole('subuser')) {
            $companies = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($q) {
                $q->where('user_id', \Auth::user()->id);
            })->orwhere('owner', \Auth::user()->id)->pluck('business_name', 'id');
        } else {
            $companies = Company::where('company_user_id', '=', $company_user_id)->pluck('business_name', 'id');
        }
        return view('contacts.addwithmodal', ['companies' => $companies]);
    }

    public function addWithModalManualQuote()
    {
        $company_user_id = \Auth::user()->company_user_id;
        if (\Auth::user()->hasRole('subuser')) {
            $companies = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($q) {
                $q->where('user_id', \Auth::user()->id);
            })->orwhere('owner', \Auth::user()->id)->pluck('business_name', 'id');
        } else {
            $companies = Company::where('company_user_id', '=', $company_user_id)->pluck('business_name', 'id');
        }
        return view('contacts.addWithModalManualQuote', ['companies' => $companies]);
    }

    public function addWithModalCompanies($company_id)
    {
        $company_user_id = \Auth::user()->company_user_id;
        if (\Auth::user()->hasRole('subuser')) {
            $companies = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($q) {
                $q->where('user_id', \Auth::user()->id);
            })->orwhere('owner', \Auth::user()->id)->pluck('business_name', 'id');
        } else {
            $companies = Company::where('company_user_id', '=', $company_user_id)->pluck('business_name', 'id');
        }
        return view('contacts.add', ['companies' => $companies, 'company_id' => $company_id]);
    }

    public function store(StoreContact $request)
    {
        $request->validated();

        if ($request->ajax() && $request->options) {

            $data = json_decode($request->options, true);

            if (array_key_exists('external_contact_id', $data)) {
                $contact = Contact::where('options->external_contact_id', $data['external_contact_id'])->first();
                if ($contact) {
                    $contact->fill($request->all())->save();
                    return $contact;
                }
            }

            if (array_key_exists('external_company_id', $data)) {
                $company = Company::where('options->external_company_id', $data['external_company_id'])->first();
                if ($company) {
                    $request->merge(['company_id' => $company->id]);
                }
            }

            $contact = Contact::create($request->all());

            return $contact;
        }

        $options = null;

        if ($request->key_name && $request->key_value) {
            $options_array = array();

            $options_key = $this->processArray($request->key_name);
            $options_value = $this->processArray($request->key_value);

            $options_array = json_encode(array_combine($options_key, $options_value));
            $options = $options_array;
        }

        $request->request->add(['options' => $options]);

        $contact = Contact::create($request->all());

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register completed successfully!');

        return redirect()->back();
    }

    public function edit($id)
    {
        $contact = Contact::find($id);
        $company_user_id = \Auth::user()->company_user_id;
        if (\Auth::user()->hasRole('subuser')) {
            $companies = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($q) {
                $q->where('user_id', \Auth::user()->id);
            })->orwhere('owner', \Auth::user()->id)->pluck('business_name', 'id');
        } else {
            $companies = Company::where('company_user_id', '=', $company_user_id)->pluck('business_name', 'id');
        }

        return view('contacts.edit', compact('contact', 'companies'));
    }

    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        $options = null;

        if ($request->key_name && $request->key_value) {
            $options_array = array();

            $options_key = $this->processArray($request->key_name);
            $options_value = $this->processArray($request->key_value);

            $options_array = json_encode(array_combine($options_key, $options_value));
            $options = $options_array;
        }

        $request->request->add(['options' => $options]);

        $contact->update($request->all());

        if ($request->ajax()) {
            return response()->json('Contact updated successfully!');
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register updated successfully!');

        return redirect()->route('contacts.index');
    }

    public function show(Request $request, $id)
    {
        $contact = Contact::with(array('company' => function ($query) {
            $query->select('id', 'business_name', 'phone', 'address', 'email', 'tax_number', 'company_user_id', 'owner');
            $query->with(['owner' => function ($q) {
                $q->select('id', 'name', 'lastname', 'email', 'phone', 'position', 'state', 'company_user_id');
            }]);
            $query->with(['company_user' => function ($q) {
                $q->select('id', 'name', 'address', 'phone', 'currency_id');
                $q->with(['currency' => function ($qy) {
                    $qy->select('id', 'name', 'alphacode', 'api_code_eur', 'api_code', 'rates', 'rates_eur');
                }]);
            }]);
        }))->where('id', $id)->firstOrFail();

        if ($request->ajax()) {
            $collection = Collection::make($contact);
            return $collection;
        }

        return view('contacts.show', compact('conact'));
    }

    public function delete($id)
    {
        $contact = Contact::find($id);

        return view('contacts.delete', compact('contact'));
    }

    public function destroy(Request $request, $id)
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->delete();

            if ($request->ajax()) {
                return response()->json('Contact deleted successfully!');
            }

            return response()->json(['message' => 'Ok']);
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Record not found',
                ], 404);
            }

            return response()->json(['message' => $e]);
        }
    }

    public function getContacts()
    {
        $contact = Contact::all()->pluck('first_name', 'id');
        return $contact;
    }

    public function getContactsByCompanyId($id)
    {
        $contact = Contact::where('company_id', $id)->pluck('first_name', 'id');
        return $contact;
    }
}
