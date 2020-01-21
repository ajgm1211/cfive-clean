<style>
    /* Style the tab */
    .tab {
        width: 100% !important;
        overflow: hidden;
        border-bottom: 3px solid #fff; 
        border-radius: 10px 10px 0px 0px;
    }

    /* Style the buttons inside the tab */
    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
        font-size: 16px;
        color: #0072fc; 
        width: 33.33%;
        text-align: left;
        background-color: #f5fafc;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
        background-color: #fafafa;
    }

    /* Create an active/current tablink class */
    .tab button.active {
        border-bottom: 3px solid #0072fc;
        background-color: #fff;
    }

    /* Style the tab content */
    .tabcontent {
        padding: 6px 12px;
        border-top: none;
    }
    .displayno {
        display: none;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="m-portlet custom-portlet no-border">
            <div class="m-portlet__head" style="border-bottom: none; padding: 0;">
                <div class="tab">
                    <button class="tablinks {{$quote->pdf_option->show_type=='detailed' ? 'active':''}}" onclick="openTab(event, 'detailed',{{$quote->id}})" data-quote-id="{{$quote->id}}">Detailed PDF Layout</button>
                    <button class="tablinks {{$quote->pdf_option->show_type=='total in' ? 'active':''}}" onclick="openTab(event, 'all',{{$quote->id}})" data-quote-id="{{$quote->id}}">All-in PDF Layout</button>
                    <button class="tablinks {{$quote->pdf_option->show_type=='charges' ? 'active':''}}" onclick="openTab(event, 'charges',{{$quote->id}})" data-quote-id="{{$quote->id}}">Charges Only PDF Layout</button>
                </div>
                <div id="detailed" class="tabcontent {{$quote->pdf_option->show_type=='detailed' ? '':'displayno'}}">
                    <div class="row">

                        <div class="col-md-3 show_carrier">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" type="checkbox" name="show_carrier" value="1" id="show_carrier" data-quote-id="{{$quote->id}}" data-type="checkbox" data-name="show_carrier" {{$quote->pdf_option->show_carrier==1 ? 'checked':''}}>
                                    <label class="form-check-label title-quote input_form" for="show_carrier">
                                        Show carrier in the offer
                                    </label>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" data-quote-id="{{$quote->id}}" data-name="grouped_origin_charges" type="checkbox" data-type="checkbox" name="grouped_origin_charges" value="1" {{$quote->pdf_option->grouped_origin_charges==1 ? 'checked':''}}>
                                    <span class="title-quote input_form" ><b>Group Origin Charges in:</b></span>
                                    {{ Form::select('origin_charge_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->origin_charges_currency,['class'=>'form-control-sm  pdf-feature select_forms','data-type'=>'select','data-quote-id'=>$quote->id,'data-name'=>'origin_charges_currency']) }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3 col-xs-12">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" type="checkbox" data-quote-id="{{$quote->id}}" name="grouped_total_currency" data-name="grouped_total_currency" data-type="checkbox" value="1" {{$quote->pdf_option->grouped_total_currency==1 ? 'checked':''}}>
                                    <span class="title-quote input_form"><b>Show total in:</b></span>
                                    {{ Form::select('total_in_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->total_in_currency,['class'=>'form-control-sm type  pdf-feature select_forms','data-quote-id'=>$quote->id,'data-name'=>'total_in_currency','data-type'=>'select']) }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="title-quote"><b>Language:</b></label>
                            {{ Form::select('language',['English'=>'English','Spanish'=>'Spanish','Portuguese'=>'Portuguese'],$quote->pdf_option->language,['class'=>'form-control-sm  pdf-feature select_forms','id'=>'language','data-quote-id'=>$quote->id,'data-name'=>'language','data-type'=>'select']) }}
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-3">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" data-quote-id="{{$quote->id}}" data-name="grouped_destination_charges" type="checkbox" data-type="checkbox" name="grouped_destination_charges" value="1" {{$quote->pdf_option->grouped_destination_charges==1 ? 'checked':''}}>
                                    <span class="title-quote input_form"><b>Group Destination Charges in:</b></span>
                                    {{ Form::select('destination_charge_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->destination_charges_currency,['class'=>'form-control-sm   pdf-feature select_forms','data-type'=>'select','data-quote-id'=>$quote->id,'data-name'=>'destination_charges_currency']) }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" data-quote-id="{{$quote->id}}" data-name="grouped_freight_charges" type="checkbox" data-type="checkbox" name="grouped_freight_charges" value="1" {{$quote->pdf_option->grouped_freight_charges==1 ? 'checked':''}}>
                                    <span class="title-quote input_form"><b>Group Freight Charges in (Single rate):</b></span>
                                    {{ Form::select('freight_charge_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->freight_charges_currency,['class'=>'form-control-sm   pdf-feature select_forms','data-type'=>'select','data-quote-id'=>$quote->id,'data-name'=>'freight_charges_currency']) }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3 show_logo">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" type="checkbox" name="show_schedules" value="1" id="show_schedules" data-quote-id="{{$quote->id}}" data-name="show_schedules" data-type="checkbox" {{$quote->pdf_option->show_schedules==1 ? 'checked':''}}>
                                    <label class="form-check-label title-quote input_form" for="show_schedules">
                                        Show schedule's info
                                    </label>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3 show_logo">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" type="checkbox" name="show_logo" value="1" id="show_logo" data-quote-id="{{$quote->id}}" data-name="show_logo" data-type="checkbox" {{$quote->pdf_option->show_logo==1 ? 'checked':''}}>
                                    <span class="form-check-label title-quote input_form" for="show_logo">
                                        Show customer's logo
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" type="checkbox" name="replace_total_title" value="1" id="replace_total_title" data-quote-id="{{$quote->id}}" data-name="replace_total_title" data-type="checkbox" {{$quote->pdf_option->replace_total_title==1 ? 'checked':''}}>
                                    <span class="form-check-label title-quote input_form" for="replace_total_title">
                                        Replace Total by TON/M3
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="all" class="tabcontent {{$quote->pdf_option->show_type=='total in' ? '':'displayno'}}">
                    <div class="row">

                        <div class="col-md-3 col-xs-12">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" type="checkbox" data-quote-id="{{$quote->id}}" name="grouped_total_currency" data-name="grouped_total_currency" data-type="checkbox" value="1" {{$quote->pdf_option->grouped_total_currency==1 ? 'checked':''}}>
                                    <span class="title-quote input_form"><b>Show total in:</b></span>
                                    {{ Form::select('total_in_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->total_in_currency,['class'=>'form-control-sm type  pdf-feature select_forms','data-quote-id'=>$quote->id,'data-name'=>'total_in_currency','data-type'=>'select']) }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3 show_logo">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" type="checkbox" name="show_schedules" value="1" id="show_schedules" data-quote-id="{{$quote->id}}" data-name="show_schedules" data-type="checkbox" {{$quote->pdf_option->show_schedules==1 ? 'checked':''}}>
                                    <span class="form-check-label title-quote input_form" for="show_schedules">
                                        Show schedule's info
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3 show_carrier">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" type="checkbox" name="show_carrier" value="1" id="show_carrier" data-quote-id="{{$quote->id}}" data-type="checkbox" data-name="show_carrier" {{$quote->pdf_option->show_carrier==1 ? 'checked':''}}>
                                    <span class="form-check-label title-quote input_form" for="show_carrier">
                                        Show carrier in the offer
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3 show_logo">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" type="checkbox" name="show_logo" value="1" id="show_logo" data-quote-id="{{$quote->id}}" data-name="show_logo" data-type="checkbox" {{$quote->pdf_option->show_logo==1 ? 'checked':''}}>
                                    <span class="form-check-label title-quote input_form" for="show_logo">
                                        Show customer's logo
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" type="checkbox" name="replace_total_title" value="1" id="replace_total_title" data-quote-id="{{$quote->id}}" data-name="replace_total_title" data-type="checkbox" {{$quote->pdf_option->replace_total_title==1 ? 'checked':''}}>
                                    <span class="form-check-label title-quote input_form" for="replace_total_title">
                                        Replace Total by TON/M3
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="charges" class="tabcontent {{$quote->pdf_option->show_type=='charges' ? '':'displayno'}}">
                    <div class="row">
                        <div class="col-md-3 show_carrier">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" type="checkbox" name="show_carrier" value="1" id="show_carrier" data-quote-id="{{$quote->id}}" data-type="checkbox" data-name="show_carrier" {{$quote->pdf_option->show_carrier==1 ? 'checked':''}}>
                                    <span class="form-check-label title-quote input_form" for="show_carrier">
                                        Show carrier in the offer
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" data-quote-id="{{$quote->id}}" data-name="grouped_origin_charges" type="checkbox" data-type="checkbox" name="grouped_origin_charges" value="1" {{$quote->pdf_option->grouped_origin_charges==1 ? 'checked':''}}>
                                    <span class="title-quote input_form"><b>Group Origin Charges in:</b></span>
                                    {{ Form::select('origin_charge_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->origin_charges_currency,['class'=>'form-control-sm pdf-feature select_forms','data-type'=>'select','data-quote-id'=>$quote->id,'data-name'=>'origin_charges_currency']) }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3 col-xs-12">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" type="checkbox" data-quote-id="{{$quote->id}}" name="grouped_total_currency" data-name="grouped_total_currency" data-type="checkbox" value="1" {{$quote->pdf_option->grouped_total_currency==1 ? 'checked':''}}>
                                    <span class="title-quote input_form"><b>Show total in:</b></span>
                                    {{ Form::select('total_in_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->total_in_currency,['class'=>'form-control-sm type  pdf-feature select_forms','data-quote-id'=>$quote->id,'data-name'=>'total_in_currency','data-type'=>'select']) }}
                                </label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="title-quote"><b>Language:</b></label>
                            {{ Form::select('language',['English'=>'English','Spanish'=>'Spanish','Portuguese'=>'Portuguese'],$quote->pdf_option->language,['class'=>'form-control-sm  pdf-feature select_forms','id'=>'language','data-quote-id'=>$quote->id,'data-name'=>'language','data-type'=>'select']) }}
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-3">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" data-quote-id="{{$quote->id}}" data-name="grouped_destination_charges" type="checkbox" data-type="checkbox" name="grouped_destination_charges" value="1" {{$quote->pdf_option->grouped_destination_charges==1 ? 'checked':''}}>
                                    <span class="title-quote input_form"><b>Group Destination Charges in:</b></span>
                                    {{ Form::select('destination_charge_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->destination_charges_currency,['class'=>'form-control-sm   pdf-feature select_forms','data-type'=>'select','data-quote-id'=>$quote->id,'data-name'=>'destination_charges_currency']) }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3 show_logo">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" type="checkbox" name="show_logo" value="1" id="show_logo" data-quote-id="{{$quote->id}}" data-name="show_logo" data-type="checkbox" {{$quote->pdf_option->show_logo==1 ? 'checked':''}}>
                                    <span class="form-check-label title-quote input_form" for="show_logo">
                                        Show customer's logo
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <label>
                                    <input class="form-check-input pdf-feature check" type="checkbox" name="replace_total_title" value="1" id="replace_total_title" data-quote-id="{{$quote->id}}" data-name="replace_total_title" data-type="checkbox" {{$quote->pdf_option->replace_total_title==1 ? 'checked':''}}>
                                    <span class="form-check-label title-quote input_form" for="replace_total_title">
                                        Replace Total by TON/M3
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="m-portlet__body" style="background-color: #e3e8ee; margin-bottom: 25px;">
    <div class="tab-content" id="show_detailed">
        <div class="row" style="padding-top: 10px;">
            <div class="m-portlet__head-tools d-flex justify-content-center" style="width: 100%;">
                <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                    <li class="nav-item m-tabs__item" id="edit_li">
                        <button class="btn btn-primary-v2 btn-edit" data-toggle="modal" data-target="#SendQuoteModal">
                            Send &nbsp;&nbsp;<i class="fa fa-envelope"></i>
                        </button>
                    </li>
                    <li class="nav-item m-tabs__item" id="edit_li">
                        @if($quote->type=='FCL')
                        <a class="btn btn-primary-v2 btn-edit" href="{{route('quotes-v2.pdf',setearRouteKey($quote->id))}}" target="_blank">
                            PDF &nbsp;&nbsp;<i class="fa fa-download"></i>
                        </a>
                        @elseif($quote->type=='LCL')
                        <a class="btn btn-primary-v2 btn-edit" href="{{route('quotes-v2.pdf.lcl.air',setearRouteKey($quote->id))}}" target="_blank">
                            PDF &nbsp;&nbsp;<i class="fa fa-download"></i>
                        </a>
                        @else
                        <a class="btn btn-primary-v2 btn-edit" href="{{route('quotes-v2.pdf.air',setearRouteKey($quote->id))}}" target="_blank">
                            PDF &nbsp;&nbsp;<i class="fa fa-download"></i>
                        </a>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>