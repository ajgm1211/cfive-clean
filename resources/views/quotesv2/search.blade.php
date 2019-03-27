@extends('layouts.app')
@section('css')
@parent
<link href="/css/quote.css" rel="stylesheet" type="text/css" />

<link href="/assets/plugins/datatables.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Quotes')
@section('content')
<br>

<div class="row">

  <div class="col-lg-1"></div>
  <div class="col-lg-10">
    {!! Form::open(['route' => 'quote.processSearch','class' => 'form-group m-form__group']) !!}
    <div class="m-portlet">
      <div class="m-portlet__body">
        <div class="tab-content">
          <div>
            <div class="row">

              <div class="col-lg-2">
                <label>Quote Type</label>
                {{ Form::select('type',['1' => 'FCL','2' => 'LCL'],null,['class'=>'m-select2-general form-control']) }}
              </div>
              <div class="col-lg-2">
                <label>Equipment</label>
                {{ Form::select('equipment[]',['20' => '20\'','40' => '40','40hc'=>'40HC','40nor'=>'40NOR','45'=>'45'],@$form['equipment'],['class'=>'m-select2-general form-control','id'=>'equipment','multiple' => 'multiple','required' => 'true']) }}
              </div>
              <div class="col-lg-2">
                <label>Company</label>

                <div class="m-input-icon m-input-icon--right">
                  {{ Form::select('company_id_quote', $companies,@$form['company_id_quote'],['class'=>'m-select2-general form-control','id' => 'm_select2_2_modal','required'=>'true']) }} 
                  <span class="m-input-icon__icon m-input-icon__icon--right">
                    <span>
                      <i class="la 	la-plus-circle" style="color:blue; font-size: 18px;"></i>
                    </span>
                  </span>
                </div>
              </div>
              <div class="col-lg-2">
                <label>Contact</label>
                <div class="m-input-icon m-input-icon--right">
                  {{ Form::select('contact_id',[],null,['id' => 'contact_id', 'class'=>'m-select2-general form-control','required'=>'true']) }}
                  {{  Form::hidden('contact_id_num', @$form['contact_id'] , ['id' => 'contact_id_num'  ])  }}
                  <span class="m-input-icon__icon m-input-icon__icon--right">
                    <span>
                      <i class="la 	la-plus-circle" style="color:blue; font-size: 18px;"></i>
                    </span>
                  </span>
                </div>
              </div>
              <div class="col-lg-2">
                <label>Price level</label>
                {{ Form::select('price_id',[],null,['id' => 'price_id' ,'class'=>'form-control']) }}
                {{  Form::hidden('price_id_num', @$form['price_id'] , ['id' => 'price_id_num'  ])  }}
              </div>
            </div><br>
            <div class="row">
              <div class="col-lg-2" id="origin_harbor_label">
                <label>Origin port</label>
                {{ Form::select('originport[]',$harbors,@$form['originport'],['class'=>'m-select2-general form-control','multiple' => 'multiple','id'=>'origin_harbor','required' => 'true']) }}
              </div>
              <div class="col-lg-2" id="destination_harbor_label">
                <label>Destination port</label>
                {{ Form::select('destinyport[]',$harbors,@$form['destinyport'],['class'=>'m-select2-general form-control','multiple' => 'multiple','id'=>'destination_harbor','required' => 'true']) }}
              </div>
              <div class="col-lg-2">
                <label>Date</label>
                <div class="input-group date">
                  {!! Form::text('date', @$form['date'], ['id' => 'm_daterangepicker_1' ,'placeholder' => 'Select date','class' => 'form-control m-input date' ,'required' => 'true','autocomplete'=>'off']) !!}
                  {!! Form::text('date_hidden', null, ['id' => 'date_hidden','hidden'  => 'true']) !!}

                  <div class="input-group-append">
                    <span class="input-group-text">
                      <i class="la la-calendar-check-o"></i>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-lg-2">
                <label>Delivery type</label>
                {{ Form::select('delivery_type',['1' => 'PORT(Origin) To PORT(Destination)','2' => 'PORT(Origin) To DOOR(Destination)','3'=>'DOOR(Origin) To PORT(Destination)','4'=>'DOOR(Origin) To DOOR(Destination)'],null,['class'=>'m-select2-general form-control','id'=>'delivery_type']) }}
              </div>
              <div class="col-lg-2 hide" id="origin_address_label">
                <label>Origin address</label>
                {!! Form::text('origin_address', '', ['placeholder' => 'Please enter a origin address','class' => 'form-control m-input','id'=>'origin_address']) !!}
              </div>
              <div class="col-lg-2 hide" id="destination_address_label">
                <label>Destination address</label>
                {!! Form::text('destination_address', '', ['placeholder' => 'Please enter a destination address','class' => 'form-control m-input','id'=>'destination_address']) !!}
              </div>

            </div>
            <br>
            <div class ="row">  <div class="col-lg-12"> <center><button type="submit" class="btn m-btn--pill    btn-info">Search</button></center> </div>  </div>
          </div>
        </div>      
      </div>
    </div>

    {!! Form::close() !!}
  </div>
  <div class="col-lg-1"></div>


</div>
@if(!empty($arreglo))
<div class="row" >
  <div class="col-sm-1"></div>
  <div class="col-lg-10">
    <div class="m-portlet">
      <div class="m-portlet__body">
        <div class="tab-content">
          <div>
            <div class="row">
              <div class="col-lg-6">
                <label>Sort By</label>
                <div class="input-group m-input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="la la-exclamation-triangle"></i></span>
                  </div>
                  <div >
                    <select class="form-control m-select2-general ">
                      <option value="AK">Asc</option>
                      <option value="AK">Desc</option>
                      <option value="AK">Minnor Price</option>
                      <option value="AK">Mayor Price</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-lg-6" align='right'><button  class="btn-large" disabled='true'> Quote

                </button></div>
            </div>
            <div class="row">
              <div class="col-lg-12"><hr></div>
            </div>
            <br><br>

            <div class="row"  >
              <div class="col-lg-2" >  <span class="portcss"> Carrier</span></div>
              <div class="{{ $equipmentHides['originClass']  }}"><span class="portcss">Origin</span></div>
              <div class="{{ $equipmentHides['destinyClass']  }}"><span class="portcss">Destination</span></div>
              <div class="col-lg-1"><span class="portcss">Validity</span></div>
              <div class="col-lg-1" {{ $equipmentHides['20'] }} ><span class="portcss">20'</span></div>
              <div class="col-lg-1" {{ $equipmentHides['40'] }} ><span class="portcss">40</span></div>
              <div class="col-lg-1" {{ $equipmentHides['40hc'] }} ><span class="portcss">40HC'</span></div>
              <div class="col-lg-1" {{ $equipmentHides['40nor'] }} ><span class="portcss">40NOR'</span></div>
              <div class="col-lg-1" {{ $equipmentHides['45'] }} ><span class="portcss">45'</span></div>
              <div class="col-lg-1" ></div>
            </div>
            <div class="row">
              <div class="col-lg-12"><br><br></div>
            </div>
            @foreach($arreglo as $arr)
            <div class="row" id='principal{{$loop->iteration}}' >
              <div class="col-lg-2">            
                <div class="m-widget5">
                  <div class="m-widget5__item">
                    <div class="m-widget5__pic"> 
                      <img src="{{ url('imgcarrier/'.$arr->carrier->image) }}" alt="" title="" />
                    </div>
                  </div>
                </div>
              </div>
              <div class="{{ $equipmentHides['dataOrigDest']  }}">
                <div class="row">
                  <div class="col-lg-4">
                    <span class="portcss"> {{$arr->port_origin->name  }}</span><br>
                    <span class="portalphacode"> {{$arr->port_origin->code  }}</span>
                  </div>
                  <div class="col-lg-4">
                    <div class="progress m-progress--sm">
                      <div class="progress-bar " role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div><br>
                    Contract: {{ $arr->contract->name }} / {{ $arr->contract->number }}
                  </div>
                  <div class="col-lg-4">
                    <span class="portcss"> {{$arr->port_destiny->name  }}</span><br>
                    <span class="portalphacode"> {{$arr->port_destiny->code  }}</span>
                  </div>
                </div>
                <br>
                <span class="workblue">Detail Cost</span>  <a  id='display_l{{$loop->iteration}}' onclick="display({{$loop->iteration}})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Cancel" >
                <i  class="la la-angle-down blue"></i>
                </a>

              </div>

              <div class="col-lg-1">
                <span class="portcss">{{   \Carbon\Carbon::parse($arr->contract->validity)->format('d M Y') }} - {{   \Carbon\Carbon::parse($arr->contract->expire)->format('d M Y') }}</span>    
              </div>
              <div class="col-lg-1" {{ $equipmentHides['20'] }} ><span class="currency">$USD</span> <span class="darkblue validate"> {{$arr->twuenty  }}</span></div>
              <div class="col-lg-1" {{ $equipmentHides['40'] }}> <span class="currency">$USD</span> <span class="darkblue validate"> {{$arr->forty  }}</span> </div>
              <div class="col-lg-1" {{ $equipmentHides['40hc'] }}> <span class="currency">$USD</span> <span class="darkblue validate">  {{$arr->fortyhc  }} </span></div>
              <div class="col-lg-1" {{ $equipmentHides['40nor'] }}><span class="currency">$USD</span>   <span class="darkblue validate"> {{$arr->fortynor  }} </span></div>
              <div class="col-lg-1" {{ $equipmentHides['45'] }}> <span class="currency">$USD</span>  <span class="darkblue validate"> {{$arr->fortyfive  }} </span></div>
              <div class="col-lg-1">
                <button type="button" class="btn btn-sm btn-default btn-bold btn-upper">Select</button>
              </div>
            </div>
            <!-- Gastos Freight-->
            <div class="row" id='freight{{$loop->iteration}}' hidden='true' >
              <div class="col-lg-12">
                <div class="row">
                  <span class="darkblue cabezeras">Freight</span><br><br>
                </div>
                <div class="row bg-light">
                  <div class="col-lg-2"><span class="portalphacode">Charge</span></div>
                  <div class="col-lg-3"><span class="portalphacode">Detail</span></div>
                  <div class="col-lg-2" {{ $equipmentHides['20'] }}><span class="portalphacode">20'</span></div>
                  <div class="col-lg-2" {{ $equipmentHides['40'] }}><span class="portalphacode">40'</span></div>
                  <div class="col-lg-1" {{ $equipmentHides['40hc'] }}><span class="portalphacode">40HC'</span></div>
                  <div class="col-lg-1"  {{ $equipmentHides['40nor'] }}><span class="portalphacode">40NOR'</span></div>
                  <div class="col-lg-1" {{ $equipmentHides['45'] }}><span class="portalphacode">45'</span></div>
                  <div class="col-lg-2" ><span class="portalphacode">Currency</span></div>
                </div><br>
                @foreach($arr->rates as $rates)
                <div class="row">
                  <div class="col-lg-2">{{ $rates['type'] }}</div>
                  <div class="col-lg-3">{{ $rates['detail'] }}</div>
                  <div class="col-lg-2" {{ $equipmentHides['20'] }}>{{ $rates['price20'] }} + {{ $rates['markup20'] }} <i class="la la-angle-left"></i> {{  $rates['monto20'] }}  </div>
                  <div class="col-lg-2" {{ $equipmentHides['40'] }}>{{ $rates['price40'] }} + {{ $rates['markup40'] }} <i class="la la-angle-left"></i> {{  $rates['monto40'] }}  </div>
                  <div class="col-lg-1" {{ $equipmentHides['40hc'] }}>{{ $rates['price20'] }} + {{ $rates['markup20'] }} <i class="la la-angle-left"></i> {{  $rates['monto20'] }}  </div>
                  <div class="col-lg-1" {{ $equipmentHides['40nor'] }}>{{ $rates['price20'] }} + {{ $rates['markup20'] }} <i class="la la-angle-left"></i> {{  $rates['monto20'] }}  </div>
                  <div class="col-lg-1" {{ $equipmentHides['45'] }}>{{ @$rates['price45'] }} + {{ @$rates['markup45'] }} <i class="la la-angle-left"></i> {{  @$rates['monto45'] }}  </div>
                  <div class="col-lg-2" >{{$rates['currency_rate']}}</div>
                </div><br>
                @endforeach
                <div class="row bg-light">
                  <div class="col-lg-5 col-lg-offset-" ><span class="portalphacode">Subtotal Freight Charges</span></div>
                  <div class="col-lg-2" {{ $equipmentHides['20'] }}><span class="portalphacode">684,00</span></div>
                  <div class="col-lg-2" {{ $equipmentHides['40'] }}><span class="portalphacode">684,00</span></div>
                  <div class="col-lg-1" {{ $equipmentHides['40hc'] }}><span class="portalphacode">684,00</span></div>
                  <div class="col-lg-1" {{ $equipmentHides['40nor'] }}><span class="portalphacode">684,00</span></div>
                  <div class="col-lg-1" {{ $equipmentHides['45'] }}><span class="portalphacode">684,00</span></div>
                  <div class="col-lg-2" ><span class="portalphacode">EUR</span></div>
                </div>

              </div>
            </div>
            <br><br>
            <div class="row"><div class="col-lg-12"><hr></div></div>
            <br><br>
            @endforeach       
          </div>

        </div>      
      </div>
    </div>
  </div>
  <div class="col-sm-1"></div>

</div>
@endif

@endsection

@section('js')
@parent


<script src="{{asset('js/quotes.js')}}" type="text/javascript"></script>
@if(empty($arreglo))
<script>

  $('select[name="contact_id"]').prop("disabled",true);
  $("select[name='company_id_quote']").val('');
  $('#select2-m_select2_2_modal-container').text('Please an option');
</script>
@else

<script>
  precargar()
</script>


@endif
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
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


  $('.m-select3-general').select2();

  $('.select2-selection__arrow').remove();



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
</script>
@stop
