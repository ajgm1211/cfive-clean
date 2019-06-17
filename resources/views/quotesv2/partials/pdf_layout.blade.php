  <div class="row">
    <div class="col-md-12">
      <div class="m-portlet custom-portlet no-border">
        <div class="m-portlet__head">
          <div class="row" style="padding-top: 20px;">
            <h3 class="title-quote size-14px">PDF Layout</h3>
          </div>
          <div class="m-portlet__head-tools">
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
                @else
                  <a class="btn btn-primary-v2 btn-edit" href="{{route('quotes-v2.pdf.lcl.air',setearRouteKey($quote->id))}}" target="_blank">
                    PDF &nbsp;&nbsp;<i class="fa fa-download"></i>
                  </a>
                @endif
              </li>
            </ul>
          </div>
        </div>
        <div class="m-portlet__body">
          <div class="tab-content" id="show_detailed">
            <div class="row" class="">
              <div class="col-md-3 group_origin_charges">
                <div class="form-check">
                  <input class="form-check-input pdf-feature check" data-quote-id="{{$quote->id}}" data-name="grouped_origin_charges" type="checkbox" data-type="checkbox" name="grouped_origin_charges" value="1" {{$quote->pdf_option->grouped_origin_charges==1 ? 'checked':''}}> <!-- aqui-->
                  <label class="title-quote input_form"><b>Group Origin Charges in:</b></label>
                  {{ Form::select('origin_charge_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->origin_charges_currency,['class'=>'form-control-sm company_id select2 pdf-feature select_forms','data-type'=>'select','data-quote-id'=>$quote->id,'data-name'=>'origin_charges_currency']) }}
                </div>
              </div>


              <div class="col-md-3 col-xs-12">
                <div class="form-check">
                  <input class="form-check-input pdf-feature check" type="checkbox" data-quote-id="{{$quote->id}}" name="grouped_total_currency" data-name="grouped_total_currency" data-type="checkbox" value="1" {{$quote->pdf_option->grouped_total_currency==1 ? 'checked':''}}>
                  <label class="title-quote input_form"><b>Show total in:</b></label>
                  {{ Form::select('total_in_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->total_in_currency,['class'=>'form-control-sm type select2 pdf-feature select_forms','data-quote-id'=>$quote->id,'data-name'=>'total_in_currency','data-type'=>'select']) }}
                </div>
              </div>


              <div class="col-md-2 show_logo">
                <div class="form-check">
                  <input class="form-check-input pdf-feature check" type="checkbox" name="show_logo" value="1" id="show_logo" data-quote-id="{{$quote->id}}" data-name="show_logo" data-type="checkbox" {{$quote->pdf_option->show_logo==1 ? 'checked':''}}>
                  <label class="form-check-label title-quote input_form" for="show_logo">
                    Show customer logo
                  </label>
                </div>
              </div>

               <div class="col-md-2 show_logo">
                <div class="form-check">
                  <input class="form-check-input pdf-feature check" type="checkbox" name="show_schedules" value="1" id="show_schedules" data-quote-id="{{$quote->id}}" data-name="show_schedules" data-type="checkbox" {{$quote->pdf_option->show_schedules==1 ? 'checked':''}}>
                  <label class="form-check-label title-quote input_form" for="show_schedules">
                    Show schedule's info
                  </label>
                </div>
              </div>
            </div>

            <div class="row" style="padding-top: 10px;">
              <div class="col-md-3 group_freight_charges">
                <div class="form-check">
                  <input class="form-check-input pdf-feature check" data-quote-id="{{$quote->id}}" data-name="grouped_freight_charges" type="checkbox" data-type="checkbox" name="grouped_freight_charges" value="1" {{$quote->pdf_option->grouped_freight_charges==1 ? 'checked':''}}>
                  <label class="title-quote input_form"><b>Show Freight Charges in:</b></label>
                  {{ Form::select('freight_charge_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->freight_charges_currency,['class'=>'form-control-sm company_id select2 pdf-feature select_forms','data-type'=>'select','data-quote-id'=>$quote->id,'data-name'=>'freight_charges_currency']) }}
                </div>
              </div>

              <div class="col-md-3 group_destination_charges">
                <div class="form-check">
                  <input class="form-check-input pdf-feature check" data-quote-id="{{$quote->id}}" data-name="grouped_destination_charges" type="checkbox" data-type="checkbox" name="grouped_destination_charges" value="1" {{$quote->pdf_option->grouped_destination_charges==1 ? 'checked':''}}>
                  <label class="title-quote input_form"><b>Group Destination Charges in:</b></label>
                  {{ Form::select('destination_charge_currency',['USD'=>'USD','EUR'=>'EUR'],$quote->pdf_option->destination_charges_currency,['class'=>'form-control-sm company_id select2 pdf-feature select_forms','data-type'=>'select','data-quote-id'=>$quote->id,'data-name'=>'destination_charges_currency']) }}
                </div>
              </div>

              <div class="col-md-3 show_carrier">
                <div class="form-check">
                  <input class="form-check-input pdf-feature check" type="checkbox" name="show_carrier" value="1" id="show_carrier" data-quote-id="{{$quote->id}}" data-type="checkbox" data-name="show_carrier" {{$quote->pdf_option->show_carrier==1 ? 'checked':''}}>
                  <label class="form-check-label title-quote input_form" for="show_carrier">
                    Show carrier
                  </label>
                </div>
              </div>

              <div class="col-auto">
                <label class="title-quote"><b>Language:</b></label>
                {{ Form::select('language',['English'=>'English','Spanish'=>'Spanish','Portuguese'=>'Portuguese'],$quote->pdf_option->language,['class'=>'form-control-sm company_id select2 pdf-feature select_forms','id'=>'language','data-quote-id'=>$quote->id,'data-name'=>'language','data-type'=>'select']) }}
              </div>

              <div class="col-md-12 col-xs-12">
                <hr>
                {{ Form::select('show_type',['detailed'=>'Show detailed','total in'=>'Show total in'],$quote->pdf_option->show_type,['class'=>'form-control-sm type select2 pdf-feature select_forms','id'=>'show_hide_select','data-quote-id'=>$quote->id,'data-name'=>'show_type','data-type'=>'select']) }}
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>