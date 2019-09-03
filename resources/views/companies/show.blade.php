<?php
/**
 * Created by PhpStorm.
 * User: Julio
 * Date: 15/05/2018
 * Time: 03:59 PM
 */
?>
@extends('layouts.app')
@section('title', 'Companies | Details')
@section('css')
    @parent
    <link rel="stylesheet" type="text/css" href="/assets/plugins/button-dropdown/css/bootstrap.css">
    <script src="/assets/plugins/button-dropdown/js/jquery3.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="/assets/plugins/button-dropdown/js/bootstrap.js"></script>
@endsection
@section('content')
    <div class="m-content">
        <div class="m-portlet--mobile">
            @if(Session::has('message.nivel'))
                <div class="col-md-12">
                    <br>
                    <div class="m-alert m-alert--icon m-alert--outline alert alert-{{ session('message.nivel') }} alert-dismissible fade show" role="alert">
                        <div class="m-alert__icon">
                            <i class="la la-warning"></i>
                        </div>
                        <div class="m-alert__text">
                            <strong>
                                {{ session('message.title') }}
                            </strong>
                            {{ session('message.content') }}
                        </div>
                        <div class="m-alert__close">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            @endif
            <div class="m-portlet__body">
                <!--begin: Search Form -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                                    <div class="m-portlet__body text-center">
                                        @if($company->logo!='')
                                            <div class="" style="line-height: .5;">
                                                <img src="{{Storage::disk('s3_upload')->url($company->logo)}}" class="img img-fluid" style="width: 100px; height: auto; margin-bottom:25px">
                                            </div>
                                            <br>
                                        @endif
                                        <h2 class="size-18px color-blue" style="text-transform: uppercase;"><b>{{$company->business_name}}</b> </h2>
                                        <hr>
                                        <button class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Actions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" x-placement="top-start" style="position: absolute; transform: translate3d(0px, -136px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            <a class="dropdown-item" href="#" onclick="AbrirModal('edit',{{$company->id}})">
                                            <span>
                                                Edit
                                                &nbsp;
                                                <i class="la la-edit"></i>
                                            </span>
                                            </a>
                                            <a id="delete-company-show" href="#" class="dropdown-item" data-company-id="{{$company->id}}" title="Delete">
                                            <span>
                                                Delete
                                                &nbsp;
                                                <i class="la la-trash"></i>
                                            </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                                    <div class="m-portlet__body">
                                        <h4 class="size-16px color-blue" data-toggle="collapse" data-target="#about_company" style="cursor: pointer"><i class="fa fa-angle-down"></i> &nbsp;<b>About {{$company->business_name}}</b></h4>
                                        <hr>
                                        <div class="collapse show" id="about_company">
                                            <label><b>Name</b></label>
                                            <p class="color-black">
                                                <span id="business_name_span">{{$company->business_name}}</span>
                                                <input type="text" class="form-control" id="business_name_input" value="{{$company->business_name}}" hidden>
                                                <a  id='edit_business_name' onclick="display_business_name()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill pull-right"  title="Edit ">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <a  id='save_business_name' onclick="save_business_name({{$company->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">

                                                    <i class="la la-save"></i>
                                                </a>
                                                <a  id='cancel_business_name' onclick="cancel_business_name()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                                                    <i  class="la la-reply"></i>
                                                </a>
                                                <br>
                                            </p>
                                            <hr>
                                            <label><b>Phone</b></label>
                                            <p class="color-black">
                                                <span id="phone_span">{{$company->phone}}</span>
                                                <input type="text" class="form-control" id="phone_input" value="{{$company->phone}}" hidden>
                                                <a  id='edit_phone' onclick="display_phone()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill pull-right"  title="Edit ">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <a  id='save_phone' onclick="save_phone({{$company->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">
                                                    <i class="la la-save"></i>
                                                </a>
                                                <a  id='cancel_phone' onclick="cancel_phone()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                                                    <i  class="la la-reply"></i>
                                                </a>
                                                <br>
                                            </p>
                                            <hr>
                                            <label><b>Email</b></label>
                                            <p class="color-black">
                                                <span id="email_span">{{$company->email}}</span>
                                                <input type="email" class="form-control" id="email_input" value="{{$company->email}}" hidden>
                                                <a  id='edit_email' onclick="display_email()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill pull-right"  title="Edit ">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <a  id='save_email' onclick="save_email({{$company->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">
                                                    <i class="la la-save"></i>
                                                </a>
                                                <a  id='cancel_email' onclick="cancel_email()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                                                    <i  class="la la-reply"></i>
                                                </a>
                                                <br>
                                            </p>
                                            <hr>
                                            <label><b>Tax number</b></label>
                                            <p class="color-black">
                                                <span id="tax_number_span">{{$company->tax_number}}</span>
                                                <input type="text" class="form-control" id="tax_number_input" value="{{$company->tax_number}}" hidden>
                                                <a  id='edit_tax_number' onclick="display_tax_number()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill pull-right"  title="Edit ">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <a  id='save_tax_number' onclick="save_tax_number({{$company->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">
                                                    <i class="la la-save"></i>
                                                </a>
                                                <a  id='cancel_tax_number' onclick="cancel_tax_number()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                                                    <i  class="la la-reply"></i>
                                                </a>
                                                <br>
                                            </p>
                                            <hr>
                                            <label><b>PDF language</b></label>
                                            <p class="color-black">
                                            <span id="pdf_language_span">
                                                @if($company->pdf_language==1)
                                                    English
                                                @elseif($company->pdf_language==2)
                                                    Spanish
                                                @else
                                                    Portuguese
                                                @endif
                                            </span>
                                                {{ Form::select('pdf_language',['0'=>'Choose a language',1=>'English',2=>'Spanish',3=>'Portuguese'],$company->pdf_language,['class'=>'custom-select form-control','id' => 'pdf_language_select','hidden'=>'true']) }}
                                                <a  id='edit_pdf_language' onclick="display_pdf_language()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill pull-right"  title="Edit ">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <a  id='save_pdf_language' onclick="save_pdf_language({{$company->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden="true">
                                                    <i class="la la-save"></i>
                                                </a>
                                                <a  id='cancel_pdf_language' onclick="cancel_pdf_language()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden="true">
                                                    <i  class="la la-reply"></i>
                                                </a>
                                                <br>
                                            </p>
                                            <hr>
                                            <label><b>Address</b></label>
                                            <p class="color-black">
                                                <span id="address_span">{{$company->address}}</span>
                                                <textarea class="form-control" id="address_input" hidden>
                                                {{trim($company->address)}}
                                            </textarea>
                                                <a  id='edit_address' onclick="display_address()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill pull-right"  title="Edit ">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <a  id='save_address' onclick="save_address({{$company->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden>

                                                    <i class="la la-save"></i>
                                                </a>
                                                <a  id='cancel_address' onclick="cancel_address()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden>
                                                    <i  class="la la-reply"></i>
                                                </a>
                                                <br>
                                            </p>
                                            <hr>
                                            <label><b>Price level</b></label>
                                            @if(isset($company->price_name) && count($company->price_name)>0)
                                                <div id="price_level_list">
                                                    <ul id="price_level_ul">
                                                        @foreach($company->price_name as $price)
                                                            <li style="margin-left: -25px;" class="color-black">{{$price->name}}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>

                                            @else
                                                <p class="color-black">
                                                    <span id="price_level_span">There are not associated prices</span>
                                                </p>
                                            @endif
                                            <p>
                                                {{ Form::select('price_id[]',$prices,$company->price_name,['class'=>'custom-select form-control','id' => 'price_level_select','multiple'=>'true','hidden'=>'false']) }}
                                                <a  id='edit_prices' onclick="display_price_level()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill pull-right"  title="Edit ">
                                                    <i class="la la-edit"></i>
                                                </a>
                                                <a  id='save_prices' onclick="save_price_level({{$company->id}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Save" hidden>
                                                    <i class="la la-save"></i>
                                                </a>
                                                <a  id='cancel_prices' onclick="cancel_price_level()" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" hidden>
                                                    <i  class="la la-reply"></i>
                                                </a>
                                                <br>
                                            </p>
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                                    <div class="m-portlet__body">
                                        <h4 class="size-16px color-blue" data-toggle="collapse" data-target="#about_company" style="cursor: pointer"><i class="fa fa-angle-down"></i> &nbsp;<b>Payment conditions</b></h4>
                                        <hr>
                                        <div class="collapse show" id="about_company">
                                            {!! Form::open(['route' => 'companies.update.payments','class' => 'form-group m-form__group','type'=>'POST']) !!}
                                            <input type="hidden" name="company_id" value="{{$company->id}}"/>
                                            {!! Form::textarea('payment_conditions', $company->payment_conditions, ['placeholder' => 'Please enter payment conditions','class' => 'form-control m-input address_input editor','id'=>'payment_conditions','rows'=>4]) !!}
                                            <br>
                                            <button class="btn btn-primary" type="submit">
                                                Save
                                            </button>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                                    <div class="m-portlet__body">
                                        <h4 class="size-16px color-blue" data-toggle="collapse" data-target="#company_contacts" style="cursor: pointer"><i class="fa fa-angle-down"></i> &nbsp;<b>Contacts</b></h4>
                                        <hr>
                                        <div class="collapse show" id="company_contacts">
                                            @if(!$company->contact->isEmpty())
                                                @foreach($company->contact as $contact)
                                                    <ul>
                                                        <li>{{$contact->first_name}} {{$contact->last_name}} <a href="#" data-contact-id="{{$contact->id}}" id="delete-contact"><span class="pull-right"><i class="fa fa-close"></i></span></a></li>
                                                    </ul>
                                                @endforeach
                                            @else
                                                <p>No contacts</p>
                                            @endif
                                            <br>
                                            <div class="text-center">
                                                <button class="btn btn-default" onclick="AbrirModal('addContact',{{$company->id}})">
                                                    Add contact
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="m-portlet m-portlet--brand m-portlet--head-solid-bg">
                                    <div class="m-portlet__body">
                                        <h4 class="size-16px color-blue" data-toggle="collapse" data-target="#company_owners" style="cursor: pointer"><i class="fa fa-angle-down"></i> &nbsp;<b>Owners</b></h4>
                                        <hr>
                                        <div class="collapse show" id="company_owners">
                                            @if(!$company->groupUserCompanies->isEmpty())
                                                @foreach($company->groupUserCompanies as $groupUser)
                                                    <ul>
                                                        <li>{{$groupUser->user->name}} {{$groupUser->user->lastname}} <a href="#" data-owner-id="{{$groupUser->user_id}}" id="delete-owner"><span class="pull-right"><i class="fa fa-close"></i></span></a></li>
                                                    </ul>
                                                @endforeach
                                            @else
                                                <p>No Owners</p>
                                            @endif
                                            <br>
                                            <div class="text-center">
                                                <button class="btn btn-default" data-toggle="modal" data-target="#addOwnerModal">
                                                    Add owner
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <table class="m-datatable text-center" id="html_table" >
                            <thead>
                            <tr>
                                <th title="Status">
                                    Id
                                </th>
                                <th title="Created">
                                    Created
                                </th>
                                <th title="Origin">
                                    Origin
                                </th>
                                <th title="Destination">
                                    Destination
                                </th>
                                <th title="Options">
                                    Options
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $origin_li='';
                                $destination_li='';
                            @endphp
                            @foreach ($quotes as $quote)
                                <?php
                                    if(isset($quote->company)){
                                        $company_name  = $quote->company->business_name;
                                    }
                                    if($quote->custom_quote_id!=''){
                                        $id  = $quote->custom_quote_id;
                                    }else{
                                        $id = $quote->quote_id;
                                    }

                                    if($quote->type=='AIR'){
                                        $origin=$quote->origin_airport;
                                        $destination=$quote->destination_airport;
                                        $img='<img src="/images/plane-blue.svg" class="img img-responsive" width="25">';
                                    }else{
                                        $origin=$quote->origin_port;
                                        $destination=$quote->destination_port;
                                        $img='<img src="/images/logo-ship-blue.svg" class="img img-responsive" width="25">';
                                    }
                                    
                                    $explode_orig = explode("| ",$origin);
                                    $explode_dest = explode("| ",$destination);
                                    foreach($explode_orig as $item){
                                        $origin_li.='<li>'.$item.'</li>';
                                    }

                                    foreach($explode_dest as $item){
                                        $destination_li.='<li>'.$item.'</li>';
                                    }
                                ?>
                                <tr>
                                    <td >{{$id}}</td>
                                    <td>{{ date_format($quote->created_at, 'M d, Y H:i')}}</td>
                                    <td>{!! $origin !!}</td>
                                    <td>{!! $destination !!}</td>
                                    <td>
                                        <a href="{{route('quotes-v2.show',setearRouteKey($quote->id))}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Show ">
                                            <i class="la la-eye"></i>
                                        </a>
                                        <a href="{{route('quotes-v2.duplicate',setearRouteKey($quote->id))}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Duplicate ">
                                            <i class="la la-plus"></i>
                                        </a>
                                        <button id="delete-quote" data-quote-id="{{$quote->id}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Delete ">
                                            <i class="la la-eraser"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('companies.partials.companiesModal')
    @include('companies.partials.deleteCompaniesModal')
    @include('companies.partials.addContactModal')
    @include('companies.partials.addOwnerModal')

@endsection

@section('js')
    @parent
    <script src="{{asset('js/base.js')}}" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/datatables/base/html-table-company-quotes.js" type="text/javascript"></script>
    <script src="{{asset('js/tinymce/jquery.tinymce.min.js')}}"></script>
    <script src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
    <script src="{{asset('js/companies.js')}}"></script>
    <script>

        var editor_config = {
            path_absolute : "/",
            selector: "textarea#payment_conditions",
            plugins: ["template"],
            toolbar: "insertfile undo redo | template | bold italic strikethrough | alignleft aligncenter alignright alignjustify | ltr rtl | bullist numlist outdent indent removeformat formatselect| link image media | emoticons charmap | code codesample | forecolor backcolor",
            external_plugins: { "nanospell": "{{asset('js/tinymce/plugins/nanospell/plugin.js')}}" },
            nanospell_server:"php",
            browser_spellcheck: true,
            relative_urls: false,
            remove_script_host: false,
            file_browser_callback : function(field_name, url, type, win) {
                var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

                var cmsURL = editor_config.path_absolute + 'laravel-filemanager?field_name=' + field_name;
                if (type == 'image') {
                    cmsURL = cmsURL + "&type=Images";
                } else {
                    cmsURL = cmsURL + "&type=Files";
                }

                tinymce.activeEditor.windowManager.open({
                    file: '<?= route('elfinder.tinymce4') ?>',// use an absolute path!
                    title: 'File manager',
                    width: 900,
                    height: 450,
                    resizable: 'yes'
                }, {
                    setUrl: function (url) {
                        win.document.getElementById(field_name).value = url;
                    }
                });
            }
        };

        tinymce.init(editor_config);

        function AbrirModal(action,id){
            if(action == "edit"){
                var url = '{{ route("companies.edit", ":id") }}';
                url = url.replace(':id', id);
                $('.modal-body').load(url,function(){
                    $('#companyModal').modal({show:true});
                });
            }if(action == "add"){
                var url = '{{ route("companies.add") }}';
                $('.modal-body').load(url,function(){
                    $('#companyModal').modal({show:true});
                });
            }
            if(action == "delete"){
                var url = '{{ route("companies.delete", ":id") }}';
                url = url.replace(':id', id);
                $('.modal-body').load(url,function(){
                    $('#deleteCompanyModal').modal({show:true});
                });
            }
            if(action == "addContact"){
                var url = '{{ route("contacts.addCMC",":id") }}';
                url = url.replace(':id', id);
                $('.modal-body').load(url,function(){
                    $('#addContactModal').modal({show:true});
                });
            }

        }

    </script>
@stop

