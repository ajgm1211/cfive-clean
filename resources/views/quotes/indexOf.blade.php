@extends('layouts.app')
@section('title', 'Quotes')
@section('content')
<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
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
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-6 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">
                            <div class="col-md-4">
                                <div class="m-input-icon m-input-icon--left">
                                    <input type="text" class="form-control m-input" placeholder="Search..." id="generalSearch">
                                    <span class="m-input-icon__icon m-input-icon__icon--left">
                                        <span>
                                            <i class="la la-search"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 order-1 order-xl-2 m--align-right">
                        <a href="{{route('quotes.automatic')}}" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
                            <span>
                                <span>
                                    Search Rates
                                </span>
                                <i class="la la-plus"></i>
                            </span>
                        </a>
                        <a href="{{route('quotes.create')}}" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill">
                            <span>
                                <span>
                                    Manual Quote
                                </span>
                                <i class="la la-plus"></i>
                            </span>
                        </a>
                        <div class="m-separator m-separator--dashed d-xl-none"></div>
                    </div>
                </div>
            </div>
            <table class="m-datatable text-center title-quote"  id="html_table" >
                <thead>
                    <tr class="title-quote">
                        <th title="Status">
                            Status
                        </th>
                        <th title="id">
                            Id
                        </th>
                        <th title="Client">
                            Client
                        </th>
                        <th title="Created">
                            Created
                        </th>
                        <th title="Owner">
                            Owner
                        </th>
                        <th title="Origin">
                            Origin
                        </th>
                        <th title="Destination">
                            Destination
                        </th>
                        <th title="Amount">
                            Amount
                        </th>
                        <th title="Markup">
                            Markup
                        </th>
                        <th title="Type">
                            Type
                        </th>
                        <th title="Options">
                            Options
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($quotes as $quote)
                    <tr>
                        <td><span class="{{$quote->status->name}}"  onclick="AbrirModal('change_status',{{$quote->id}})" style="cursor: pointer;">{{$quote->status->name }}</span></td>
                        @if(isset($quote->company))
                        <td>{{$quote->company_quote }}</td>
                        <td>{{$quote->company->business_name }}</td>
                        @else
                        <td>---</td>
                        @endif
                        <td>{{ date_format($quote->created_at, 'M d, Y H:i')}}</td>
                        <td>{!!$quote->user->name.' '.$quote->user->lastname!!}</td>
                        @if($quote->origin_harbor)
                        <td>{{$quote->origin_harbor->display_name }}</td>
                        @elseif($quote->origin_airport)
                        <td>{{$quote->origin_airport->name }}</td>
                        @else
                        <td>{{$quote->origin_address }}</td>
                        @endif
                        @if($quote->destination_harbor)
                        <td>{{$quote->destination_harbor->display_name }}</td>
                        @elseif($quote->destination_airport)
                        <td>{{$quote->destination_airport->name }}</td>
                        @else
                        <td>{{$quote->destination_address }}</td>
                        @endif
                        <td>{{$quote->sub_total_origin+$quote->sub_total_freight+$quote->sub_total_destination}} {{$quote->currencies->alphacode}}</td>
                        <td>{{$quote->total_markup_origin+$quote->total_markup_freight+$quote->total_markup_destination}} {{$quote->currencies->alphacode}}</td>
                        <td>
                            @if($quote->type==1)
                            <img src="{{asset('images/logo-ship-blue.svg')}}" class="img img-responsive" width="25"> 
                            @elseif($quote->type==2)
                            <img src="{{asset('images/logo-ship-blue.svg')}}" class="img img-responsive" width="25"> 
                            @else
                            <img src="{{asset('images/plane-blue.svg')}}" class="img img-responsive" width="21"> 
                            @endif
                        </td>
                        <td>
                            <a href="{{route('quotes.show',setearRouteKey($quote->id))}}" class=" m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Show ">
                                <i class="la la-eye"></i>
                            </a>
                            <a href="{{route('quotes.edit',setearRouteKey($quote->id))}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                                <i class="la la-edit"></i>
                            </a>
                            <a href="{{route('quotes.duplicate',setearRouteKey($quote->id))}}" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Duplicate ">
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
            <div class="modal fade" id="change_status_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">
                                Change status
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                            </button>
                        </div>
                        <div class="modal-body">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('quotes.partials.quotesModal');
@endsection

@section('js')
@parent
<script src="/assets/demo/default/custom/components/datatables/base/html-table-quotes.js" type="text/javascript"></script>
<script src="/assets/demo/default/custom/components/forms/widgets/select2.js" type="text/javascript"></script>
<script src="{{asset('js/base.js')}}" type="text/javascript"></script>
<script>
    function AbrirModal(action,id){
        if(action == "edit"){
            var url = '{{ route("quotes.edit", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#priceModal').modal({show:true});
            });
        }
        if(action == "delete"){
            var url = '{{ route("quotes.destroy", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#deletePriceModal').modal({show:true});
            });
        }
        if(action == "change_status"){
            var url = '{{ route("quotes.change_status", ":id") }}';
            url = url.replace(':id', id);
            $('.modal-body').load(url,function(){
                $('#change_status_modal').modal({show:true});
            });
        }
    }

    $(document).ready(function() {
        $('#select-origin--2').select2();
        $('#select-destination--2').select2();
    });
</script>
@stop
