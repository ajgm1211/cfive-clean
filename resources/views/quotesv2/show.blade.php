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
    <div class="row">
      <div class="col-md-10 offset-md-1">
        <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right" role="tablist" style="border-bottom: none;">
          <li class="nav-item m-tabs__item" >
            <a class="btn btn-primary-v2" id="create-quote-back" data-toggle="tab" href="#" role="tab">
              Send &nbsp;&nbsp;<i class="fa fa-envelope"></i>
            </a>
          </li>
          <li class="nav-item m-tabs__item" >
            <a class="btn btn-primary-v2" id="create-quote-back" data-toggle="tab" href="#" role="tab">
              PDF
            </a>
          </li>
          <li class="nav-item m-tabs__item" >
            <a class="btn btn-primary-v2" id="create-quote-back" data-toggle="tab" href="#" role="tab">
              Duplicate
            </a>
          </li>
        </ul>
      </div>
      <div class="col-md-10 offset-md-1">
        <div class="m-portlet">
          <div class="m-portlet__head">
            <div class="row" style="padding-top: 20px;">
              <h3 class="title-quote size-16px">Quote info</h3>
            </div>
            <div class="m-portlet__head-tools">
              <ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--right m-tabs-line-danger" role="tablist" style="border-bottom: none;">
                <li class="nav-item m-tabs__item" id="edit_li">
                  <a class="btn btn-primary-v2" id="edit-quote" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                    <i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;Edit
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <div class="m-portlet__body">
            <div class="tab-content">
              <div class="row">
                <div class="col-md-4">
                  <label class="title-quote"><b>Quotation ID:&nbsp;&nbsp;</b></label>
                  <input type="text" value="{{$quote->quote_id}}" class="form-control quote_id" hidden >
                  <span class="quote_id_span">{{$quote->quote_id}}</span>
                  <!--<a href="#" id="quote_id" class="editable" data-tpl="<input type='text' style='width:100px;'>" data-type="text" data-value="{{$quote->quote_id}}" data-pk="{{$quote->id}}" data-title="Quote id">{{$quote->quote_id}}</a>-->
                </div>
                <div class="col-md-4">
                  <label class="title-quote"><b>Type:&nbsp;&nbsp;</b></label>
                  <input type="text" value="{{$quote->quote_id}}" class="form-control" hidden >
                  {{ Form::select('type',['FCL'=>'FCL','LCL'=>'LCL'],$quote->type,['class'=>'form-control type','hidden','']) }}
                  <span class="type_span">{{$quote->type}}</span>
                  <!--<a href="#" id="type" class="editable" data-source="[{value: 'FCL', text: 'FCL'},{value: 'LCL', text: 'LCL'}]" data-type="select" data-value="{{$quote->type}}" data-pk="{{$quote->id}}" data-title="Select type"></a>-->
                </div>
                <div class="col-md-4">
                  <label class="title-quote"><b>Company:&nbsp;&nbsp;</b></label>
                  {{ Form::select('company_id',$companies,$quote->company_id,['class'=>'form-control company_id','hidden','']) }}
                  <span class="company_span">{{$quote->company->business_name}}</span>
                  <!--<a href="#" id="company" class="editable" data-type="select" data-value="{{$quote->company_id}}" data-pk="{{$quote->id}}" data-title="Select type"></a>-->
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <label class="title-quote"><b>Status:&nbsp;&nbsp;</b></label>
                  {{ Form::select('status',['Draft'=>'Draft','Win'=>'Win','Sent'=>'Sent'],$quote->status,['class'=>'form-control status','hidden','']) }}
                  <span class="status_span Status_{{$quote->status}}" style="border-radius: 10px;">{{$quote->status}} <i class="fa fa-check"></i></span>
                  <!--<a href="#" id="status" class="editable Status_{{$quote->status}}" data-source="[{value: 'Draft', text: 'Draft'},{value: 'Win', text: 'Win'},{value: 'Sent', text: 'Sent'}]" data-value="{{$quote->status}}" data-type="select" data-pk="{{$quote->id}}" data-title="Select type" style="border-radius: 10px;">{{$quote->status}} <i class="fa fa-check"></i></a>-->
                </div>
                <div class="col-md-4">
                  <label class="title-quote"><b>Destination type:&nbsp;&nbsp;</b></label>
                  {{ Form::select('status',[1=>'Port to Port',2=>'Port to Door',3=>'Door to Port',4=>'Door to Door'],$quote->delivery_type,['class'=>'form-control delivery_type','hidden','']) }}
                  <span class="delivery_type_span">{{$quote->delivery_type}}</span>
                  <!--<a href="#" id="delivery_type" class="editable" data-source="[{value: '1', text: 'Port to Port'},{value: '2', text: 'Port to Door'},{value: '3', text: 'Door to Port'},{value: '4', text: 'Door to Door'}]" data-type="select" data-value="{{$quote->delivery_type}}" data-pk="{{$quote->id}}" data-title="Select delivery type"></a>-->
                </div>
                <div class="col-md-4">
                  <label class="title-quote"><b>Contact:&nbsp;&nbsp;</b></label>
                  {{ Form::select('contact_id',[],$quote->contact_id,['class'=>'form-control contact_id','hidden']) }}
                  <span class="contact_id_span">{{$quote->contact->first_name}} {{$quote->contact->last_name}}</span>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <label class="title-quote"><b>Date issued:&nbsp;&nbsp;</b></label>
                  <span class="date_issued_span">{{$quote->created_at}}</span>
                  <!--<a href="#" id="created_at" data-type="date" data-pk="{{$quote->id}}" data-title="Select date">{{date_format($quote->created_at, 'd M Y')}}</a>-->
                </div>
                <div class="col-md-4">
                  <label class="title-quote"><b>Equipment:&nbsp;&nbsp;</b></label>

                </div>
                <div class="col-md-4">
                  <label class="title-quote"><b>Price level:&nbsp;&nbsp;</b></label>
                  <span class="price_level_span">{{$quote->price->name}}</span>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <label class="title-quote"><b>Validity:&nbsp;&nbsp;</b></label>
                  <span class="validity_span">{{$quote->validity_since}} / {{$quote->validity_until}}</span>
                </div>
                <div class="col-md-4">
                  <label class="title-quote"><b>Incoterm:&nbsp;&nbsp;</b></label>
                {{ Form::select('incoterm_id',$incoterms,$quote->incoterm_id,['class'=>'form-control incoterm_id','hidden','']) }}
                  <span class="incoterm_id_span">{{$quote->incoterm->name}}</span>
                  <!--<a href="#" id="incoterm_id" class="editable" data-source="[{value: '1', text: 'EWX'},{value: '2', text: 'FAS'},{value: '3', text: 'FCA'},{value: '4', text: 'FOB'},{value: '5', text: 'CFR'},{value: '6', text: 'CIF'},{value: '7', text: 'CIP'},{value: '8', text: 'DAT'},{value: '9', text: 'DAP'},{value: '10', text: 'DDP'}]" data-type="select" data-value="{{$quote->incoterm_id}}" data-pk="{{$quote->id}}" data-title="Select delivery type"></a>-->
                </div>
                <div class="col-md-4">
                  <label class="title-quote"><b>Owner:&nbsp;&nbsp;</b></label>
                  {{$quote->user->name}} {{$quote->user->lastname}}
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 text-center" id="update_buttons" hidden>
                  <hr>
                  <br>
                  <a class="btn btn-danger" id="cancel" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                    <i class="fa fa-close"></i>&nbsp;&nbsp;&nbsp;Cancel
                  </a>
                  <a class="btn btn-primary" id="update" data-toggle="tab" href="#m_portlet_tab_1_1" role="tab">
                    <i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;Update
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-10 offset-md-1">
        <div class="m-portlet">
          <div class="m-portlet__body">
            <div class="tab-content">

            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

@endsection

@section('js')
  @parent
  <script src="{{asset('js/base.js')}}" type="text/javascript"></script>
  <script src="{{asset('js/quotes-v2.js')}}" type="text/javascript"></script>
  <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
  <script src="/js/quote.js"></script>
  <script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-touchspin.js" type="text/javascript"></script>
  <script src="/assets/demo/default/custom/components/forms/widgets/ion-range-slider.js" type="text/javascript"></script>
  <script src="/assets/demo/default/custom/components/base/dropdown.js" type="text/javascript"></script>
  <script src="/assets/demo/default/custom/components/datatables/base/html-table-quotesrates.js" type="text/javascript"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBCVgHV1pi7UVCHZS_wMEckVZkj_qXW7V0&libraries=places&callback=initAutocomplete" async defer></script>
  <script>



    /*** GOOGLE MAPS API ***/

    var autocomplete;
    function initAutocomplete() {
      var geocoder = new google.maps.Geocoder();
      var autocomplete = new google.maps.places.Autocomplete((document.getElementById('origin_address')));
      var autocomplete_destination = new google.maps.places.Autocomplete((document.getElementById('destination_address')));
      //autocomplete.addListener('place_changed', fillInAddress);
    }

    function codeAddress(address) {
      var geocoder;
      geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == 'OK') {
          alert(results[0].geometry.location);
        } else {
          alert('Geocode was not successful for the following reason: ' + status);
        }
      });
    }

    $valor =   $('#date_hidden').val();

    if($valor != 0){
      $('#m_datepicker_2').val($valor);
    }
    function setdateinput(){
      var date = $('#m_datepicker_2').val();
      $('#date_hidden').val(date);
    }

    function AbrirModal(action,id){

      if(action == "add"){
        var url = '{{ route("companies.addM") }}';
        $('#modal-body').load(url,function(){
          $('#companyModal').modal({show:true});
        });
      }
      if(action == "addContact"){
        var url = '{{ route("contacts.addCM") }}';
        $('.modal-body').load(url,function(){
          $('#contactModal').modal({show:true});
        });
      }

    }

    $(function() {
      $('#company').editable({
        type: 'select2',
        url: '/post',
        pk: 1,
        onblur: 'submit',
        emptytext: 'None',
        select2: {
          placeholder: 'Select a Requester',
          allowClear: true,
          width: '50px',
          minimumInputLength: 3,
          id: function (e) {
            return e.EmployeeId;
          },
          ajax: {
            url: '/EmployeeSearch',
            dataType: 'json',
            data: function (term, page) {
              return { query: term };
            },
            results: function (data, page) {
              return { results: data };
            }
          },
          formatResult: function (employee) {
            return employee.EmployeeName;
          },
          formatSelection: function (employee) {
            return employee.EmployeeName;
          },
          initSelection: function (element, callback) {
            return $.get('/EmployeeLookupById', { query: element.val() }, function (data) {
              callback(data);
            }, 'json'); //added dataType
          }
        }
      });
    });
  </script>
@stop