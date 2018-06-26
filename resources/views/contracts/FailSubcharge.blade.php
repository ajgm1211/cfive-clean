@extends('layouts.app')
@section('title', 'Contracts')
@section('content')

<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Rates Failed
                    </h3><br>

                </div>
            </div>
        </div>
        @if (count($errors) > 0)
        <div id="notificationError" class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if(Session::has('message.nivel'))

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
        @endif

        <div class="m-portlet__body">
            <div class="m-portlet__head">
                <label >
                    <i class="fa fa-dot-circle-o" style="color:red;"> </i>
                    <strong >
                        Rates Failed: 
                    </strong>
                    <strong id="strfail">{{$countfailrates}}</strong>
                    <input type="hidden" value="{{$countfailrates}}" id="strfailinput" />
                </label>
                <br>
                <label>
                    <i class="fa fa-dot-circle-o" style="color:green;"> </i>
                    <strong id="">
                        Good Rates: 
                    </strong>
                    <strong id="strgood">
                        {{$countrates}}
                    </strong>
                    <input type="hidden" value="{{$countrates}}" id="strgoodinput" />
                </label>
            </div>
            <!--<button onclick="prueba()">prueba</button>-->
            <table class="m-datatable "  id="html_table" >
                <thead >
                    <tr>
                        <th >
                            Status
                        </th>
                        <th >
                            Surcharge
                        </th>
                        <th >
                            Origin
                        </th>
                        <th >
                            Destiny
                        </th>
                        <th >
                            Type Destiny
                        </th>
                        <th >
                            Type Calculation 
                        </th>
                        <th >
                            Ammount
                        </th>
                        <th >
                            Currency
                        </th>
                        <th >
                            Carrier
                        </th>
                        <th >
                            Options
                        </th>

                    </tr>
                </thead>
                <tbody>
                    @php
                    $i=1 
                    @endphp
                    @foreach($failsurcharge as $surchargef)
                    <tr class="" id="{{'trR'.$i}}">
                        <td>
                            <i class="fa fa-dot-circle-o {{'icon'.$i}}" style="color:red;" id="" ></i>
                        </td>
                        <td>
                            <div class="{{'tdAB'.$i}}">
                                <label class="{{'lb'.$i}}" id="{{'carrierlb'.$i}}" style="{{$surchargef['classsurcharge']}}">
                                    {{$surchargef['surcharge_id']}}
                                </label>
                            </div>
                            <div class="in {{'tdIn'.$i}}" hidden>
                                {{ Form::select('surcharge_id', $subchargeSelect,$surchargef['surcharge_id'],['id' =>'carrier'.$i,'class'=>'m-select2-general form-control lb'.$i,'style' => $surchargef['classcarrier']]) }}
                            </div>
                        </td>
                        <td>
                            <div class="{{'tdAB'.$i}}">
                                <label class="{{'lb'.$i}}" id="{{'originlb'.$i}}" style="{{$surchargef['classorigin']}}">
                                    {{$surchargef['origin_portLb']}}
                                </label>
                            </div>
                            <div class="in {{'tdIn'.$i}}" hidden>
                                {{ Form::select('origin_port', $harbor,$surchargef['origin_port'],['class'=>'custom-select m-input form-control lb'.$i,'style'=>$surchargef['classorigin'],'id'=>'origin'.$i]) }}
                            </div>
                        </td>
                        <td>
                            <div class="{{'tdAB'.$i}}">
                                <label class="{{'lb'.$i}}" id="{{'destinylb'.$i}}" style="{{$surchargef['classdestiny']}}">
                                    {{$surchargef['destiny_portLb']}}
                                </label>
                            </div>
                            <div class="in {{'tdIn'.$i}}" hidden>
                                {{ Form::select('destiny_port', $harbor,$surchargef['destiny_port'],['class'=>'custom-select m-input form-control lb'.$i,'style'=>$surchargef['classdestiny'],'id'=>'destination'.$i]) }}

                            </div>
                        </td>
                        <td>
                            <div class="{{'tdAB'.$i}}">
                                <label class="{{'lb'.$i}}" id="{{'carrierlb'.$i}}" style="{{$surchargef['classcarrier']}}">{{$surchargef['carrierLb']}}
                                </label>
                            </div>
                            <div class="in {{'tdIn'.$i}}" hidden>
                                {{ Form::select('carrier_id', $carrierSelect,$surchargef['carrierAIn'],['id' =>'carrier'.$i,'class'=>'m-select2-general form-control lb'.$i,'style' => $surchargef['classcarrier']]) }}
                            </div>
                        </td>
                        <td>
                            <div class="{{'tdAB'.$i}}">
                                <label class="{{'lb'.$i}}" id="{{'twuentylb'.$i}}" style="{{$surchargef['classtwuenty']}}">
                                    {{$surchargef['twuenty']}}
                                </label>
                            </div>
                            <div class="in {{'tdIn'.$i}}" hidden>
                                <input type="text" style="{{$surchargef['classtwuenty']}}" name="twuenty" id="{{'twuenty'.$i}}" value="{{$surchargef['twuenty']}}" class="form-control m-input {{'lb'.$i}}"> 
                            </div>
                        </td>
                        <td>
                            <div class="{{'tdAB'.$i}}">
                                <label class="{{'lb'.$i}}" id="{{'fortylb'.$i}}" style="{{$surchargef['classforty']}}">
                                    {{$surchargef['forty']}}
                                </label>
                            </div>
                            <div class="in {{'tdIn'.$i}}" hidden>
                                <input type="text" style="{{$surchargef['classforty']}}" name="forty" id="{{'forty'.$i}}" value="{{$surchargef['forty']}}" class="form-control m-input {{'lb'.$i}}"> 
                            </div>
                        </td>
                        <td>
                            <div class="{{'tdAB'.$i}}">
                                <label class="{{'lb'.$i}}" id="{{'fortyhclb'.$i}}" style="{{$surchargef['classfortyhc']}}">
                                    {{$surchargef['fortyhc']}}
                                </label>
                            </div>
                            <div class="in {{'tdIn'.$i}}" hidden>
                                <input type="text" style="{{$surchargef['classfortyhc']}}" name="fortyhc" id="{{'fortyhc'.$i}}" value="{{$surchargef['fortyhc']}}" class="form-control m-input {{'lb'.$i}}"> 
                            </div>
                        </td>
                        <td>
                            <div class="{{'tdAB'.$i}}">
                                <label class="{{'lb'.$i}}" id="{{'currencylb'.$i}}" style="{{$surchargef['classcurrency']}}">
                                    {{$surchargef['currency_id']}}
                                </label>
                            </div>
                            <div class="in {{'tdIn'.$i}}" hidden="hidden">
                                {{ Form::select('currency_id', $currency,$surchargef['currencyAIn'],['class'=>'custom-select m-input form-control lb'.$i,'style'=>$surchargef['classcurrency'],'id'=>'currency'.$i]) }}
                            </div>
                        </td>
                        <td>
                            <a  class=" {{'tdBTU'.$i}} m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill {{'tdAB'.$i}}" onclick="showbox({{$i}})" title="Edit ">
                                <i class="la la-edit"></i>
                            </a>

                            <a class=" {{'tdAB'.$i}} {{'tdBTU'.$i}} DestroyRate m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Delete " onclick="DestroyRate({{$i}},{{$surchargef['surcharge_id']}})" >
                                <i class="la 	la-remove"></i>
                            </a>

                            <a  hidden class=" {{'tdIn'.$i}} m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Close " onclick="hidebox({{$i}})" >
                                <i class="la 	la-remove"></i>
                            </a>
                            <a  hidden class=" {{'tdIn'.$i}} m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Save " onclick="SaveCorrectRate({{$i}},{{$surchargef['surcharge_id']}},{{$surchargef['contract_id']}})" >
                                <i class="la la-save"></i>
                            </a>
                            <input type="hidden" name="define" value="1" id="{{'accion'.$i}}" />

                        </td>

                    </tr>

                    @php
                    $i++
                    @endphp
                    @endforeach

                    
                </tbody>
            </table>
            <!--begin: Search Form -->
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">


                </div>
            </div>
        </div>
    </div>




</div>

@endsection
@section('js')
@parent
<script src="/assets/demo/default/custom/components/datatables/base/html-table-surcharge.js" type="text/javascript"></script>
<script>
    function showbox(id){
        $(".tdAB"+id).attr('hidden','hidden');
        $(".tdIn"+id).removeAttr('hidden');
    }

    function hidebox(id){
        $(".tdIn"+id).attr('hidden','hidden');
        $(".tdAB"+id).removeAttr('hidden');
    }

    function SaveCorrectRate(idtr,idrate,idcontract){
        //alert('tdIn'+idtr+' '+idrate);
        var origin = $("#origin"+idtr).val();
        var destination = $("#destination"+idtr).val();
        var carrier = $("#carrier"+idtr).val();
        var twuenty = $("#twuenty"+idtr).val();
        var forty = $("#forty"+idtr).val();
        var fortyhc = $("#fortyhc"+idtr).val();
        var currency = $("#currency"+idtr).val();
        var accion = $("#accion"+idtr).val();
        //alert('A.'+origin+' B.'+ destination+' C.'+ carrier+' D.'+ twuenty+' E.'+ forty+' F.'+fortyhc +' G.'+ currency);
        if(accion == 1){
            jQuery.ajax({
                method:'get',
                data:{surcharge_id:idrate,
                      contract_id:idcontract,
                      origin:origin,
                      destination:destination,
                      carrier:carrier,
                      twuenty:twuenty,
                      forty:forty,
                      fortyhc:fortyhc,
                      currency:currency,
                     },
                url:'/contracts/CorrectedsurchargeforContracts',
                success:function(data){
                    //console.log(data);
                    if(data.response == 0){
                        //campo errado
                        swal("Error!", "wrong field in the rate!", "error");
                    }
                    else if(data.response == 1){
                        //exito
                        swal("Good job!", "Updated rate!", "success");
                        $(".icon"+idtr).attr('style','color:green');
                        $(".lb"+idtr).removeAttr('style');
                        hidebox(idtr);
                        var a = $('#strfailinput').val();
                        var b = $('#strgoodinput').val();
                        a--;
                        b++;
                        $('#strfail').text(a);
                        $('#strgood').text(b);
                        $('#strfailinput').attr('value',a);
                        $('#strgoodinput').attr('value',b);

                        $('#originlb'+idtr).text(data.origin);
                        $('#destinylb'+idtr).text(data.destiny);
                        $('#carrierlb'+idtr).text(data.carrier);
                        $('#twuentylb'+idtr).text(data.twuenty);
                        $('#fortylb'+idtr).text(data.forty);
                        $('#fortyhclb'+idtr).text(data.fortyhc);
                        $('#currencylb'+idtr).text(data.currency);

                        $("#accion"+idtr).attr('value',2);

                    }
                    else if(data.response == 2){
                        //duplicado
                        swal("Error!", "Alrready Rate!", "warning");
                    }

                }
            });
        }
        else if( accion == 2){
            // para actualizar campos
            jQuery.ajax({
                method:'get',
                data:{surcharge_id:idrate,
                      contract_id:idcontract,
                      origin:origin,
                      destination:destination,
                      carrier:carrier,
                      twuenty:twuenty,
                      forty:forty,
                      fortyhc:fortyhc,
                      currency:currency,
                     },
                url:'/contracts/UpdateRatesForContracts',
                success:function(data){
                    //console.log(data);
                    if(data.response == 0){
                        //campo errado
                        swal("Error!", "wrong field in the rate!", "error");
                    }
                    else if(data.response == 1){
                        //exito
                        swal("Good job!", "Updated rate!", "success");

                        hidebox(idtr);

                        $('#originlb'+idtr).text(data.origin);
                        $('#destinylb'+idtr).text(data.destiny);
                        $('#carrierlb'+idtr).text(data.carrier);
                        $('#twuentylb'+idtr).text(data.twuenty);
                        $('#fortylb'+idtr).text(data.forty);
                        $('#fortyhclb'+idtr).text(data.fortyhc);
                        $('#currencylb'+idtr).text(data.currency);
                        $("#accion"+idtr).attr('value',2);

                    }
                    else if(data.response == 2){
                        //duplicado
                        swal("Error!", "Alrready Rate!", "warning");
                    }

                }
            });
        }
        //alert(idcontract);
    }

    function DestroyRate(idtr,idrate){
        var accion = $('#accion'+idtr).val();

        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then(function(result) {
            if (result.value) {


                jQuery.ajax({
                    method:'get',
                    data:{
                        surcharge_id:idrate,
                        accion:accion
                    },
                    url:'/contracts/DestroyRatesFailCorrectForContracts',
                    success:function(data){
                        if(data == 1){
                            swal("Good job!", "Deletion fail rate!", "success");
                            var a = $('#strfailinput').val();
                            a--;
                            $('#strfail').text(a);
                            $('#strfailinput').attr('value',a);

                        }
                        else if( data == 2){
                            swal("Good job!", "Deletion rate!", "success");
                            var b = $('#strgoodinput').val();
                            b--;
                            $('#strgoodinput').attr('value',b);
                            $('#strgood').text(b);
                        }
                        $('.tdBTU'+idtr).attr('hidden','hidden');
                        $('.icon'+idtr).attr('style','color:gray');

                        $('#originlb'+idtr).attr('style','color:red');
                        $('#destinylb'+idtr).attr('style','color:red');
                        $('#carrierlb'+idtr).attr('style','color:red');
                        $('#twuentylb'+idtr).attr('style','color:red');
                        $('#fortylb'+idtr).attr('style','color:red');
                        $('#fortyhclb'+idtr).attr('style','color:red');
                        $('#currencylb'+idtr).attr('style','color:red');
                    }
                });
            }
        });
        /* idtr--;
        var myTable = $('#html_table');
        myTable.find( 'tbody tr:eq('+idtr+')' ).remove();
        alert(idtr+' '+accion);*/


    }

    function prueba(){
        idtr=1;
        var a = $("#origin"+idtr).val();
        //var ab = $("#origin"+idtr).val('value',12);
        var ac = $("#originlb"+idtr).attr('value',a);
        alert(a);

    }

</script>

@stop
