<?php

namespace App\Http\Controllers;

use App\ApiIntegrationSetting;
use App\Company;
use App\CompanyPrice;
use App\Contact;
use App\GroupUserCompany;
use App\Http\Requests\StoreCompany;
use App\Http\Traits\EntityTrait;
use App\Price;
use App\Repositories\CompanyRepositoryInterface;
use App\User;
use App\ViewQuoteV2;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Traits\MixPanelTrait;

class CompanyController extends Controller
{
    use EntityTrait, MixPanelTrait;

    /** @var CompanyRepositoryInterface */
    private $repository;

    public function __construct(CompanyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $company_user_id = \Auth::user()->company_user_id;
        $api = ApiIntegrationSetting::where('company_user_id', \Auth::user()->company_user_id)
            ->whereHas('api_integration', function ($query) {
                $query->where('module', 'Companies');
            })->first();
        $user_id = \Auth::user()->id;
        $users = User::where('company_user_id', \Auth::user()->company_user_id)->where('type', '!=', 'company')->pluck('name', 'id');
        if (\Auth::user()->hasRole('subuser')) {
            $query = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->orwhere('owner', \Auth::user()->id)->with('groupUserCompanies.user')->User()->CompanyUser();
        } else {
            $query = Company::where('company_user_id', \Auth::user()->company_user_id)->with('groupUserCompanies.user')->User()->CompanyUser();
        }

        if ($request->paginate) {
            $companies = $query->paginate($request->paginate);
        } else {
            $companies = $query->take($request->size)->get();
        }

        if ($request->ajax()) {
            $this->trackEvents("api_companies_list", [], "api");
            return $companies;
        }

        return view('companies/index', ['companies' => $companies, 'users' => $users, 'api' => $api]);
    }

    /**
     * LoadDatatableIndex.
     *
     * @return void
     */
    public function LoadDatatableIndex()
    {
        $company_user_id = \Auth::user()->company_user_id;
        $subtype = \Auth::user()->options['subtype'];
        $user_id = \Auth::user()->id;
        
        if($subtype === 'comercial') {
            //Subtype comercial solo pueden acceder a sus propias compa??ias            
            $companies = Company::where('company_user_id', $company_user_id)
                        ->where('owner', $user_id) 
                        ->with('groupUserCompanies.user')->User()->CompanyUser();            
        } else {
            $companies = Company::where('company_user_id', \Auth::user()->company_user_id)->with('groupUserCompanies.user')->User()->CompanyUser();
        }

        $companies = $companies->get();

        $colletions = collect([]);
        $extra_fields = '';
        foreach ($companies as $company) {
            
            $data = [
                'id' => $company->id,
                'idSet' => setearRouteKey($company->id),
                'business_name' => $company->business_name,
                'phone' => $company->phone,
                'email' => $company->email,
                'tax_number' => $company->tax_number,
                'address' => $company->address,
                'extra' => $company->options,
                
            ];
            $colletions->push($data);
        }
        
        return DataTables::of($colletions)
            ->addColumn('extra', function ($colletion) use ($extra_fields) {
                if($colletion['extra']){
                    foreach ($colletion['extra'] as $key=>$item) {
                        $extra_fields .= '<b>'.$key.'</b>: '.$item .'<br>';
                    }
                }
                return $extra_fields!= '' ? '<ul><li>'.$extra_fields.'</li></ul>':'--';
            })
            ->addColumn('action', function ($colletion) {
                return
                    '<a href="companies/' . $colletion['idSet'] . '" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill">
                    <i class="la la-eye"></i>
                </a>
                <button onclick="AbrirModal(\'edit\',' . $colletion['id'] . ')" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit">
                    <i class="la la-edit"></i>
                </button>
                <button id="delete-company" data-company-id="' . $colletion['id'] . '" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Delete">
                    <i class="la la-eraser"></i>
                </button>';
            })->make(true);
    }

    /**
     * add.
     *
     * @return \Illuminate\Http\Response
     */
    public function add()
    {
        $users = User::where('company_user_id', \Auth::user()->company_user_id)->get()->map(function ($user) {
            $user->name = $user->getFullNameAttribute();
            return $user;
        })->pluck('name', 'id');
        $prices = Price::where('company_user_id', \Auth::user()->company_user_id)->pluck('name', 'id');

        return view('companies.add', compact('prices', 'users'));
    }
    
    /**
     * addOwner.
     *
     * @return \Illuminate\Http\Response
     */
    public function addOwner()
    {
        $users = User::where('company_user_id', \Auth::user()->company_user_id)->where('id', '!=', \Auth::user()->id)->where('type', '!=', 'company')->pluck('name', 'id');

        return view('companies.addOwner', compact('users'));
    }

    /**
     * addWithModal.
     *
     * @return \Illuminate\Http\Response
     */
    public function addWithModal()
    {
        $company_user_id = \Auth::user()->company_user_id;
        $prices = Price::where('company_user_id', \Auth::user()->company_user_id)->pluck('name', 'id');
        $user_id = \Auth::user()->id;
        $users = User::where('company_user_id', \Auth::user()->company_user_id)->where('id', '!=', $user_id)->where('type', '!=', 'company')->pluck('name', 'id');
        if (\Auth::user()->hasRole('subuser')) {
            $companies = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })->orwhere('owner', \Auth::user()->id)->with('groupUserCompanies.user', 'user')->get();
        } else {
            $companies = Company::where('company_user_id', \Auth::user()->company_user_id)->with('groupUserCompanies.user', 'user')->get();
        }

        return view('companies.addwithmodal', compact('prices', 'users'));
    }

    public function LoadDatatable($id)
    {
        $company_user_id = \Auth::user()->company_user_id;
        if (\Auth::user()->hasRole('subuser')) {
            $quotes = ViewQuoteV2::where('user_id', \Auth::user()->id)->where('company_id', $id)->orderBy('created_at', 'desc')->get();
        } else {
            $quotes = ViewQuoteV2::where('company_user_id', $company_user_id)->where('company_id', $id)->orderBy('created_at', 'desc')->get();
        }

        $colletions = collect([]);
        foreach ($quotes as $quote) {
            $custom_id = '---';
            $company = '---';
            $origin = '';
            $destination = '';
            $origin_li = '';
            $destination_li = '';

            if (isset($quote->company)) {
                $company = $quote->company->business_name;
            }

            if ($quote->custom_quote_id != '') {
                $id = $quote->custom_quote_id;
            } else {
                $id = $quote->quote_id;
            }

            if ($quote->type == 'AIR') {
                $origin = $quote->origin_airport;
                $destination = $quote->destination_airport;
                $img = '<img src="/images/plane-blue.svg" class="img img-responsive" width="25">';
            } else {
                $origin = $quote->origin_port;
                $destination = $quote->destination_port;
                $img = '<img src="/images/logo-ship-blue.svg" class="img img-responsive" width="25">';
            }

            $explode_orig = explode('| ', $origin);
            $explode_dest = explode('| ', $destination);

            foreach ($explode_orig as $item) {
                $origin_li .= '<li>' . $item . '</li>';
            }

            foreach ($explode_dest as $item) {
                $destination_li .= '<li>' . $item . '</li>';
            }

            if ($quote->business_name != '') {
                $company = $quote->business_name;
            } else {
                $company = '---';
            }

            if ($quote->contact != '') {
                $contact = $quote->contact;
            } else {
                $contact = '---';
            }

            $ValueOrig = count($explode_orig);
            $valueDest = count($explode_dest);

            if ($ValueOrig == 1 && $valueDest == 1) {
                $data = [
                    'id' => $id,
                    'idSet' => setearRouteKey($quote->id),
                    'client' => $company,
                    'contact' => $contact,
                    'user' => $quote->owner,
                    'created' => $quote->created_at,
                    'origin' => $origin_li,
                    'destination' => $destination_li,
                    'type' => $quote->type,
                ];
                $colletions->push($data);
            } elseif ($ValueOrig != 1 && $valueDest == 1) {
                $data = [
                    'id' => $id,
                    'idSet' => setearRouteKey($quote->id),
                    'client' => $company,
                    'contact' => $contact,
                    'user' => $quote->owner,
                    'created' => $quote->created_at,
                    'origin' => '<button class="btn dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      See origins
                                      </button>
                                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="padding:20px;">
                                      <small>' . $origin_li . '</small>
                                      </div>',
                    'destination' => $destination_li,
                    'type' => $quote->type,
                ];
                $colletions->push($data);
            } elseif ($ValueOrig == 1 && $valueDest != 1) {
                $data = [
                    'id' => $id,
                    'idSet' => setearRouteKey($quote->id),
                    'client' => $company,
                    'contact' => $contact,
                    'user' => $quote->owner,
                    'created' => $quote->created_at,
                    'origin' => $origin_li,
                    'destination' => '<button class="btn dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      See destinations
                                      </button>
                                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="padding:20px;">
                                      <small>' . $destination_li . '</small>
                                      </div>',
                    'type' => $quote->type,
                ];
                $colletions->push($data);
            } else {
                $data = [
                    'id' => $id,
                    'idSet' => setearRouteKey($quote->id),
                    'client' => $company,
                    'contact' => $contact,
                    'user' => $quote->owner,
                    'created' => $quote->created_at,
                    'origin' => '<button class="btn dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      See origins
                                      </button>
                                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="padding:20px;">
                                      <small>' . $origin_li . '</small>
                                      </div>',
                    'destination' => '<button class="btn dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      See destinations
                                      </button>
                                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="padding:20px;">
                                      <small>' . $destination_li . '</small>
                                      </div>',
                    'type' => $quote->type,
                ];
                $colletions->push($data);
            }
        }

        return DataTables::of($colletions)
            ->editColumn('created', function ($colletion) {
                return [
                    'display' => e($colletion['created']->format('M d, Y H:i')),
                    'timestamp' => $colletion['created']->timestamp,
                ];
            })
            ->addColumn('action', function ($colletion) {
                return
                    '<button class="btn dropdown-toggle quote-options" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Options
          </button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" >
          <a target="_blank" class="dropdown-item" href="/v2/quotes/show/' . $colletion['idSet'] . '">
          <span>
          <i class="la la-edit"></i>
          &nbsp;
          Edit
          </span>
          </a>
          <a target="_blank" class="dropdown-item" href="/v2/quotes/pdf/' . $colletion['idSet'] . '">
          <span>
          <i class="la la-file"></i>
          &nbsp;
          PDF
          </span>
          </a>
          <a href="/v2/quotes/duplicate/' . $colletion['idSet'] . '" class="dropdown-item" >
          <span>
          <i class="la la-plus"></i>
          &nbsp;
          Duplicate
          </span>
          </a>
          <a href="#" class="dropdown-item" id="delete-quote-v2" data-quote-id="' . $colletion['idSet'] . '" >
          <span>
          <i class="la la-eraser"></i>
          &nbsp;
          Delete
          </span>
          </a>
          </div>';
            })->editColumn('id', '{{$id}}')->make(true);
    }

    /**
     * show.
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $id = obtenerRouteKey($id);
        if ($request->ajax()) {
            $company = Company::with(['owner' => function ($query) {
                $query->select('id', 'name', 'lastname', 'email', 'phone', 'position', 'state', 'company_user_id');
            }, 'company_user' => function ($query) {
                $query->select('id', 'name', 'address', 'phone', 'currency_id');
                $query->with(['currency' => function ($q) {
                    $q->select('id', 'name', 'alphacode', 'api_code_eur', 'api_code', 'rates', 'rates_eur');
                }]);
            }])->where('id', $id)->firstOrFail();

            $collection = Collection::make($company);

            return $collection;
        } else {
            $company = $this->repository->find($id);
        }

        $companies = Company::where('company_user_id', \Auth::user()->company_user_id)->get();
        $company_user_id = \Auth::user()->company_user_id;
        if (\Auth::user()->hasRole('subuser')) {
            $quotes = ViewQuoteV2::where('user_id', \Auth::user()->id)->orderBy('created_at', 'desc')->get();
        } else {
            $quotes = ViewQuoteV2::where('company_user_id', $company_user_id)->orderBy('created_at', 'desc')->get();
        }
        $users = User::where('company_user_id', \Auth::user()->company_user_id)->where('id', '!=', \Auth::user()->id)->where('type', '!=', 'company')->pluck('name', 'id');
        $prices = Price::where('company_user_id', \Auth::user()->company_user_id)->pluck('name', 'id');

        return view('companies.show', compact('company', 'companies', 'contacts', 'quotes', 'users', 'prices'));
    }

    /**
     * store.
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCompany $request)
    {   
        $data = $this->validateData($request);

        //Check if company exists by options field
        if ($request->ajax() && $request->options) {
            $data = json_decode($request->options, true);
            if (array_key_exists('external_company_id', $data)) {
                $company = Company::where('options->external_company_id', $data['external_company_id'])->first();
                if ($company) {
                    $company->fill($request->all())->save();

                    return $company;
                }
            }
        }

        $input = Input::all();
        $file = Input::file('logo');
        $filepath_tmp = null;
        $options = null;

        if ($request->key_name && $request->key_value) {
            $options_array = [];
            $key_names = $request->key_name;
            unset($key_names[1]);
            $key_values = $request->key_value;
            unset($key_values[1]);
            foreach ($key_values as $key => $value) {  
                $key_names[$key] = $key_names[$key] == null ? $key.'_option_empty_name' : $key_names[$key];
                $key_values[$key] = $value == null ? ' ' : $value;
            }

            $options_key = $this->processArray($key_names);
            $options_value = $this->processArray($key_values);

            $options_array = json_encode(array_combine($options_key, $options_value));
        }

        if ($request->ajax()) {
            $options = $request->options;
        } else {
            if (isset($options_array)) {
                $options = $options_array;
            }
        }

        if ($file != '') {
            $filepath_tmp = 'Logos/Clients/' . $file->getClientOriginalName();
        }

        $request->request->add(['company_user_id' => Auth::user()->company_user_id, 'owner' => Auth::user()->id, 'options' => $options, 'logo' => $filepath_tmp]);

        //Save Company
        $company = Company::create($request->all());

        if ($file != '') {
            $this->saveLogo($company, $file);
        }
        if ((isset($input['price_id'])) && (count($input['price_id']) > 0)) {
            $this->saveExtraData($input['price_id'], $company, 'price');
        }
        if ((isset($input['users'])) && (count($input['users']) > 0)) {
            $this->saveExtraData($input['users'], $company, 'users');
        }

        if ($request->ajax()) {
            return $company;
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register completed successfully!');

        return redirect()->route('companies.index');
    }

    /**
     * storeOwner.
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function storeOwner(Request $request)
    {
        $input = Input::all();

        $company = $this->repository->find($input['company_id']);

        if ((isset($input['users'])) && (count($input['users']) > 0)) {
            foreach ($input['users'] as $key => $item) {
                $userCompany_group = new GroupUserCompany();
                $userCompany_group->user_id = $input['users'][$key];
                $userCompany_group->company()->associate($company);
                $userCompany_group->save();
            }
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Owner added successfully!');

        return redirect()->back();
    }

    /**
     * deleteOwner.
     *
     * @param  mixed $request
     * @param  mixed $user_id
     * @return \Illuminate\Http\Response
     */
    public function deleteOwner(Request $request, $user_id)
    {
        $user = GroupUserCompany::where('user_id', $user_id)->delete();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Owner deleted successfully!');

        return redirect()->back();
    }

    /**
     * edit.
     *
     * @param  mixed $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = $this->repository->find($id);

        $users = User::where('company_user_id', \Auth::user()->company_user_id)->where('type', '!=', 'company')->where('id', '!=', $company->owner)->get()->map(function ($user) {
            $user->name = $user->getFullNameAttribute();
            return $user;
        })->pluck('name', 'id');

        $prices = Price::where('company_user_id', \Auth::user()->company_user_id)->pluck('name', 'id');

        return view('companies.edit', compact('company', 'prices', 'users'));
    }

    /**
     * update.
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCompany $request, $id)
    {
        //Form Validation
        $request->validated();

        //Check if company exists by options field
        if ($request->ajax() && $request->options) {
            $data = json_decode($request->options, true);
            if (array_key_exists('external_company_id', $data)) {
                $company = Company::where('options->external_company_id', $data['external_company_id'])->first();
                if ($company) {
                    $company->fill($request->all())->save();

                    return $company;
                }
            }
        }

        $input = Input::all();
        $file = Input::file('logo');
        $options = null;

        if ($request->key_name && $request->key_value) {
            $options_array = [];

            $key_names = $request->key_name;
            unset($key_names[0]);
            $key_values = $request->key_value;
            unset($key_values[0]);
            foreach ($key_values as $key => $value) {  
                $key_names[$key] = $key_names[$key] == null ? $key.'_option_empty_name' : $key_names[$key];
                $key_values[$key] = $value == null ? ' ' : $value;
            }

            $options_key = $this->processArray($key_names);
            $options_value = $this->processArray($key_values);

            $options_array = json_encode(array_combine($options_key, $options_value));
        }

        if ($request->ajax()) {
            $options = $request->options;
        } else {
            if (isset($options_array)) {
                $options = $options_array;
            }
        }

        $company = Company::findOrFail($id);
        $filepath = $company->logo;

        if ($file != '') {
            $filepath = 'Logos/Clients/' . $id . '/' . $file->getClientOriginalName();
        }

        $request->request->add(['options' => $options, 'logo' => $filepath]);

        $company->fill($request->all())->save();

        if ($file != '') {
            $this->saveLogo($company, $file);
        }

        if ((isset($input['price_id'])) && (count($input['price_id']) > 0)) {
            CompanyPrice::where('company_id', $company->id)->delete();
            $this->saveExtraData($input['price_id'], $company, 'price');
        }

        if ((isset($input['users'])) && (count($input['users']) > 0)) {
            GroupUserCompany::where('company_id', $company->id)->delete();
            $this->saveExtraData($input['users'], $company, 'users');
        }

        if ($request->ajax()) {
            return response()->json($company);
        }

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register updated successfully!');

        return redirect()->back();
    }

    /**
     * delete.
     *
     * @param  mixed $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $company = $this->repository->find($id);

        if (count($company->contact) > 0) {
            return response()->json(['message' => count($company->contact)]);
        }

        return response()->json(['message' => 'Ok']);
    }

    /**
     * destroy.
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function destroy(Request $request, $id)
    {   
        try {

            $company = Company::findOrFail($id);
            $company->delete();

            if ($request->ajax()) {
                return response()->json(['message' => 'Company deleted successfully!']);
            }
            // return response()->json(['message' => 'Ok']);
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'message' => 'Record not found',
                ], 404);
            }
            \Log::info("Error company destroy".$e);
            return response()->json(['message' => $e]);            
        }
    }

    /**
     * getCompanyPrice.
     *
     * @param  mixed $id
     * @return void
     */
    public function getCompanyPrice($id)
    {
        $prices = Price::whereHas('company_price', function ($query) use ($id) {
            $query->where('company_id', $id);
        })->pluck('name', 'id');

        return $prices;
    }

    /**
     * getCompanyContact.
     *
     * @param  mixed $id
     * @return void
     */
    public function getCompanyContact($id)
    {
        $contacts = Contact::where('company_id', $id)->pluck('first_name', 'id');

        return $contacts;
    }

    /**
     * updatePaymentConditions.
     *
     * @param  mixed $request
     * @return void
     */
    public function updatePaymentConditions(Request $request)
    {
        $company = Company::findOrfail($request->company_id);
        $company->payment_conditions = $request->payment_conditions;
        $company->update();

        $request->session()->flash('message.nivel', 'success');
        $request->session()->flash('message.title', 'Well done!');
        $request->session()->flash('message.content', 'Register updated successfully!');

        return redirect()->back();
    }

    /**
     * getCompanies.
     *
     * @return void
     */
    public function getCompanies()
    {
        $company_user_id = \Auth::user()->company_user_id;

        if (\Auth::user()->hasRole('subuser')) {
            $companies = Company::where('company_user_id', '=', $company_user_id)->whereHas('groupUserCompanies', function ($q) {
                $q->where('user_id', \Auth::user()->id);
            })->orwhere('owner', \Auth::user()->id)->pluck('business_name', 'id');
        } else {
            $companies = Company::where('company_user_id', '=', $company_user_id)->pluck('business_name', 'id');
        }

        return $companies;
    }

    /**
     * updateName.
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function updateName(Request $request, $id)
    {
        $company = $this->repository->find($id);
        $company->business_name = $request->business_name;
        $company->update();

        return response()->json(['business_name' => $request->business_name]);
    }

    /**
     * updatePhone.
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function updatePhone(Request $request, $id)
    {
        $company = $this->repository->find($id);
        $company->phone = $request->phone;
        $company->update();

        return response()->json(['phone' => $request->phone]);
    }

    /**
     * updateAddress.
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function updateAddress(Request $request, $id)
    {
        $company = $this->repository->find($id);
        $company->address = $request->address;
        $company->update();

        return response()->json(['address' => $request->address]);
    }

    /**
     * updateEmail.
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function updateEmail(Request $request, $id)
    {
        $company = $this->repository->find($id);
        $company->email = $request->email;
        $company->update();

        return response()->json(['address' => $request->email]);
    }

    /**
     * updateTaxNumber.
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function updateTaxNumber(Request $request, $id)
    {
        $company = $this->repository->find($id);
        $company->tax_number = $request->tax_number;
        $company->update();

        return response()->json(['tax_number' => $request->tax_number]);
    }

    /**
     * updatePdfLanguage.
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function updatePdfLanguage(Request $request, $id)
    {
        $company = $this->repository->find($id);
        $company->pdf_language = $request->pdf_language;
        $company->update();

        return response()->json(['pdf_language' => $request->pdf_language]);
    }

    /**
     * updatePriceLevels.
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function updatePriceLevels(Request $request, $id)
    {
        $input = Input::all();

        if ((isset($input['price_id'])) && ($input['price_id'][0] != null)) {
            $company_price = CompanyPrice::where('company_id', $id)->delete();
            foreach ($input['price_id'] as $key => $item) {
                $company_price = new CompanyPrice();
                $company_price->company_id = $id;
                $company_price->price_id = $input['price_id'][$key];
                $company_price->save();
            }
        }

        $prices = Price::whereHas('company_price', function ($query) use ($id) {
            $query->where('company_id', $id);
        })->pluck('name', 'id');

        return $prices;
    }

    /**
     * apiCompanies.
     *
     * @return void
     */
    public function apiCompanies()
    {
        $companies = Company::where('api_id', '!=', '')->get();

        return view('companies.api.index', compact('companies'));
    }

    /**
     * saveLogo.
     *
     * @param  mixed $company
     * @param  mixed $file
     * @return void
     */
    public function saveLogo($company, $file)
    {
        $update_company_url = Company::findOrFail($company->id);
        $update_company_url->logo = 'Logos/Clients/' . $company->id . '/' . $file->getClientOriginalName();
        $update_company_url->update();
        $filepath = 'Logos/Clients/' . $company->id . '/' . $file->getClientOriginalName();
        $name = $file->getClientOriginalName();
        \Storage::disk('logos')->put($name, file_get_contents($file), 'public');
        $s3 = \Storage::disk('s3_upload');
        $s3->put($filepath, file_get_contents($file), 'public');
        //ProcessLogo::dispatch(auth()->user()->id, $filepath, $name, 2);
    }

    /**
     * saveExtraData.
     *
     * @param  mixed $data
     * @param  mixed $company
     * @param  mixed $type
     * @return void
     */
    public function saveExtraData($data, $company, $type)
    {
        switch ($type) {
            case 'price':
                foreach ($data as $key => $item) {
                    $company_price = new CompanyPrice();
                    $company_price->company_id = $company->id;
                    $company_price->price_id = $data[$key];
                    $company_price->save();
                }
                break;

            case 'users':
                foreach ($data as $key => $item) {
                    $userCompany_group = new GroupUserCompany();
                    $userCompany_group->user_id = $data[$key];
                    $userCompany_group->company()->associate($company);
                    $userCompany_group->save();
                }
                break;
        }
    }

    /**
     * searchCompanies.
     *
     * @param mixed $request
     * @return json
     */
    public function searchCompanies(Request $request)
    {
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }

        $companies = Company::where('company_user_id', \Auth::user()->company_user_id)->where('business_name', 'like', '%' . $term . '%')->get();

        $formatted_companies = [];
        foreach ($companies as $company) {
            $formatted_companies[] = ['id' => $company->id, 'text' => $company->business_name];
        }

        return \Response::json($formatted_companies);
    }

    /**
     * validateData
     * 
     * @param mixed $request
     * 
     * @return [type]
     */
    public function validateData($request)
    {   
        $vdata=[
            'business_name' => 'required',
            'logo' => 'max:1000',
            'options' => 'json',
        ];

        //Validating array into request
        if(isset($request['key_name']) && isset($request['key_name'])){
            foreach($request['key_name'] as $a => $name){
                if ($a>=1 && $name==null ) {
                    $vdata=[
                        'business_name' => 'required',
                        'logo' => 'max:1000',
                        'options' => 'json',
                        'key_name '=>'required'
                    ];
                }
            }
            foreach($request['key_value'] as $b => $value){
                    if($b>=1 && $value==null){
                        $vdata=[
                            'business_name' => 'required',
                            'logo' => 'max:1000',
                            'options' => 'json',
                            'key_value ' =>'required'
                        ];
                }
            }
        }

        $validator = \Validator::make($request->all(), $vdata);
        
        return $validator->validated();
    }
}
