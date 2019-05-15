@extends('layouts.app')
@section('css')
    @parent
    <link href="/css/quote.css" rel="stylesheet" type="text/css" />

    <link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Quotes')
@section('content')
    <br>
    <div class="m-content">
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
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--left" role="tablist" style="border-bottom: none;">
                    <input type="hidden" id="quote-id" value="{{$quote->id}}"/>
                    <li class="nav-item m-tabs__item size-14px" >
                        <a href="{{url('/v2/quotes/search')}}">
                            <- Back to search
                        </a>
                    </li>                    
                </ul>                
                <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right" role="tablist" style="border-bottom: none;">
                    <input type="hidden" id="quote-id" value="{{$quote->id}}"/>
                    <li class="nav-item m-tabs__item" >
                        <button class="btn btn-primary-v2" data-toggle="modal" data-target="#SendQuoteModal">
                            Send &nbsp;&nbsp;<i class="fa fa-envelope"></i>
                        </button>
                    </li>
                    <li class="nav-item m-tabs__item" >
                        <a class="btn btn-primary-v2" href="#">
                            PDF &nbsp;&nbsp;<i class="fa fa-download"></i>
                        </a>
                    </li>
                    <li class="nav-item m-tabs__item" >
                        <a class="btn btn-primary-v2" href="{{route('quotes-v2.duplicate',setearRouteKey($quote->id))}}">
                            Duplicate &nbsp;&nbsp;<i class="fa fa-plus"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Quote details -->
            <div class="col-md-12">
                <div class="m-portlet custom-portlet">
                    <div class="m-portlet__head">
                        <div class="row" style="padding-top: 20px;">
                            <h3 class="title-quote size-14px">Quote info</h3>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                                <li class="nav-item m-tabs__item" id="edit_li">
                                    <a class="btn btn-primary-v2" id="edit-quote" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Edit &nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="tab-content">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" value="{{$quote->id}}" class="form-control id" hidden >
                                    <label class="title-quote"><b>Quotation ID:&nbsp;&nbsp;</b></label>
                                    <input type="text" value="{{$quote->custom_quote_id!='' ? $quote->custom_quote_id:$quote->quote_id}}" class="form-control quote_id" hidden >
                                    <span class="quote_id_span">{{$quote->custom_quote_id!='' ? $quote->custom_quote_id:$quote->quote_id}}</span>
                                </div>
                                <div class="col-md-4">
                                    <label class="title-quote"><b>Type:&nbsp;&nbsp;</b></label>
                                    <input type="text" value="{{$quote->quote_id}}" class="form-control" hidden >
                                    {{ Form::select('type',['FCL'=>'FCL','LCL'=>'LCL'],$quote->type,['class'=>'form-control type select2','hidden','disabled']) }}
                                    <span class="type_span">{{$quote->type}}</span>
                                </div>
                                <div class="col-md-4">
                                    <label class="title-quote"><b>Company:&nbsp;&nbsp;</b></label>
                                    {{ Form::select('company_id',$companies,$quote->company_id,['class'=>'form-control company_id select2','hidden']) }}
                                    <span class="company_span">{{$quote->company->business_name}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Status:&nbsp;&nbsp;</b></label>
                                    {{ Form::select('status',['Draft'=>'Draft','Win'=>'Win','Sent'=>'Sent'],$quote->status,['class'=>'form-control status select2','hidden','']) }}
                                    <span class="status_span Status_{{$quote->status}}" style="border-radius: 10px;">{{$quote->status}} <i class="fa fa-check"></i></span>
                                </div>
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Destination type:&nbsp;&nbsp;</b></label>
                                    {{ Form::select('status',[1=>'Port to Port',2=>'Port to Door',3=>'Door to Port',4=>'Door to Door'],$quote->delivery_type,['class'=>'form-control delivery_type select2','hidden','']) }}
                                    <span class="delivery_type_span">
                                    @if($quote->delivery_type==1)
                                            Port to Port
                                        @elseif($quote->delivery_type==2)
                                            Port to Door
                                        @elseif($quote->delivery_type==3)
                                            Door to Port
                                        @else
                                            Door to Door
                                        @endif
                                </span>
                                </div>
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Contact:&nbsp;&nbsp;</b></label>
                                    {{ Form::select('contact_id',$contacts,$quote->contact_id,['class'=>'form-control contact_id select2','hidden']) }}
                                    <span class="contact_id_span">{{$quote->contact->first_name}} {{$quote->contact->last_name}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Date issued:&nbsp;&nbsp;</b></label>
                                    @php
                                        $date = date_create($quote->date_issued);
                                    @endphp
                                    <span class="date_issued_span">{{date_format($date, 'M d, Y H:i')}}</span>
                                    {!! Form::text('created_at', date_format($date, 'Y-m-d H:i'), ['placeholder' => 'Validity','class' => 'form-control m-input date_issued','readonly'=>true,'required' => 'required','hidden']) !!}
                                </div>
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Equipment:&nbsp;&nbsp;</b></label>
                                    <span class="equipment_span">
                                        <?php
                                            $equipment=json_decode($quote->equipment);
                                        ?>
                                        @foreach($equipment as $item)
                                            {{$item}}@unless($loop->last),@endunless
                                        @endforeach
                                    </span>
                                    {{ Form::select('equipment[]',['20' => '20','40' => '40','40HC'=>'40HC','40NOR'=>'40NOR','45'=>'45'],$equipment,['class'=>'form-control equipment','id'=>'equipment','multiple' => 'multiple','required' => 'true','hidden']) }}
                                </div>
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Price level:&nbsp;&nbsp;</b></label>
                                    <span class="price_level_span">{{@$quote->price->name}}</span>
                                    {{ Form::select('price_id',$prices,@$quote->price_id,['class'=>'form-control price_id select2','hidden']) }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Validity:&nbsp;&nbsp;</b></label>
                                    <span class="validity_span">{{$quote->validity_start}} / {{$quote->validity_end}}</span>
                                    @php
                                        $validity = $quote->validity_start ." / ". $quote->validity_end;
                                    @endphp
                                    {!! Form::text('validity_date', $validity, ['placeholder' => 'Validity','class' => 'form-control m-input validity','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required','hidden']) !!}
                                </div>
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Incoterm:&nbsp;&nbsp;</b></label>
                                    {{ Form::select('incoterm_id',$incoterms,$quote->incoterm_id,['class'=>'form-control incoterm_id select2','hidden','']) }}
                                    <span class="incoterm_id_span">{{$quote->incoterm->name}}</span>
                                </div>
                                <div class="col-md-4">
                                    <br>
                                    <label class="title-quote"><b>Owner:&nbsp;&nbsp;</b></label>
                                    {{ Form::select('user_id',$users,$quote->user_id,['class'=>'form-control user_id select2','hidden','']) }}
                                    <span class="user_id_span">{{$quote->user->name}} {{$quote->user->lastname}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center" id="update_buttons" hidden>
                                    <br>
                                    <hr>
                                    <br>
                                    <a class="btn btn-danger" id="cancel" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                                    </a>
                                    <a class="btn btn-primary" id="update" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Update&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Charges -->
        @if($quote->type=='FCL')
            @include('quotesv2.partials.ratesByContainer')
        @else
            @include('quotesv2.partials.ratesByPackage')
        @endif
        <!-- Payments and terms conditions -->
        <div class="row">
            <div class="col-md-12">
                <div class="m-portlet custom-portlet">
                    <div class="m-portlet__head">
                        <div class="row" style="padding-top: 20px;">
                            <h3 class="title-quote size-14px">Payment conditions</h3>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                                <li class="nav-item m-tabs__item" id="edit_li">
                                    <a class="btn btn-primary-v2" id="edit-payments" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Edit&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="card card-body bg-light">
                            <span class="payment_conditions_span">{!! $quote->payment_conditions !!}</span>
                            <div class="payment_conditions_textarea" hidden>
                                <textarea name="payment_conditions" class="form-control payment_conditions editor" id="payment_conditions">{!!$quote->payment_conditions!!}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center" id="update_payments" hidden>
                                    <br>
                                    <a class="btn btn-danger" id="cancel-payments" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                                    </a>
                                    <a class="btn btn-primary" id="update-payments" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Update &nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                    </a>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="m-portlet custom-portlet">
                    <div class="m-portlet__head">
                        <div class="row" style="padding-top: 20px;">
                            <h3 class="title-quote size-14px">Terms & conditions</h3>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                                <li class="nav-item m-tabs__item" id="edit_li">
                                    <a class="btn btn-primary-v2" id="edit-terms" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Edit&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="card card-body bg-light">
                            <span class="terms_and_conditions_span">{!! $quote->terms_and_conditions !!}</span>
                            <div class="terms_and_conditions_textarea" hidden>
                                <textarea name="terms_and_conditions" class="form-control terms_and_conditions editor" id="terms_and_conditions">{!!$quote->terms_and_conditions!!}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center" id="update_terms" hidden>
                                    <br>
                                    <a class="btn btn-danger" id="cancel-terms" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Cancel&nbsp;&nbsp;<i class="fa fa-close"></i>
                                    </a>
                                    <a class="btn btn-primary" id="update-terms" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Update&nbsp;&nbsp;<i class="fa fa-pencil"></i>
                                    </a>
                                    <br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="m-portlet custom-portlet">
                    <div class="m-portlet__head">
                        <div class="row" style="padding-top: 20px;">
                            <h3 class="title-quote size-14px">PDF Layout</h3>
                        </div>
                        <div class="m-portlet__head-tools">
                            <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                                <li class="nav-item m-tabs__item" id="edit_li">
                                    <a class="btn btn-primary-v2" id="edit-quote" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                                        Send &nbsp;&nbsp;<i class="fa fa-envelope"></i>
                                    </a>
                                </li>
                                <li class="nav-item m-tabs__item" id="edit_li">
                                    <a class="btn btn-primary-v2" href="{{route('quotes-v2.pdf',setearRouteKey($quote->id))}}" target="_blank" >
                                        PDF &nbsp;&nbsp;<i class="fa fa-download"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="tab-content" id="show_detailed">
                            <div class="row" class="">
                                <div class="col-md-2 col-xs-12">
                                    {{ Form::select('show_type',['detailed'=>'Show detailed','total in'=>'Show total in'],$quote->pdf_option->show_type,['class'=>'form-control-sm type select2 pdf-feature','id'=>'show_hide_select','data-quote-id'=>$quote->id,'data-name'=>'show_type','data-type'=>'select']) }}
                                </div>
                                <div class="col-md-2 col-xs-12">
                                    <div class="form-check">
                                        <input class="form-check-input pdf-feature" type="checkbox" data-quote-id="{{$quote->id}}" name="grouped_total_currency" data-name="grouped_total_currency" data-type="checkbox" value="1" {{$quote->pdf_option->grouped_total_currency==1 ? 'checked':''}}>
                                        <label class="title-quote"><b>Show total in:</b></label>
                                        {{ Form::select('total_in_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->total_in_currency,['class'=>'form-control-sm type select2 pdf-feature','data-quote-id'=>$quote->id,'data-name'=>'total_in_currency','data-type'=>'select']) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <label class="title-quote"><b>Language:</b></label>
                                    {{ Form::select('language',['English'=>'English','Spanish'=>'Spanish','Portuguese'=>'Portuguese'],$quote->pdf_option->language,['class'=>'form-control-sm company_id select2 pdf-feature','id'=>'language','data-quote-id'=>$quote->id,'data-name'=>'language','data-type'=>'select']) }}
                                </div>
                                <div class="col-auto show_carrier">
                                    <div class="form-check">
                                        <input class="form-check-input pdf-feature" type="checkbox" name="show_carrier" value="1" id="show_carrier" data-quote-id="{{$quote->id}}" data-type="checkbox" data-name="show_carrier" {{$quote->pdf_option->show_carrier==1 ? 'checked':''}}>
                                        <label class="form-check-label title-quote" for="show_carrier">
                                            Show carrier
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-auto show_logo">
                                    <div class="form-check">
                                        <input class="form-check-input pdf-feature" type="checkbox" name="show_logo" value="1" id="show_logo" data-quote-id="{{$quote->id}}" data-name="show_logo" data-type="checkbox" {{$quote->pdf_option->show_logo==1 ? 'checked':''}}>
                                        <label class="form-check-label title-quote" for="show_logo">
                                            Show customer logo
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row" style="padding-top: 25px;">
                                <div class="col-md-3 group_freight_charges">
                                    <div class="form-check">
                                        <input class="form-check-input pdf-feature" data-quote-id="{{$quote->id}}" data-name="grouped_freight_charges" type="checkbox" data-type="checkbox" name="grouped_freight_charges" value="1" {{$quote->pdf_option->grouped_freight_charges==1 ? 'checked':''}}>
                                        <label class="title-quote"><b>Group Freight Charges in:</b></label>
                                        {{ Form::select('freight_charge_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->freight_charges_currency,['class'=>'form-control-sm company_id select2 pdf-feature','data-type'=>'select','data-quote-id'=>$quote->id,'data-name'=>'freight_charges_currency']) }}
                                    </div>
                                </div>
                                <div class="col-md-3 group_origin_charges">
                                    <div class="form-check">
                                        <input class="form-check-input pdf-feature" data-quote-id="{{$quote->id}}" data-name="grouped_origin_charges" type="checkbox" data-type="checkbox" name="grouped_origin_charges" value="1" {{$quote->pdf_option->grouped_origin_charges==1 ? 'checked':''}}>
                                        <label class="title-quote"><b>Group Origin Charges in:</b></label>
                                        {{ Form::select('origin_charge_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->origin_charges_currency,['class'=>'form-control-sm company_id select2 pdf-feature','data-type'=>'select','data-quote-id'=>$quote->id,'data-name'=>'origin_charges_currency']) }}
                                    </div>
                                </div>
                                <div class="col-md-3 group_destination_charges">
                                    <div class="form-check">
                                        <input class="form-check-input pdf-feature" data-quote-id="{{$quote->id}}" data-name="grouped_destination_charges" data-type="checkbox" type="checkbox" name="grouped_destination_charges" value="1" {{$quote->pdf_option->grouped_destination_charges==1 ? 'checked':''}}>
                                        <div class="form-group">
                                            <label class="title-quote"><b>Group Destination Charges in:</b></label>
                                            {{ Form::select('destination_charge_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->destination_charges_currency,['class'=>'form-control-sm company_id select2 pdf-feature','data-type'=>'select','data-quote-id'=>$quote->id,'data-name'=>'destination_charges_currency']) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('quotesv2.partials.sendQuoteModal')
@endsection

@section('js')
    @parent
    <script src="{{asset('js/base.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/quotes-v2.js')}}" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-touchspin.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
    <script src="/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
    <script src="{{asset('js/tinymce/jquery.tinymce.min.js')}}"></script>
    <script src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
    <script type="text/javascript">

        var editor_config = {
            path_absolute : "/",
            selector: "textarea.editor",
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

    </script>
@stop