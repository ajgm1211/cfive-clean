@extends('layouts.app')
@section('css')
@parent
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
<style>
  .btn-save__modal {
    padding: 15px 35px !important;
    border-radius: 50px;
    background-color: #36a3f7 !important;
    border-color: #36a3f7 !important;
    font-size: 18px;
  }
  .icon__modal {
    margin-right: 10px;
  }
</style>
@endsection
@section('title', 'Global Charges')
@section('content')



<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Global Charges API
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet m-portlet--tabs">
            <div class="m-portlet__head">
                <div class="m-portlet__head-tools">
                    <ul class="nav nav-tabs m-tabs-line m-tabs-line--primary m-tabs-line--2x" role="tablist">
                        <li class="nav-item m-tabs__item">
                            <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_tabs_6_1" role="tab">
                                <i class="la la-cog"></i>
                                Global Charge API List
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="m-portlet__body">
                <div class="tab-content">
                    <div class="tab-pane active" id="m_tabs_6_1" role="tabpanel">

                        <!--begin: Search Form -->
                        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                            <div class="row align-items-center">

                                <div class="new col-xl-12 order-1 order-xl-2 m--align-right">

                                    <div class="m-separator m-separator--dashed d-xl-none"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12 order-1 order-xl-2 m--align-right">
                                    <a  id="newmodal" class="">
                                        <button id="new" type="button"  onclick="OpenModal('addGlobalCharge',0)" class="new btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                            Add New &nbsp;
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </a>

                                    <button type="button" name="bulk_delete" id="bulk_delete" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                        <span>
                                            <span>
                                                Delete Selected &nbsp;
                                            </span>
                                            <i class="la la-trash"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <table class="table m-table m-table--head-separator-primary"  id="requesttable" width="100%" style="width:100%">
                            <thead>
                                <tr>
                                    <th>
                                        Selects
                                    </th>
                                    <th>
                                        Type
                                    </th>
                                    <th>
                                        Origin 
                                    </th>
                                    <th>
                                        Destination 
                                    </th>
                                    <th>
                                        Charge Type
                                    </th>
                                    <th>
                                        Calculationtype type
                                    </th>
                                    <th>
                                        Currency
                                    </th>
                                    <th>
                                        Carrier
                                    </th>
                                    <th>
                                        Amount
                                    </th>
                                    <th>
                                        Validity
                                    </th>
                                    <th>
                                        Options
                                    </th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($globalcharges as $globalcharge)
                                <tr>
                                    <td><input type="checkbox" name="check[]" class="checkbox_global" value="{!! $globalcharge->id !!}"></td>
                                    <td>{!! $globalcharge->surcharge->name !!}</td>
                                    <td> 
                                        @foreach ($globalcharge->globalcharport as $gcp)
                                            {!! $gcp->portOrig->display_name !!}
                                        @endforeach
                                    </td>
                                    <td> 
                                        @foreach ($globalcharge->globalcharport as $gcp)
                                            {!! $gcp->portDest->display_name !!}
                                        @endforeach
                                    </td>
                                    <td>{!! $globalcharge->typedestiny->description !!}</td>
                                    <td>{!! $globalcharge->calculationtype->name !!}</td>
                                    <td>{!! $globalcharge->currency->alphacode !!}</td>
                                    <td> 
                                        @foreach ($globalcharge->globalchargeprovider as $gcp)
                                            {!! $gcp->provider->name !!}
                                        @endforeach
                                    </td>
                                    <td>{!! $globalcharge->amount !!}</td>
                                    <td>{!! $globalcharge->validity !!}</td>
                                    <td>
                                        <a  id="edit_l" onclick="OpenModal('editGlobalCharge', {!! $globalcharge->id !!})" class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill"  title="Edit ">
                                        <i class="la la-edit"></i>
                                        </a>

                                        <!--<a   class="m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill test"  title="Duplicate "  onclick="OpenModal('duplicateGlobalCharge', {!! $globalcharge->id !!})">
                                            <i class="la la-plus"></i>
                                        </a>-->
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

    <div class="modal fade bd-example-modal-lg" id="global-modal"   role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                </div>
                <div id="global-body">

                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
@parent



<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script src="/assets/demo/default/custom/components/datatables/base/record-selection.js" type="text/javascript"></script>

<script>
    function OpenModal(action,id){

        if(action == "editGlobalCharge"){
            var url = '{{ route("globalchargesapi.edit", ":globalchargesapi") }}';
            console.log(url);
            url = url.replace(':globalchargesapi', id);
            $('#global-body').load(url,function(){
                $('#global-modal').modal({show:true});
            });
            $('#exampleModalLongTitle').text('Edit Global Charges API');

        }

        if(action == "addGlobalCharge"){
            var url = '{{ route("globalchargesapi.create")}}';

            $('#global-body').load(url,function(){
                $('#global-modal').modal({show:true});
            });
            $('#exampleModalLongTitle').text('New Global Charges API')

        }

    }

    $(document).on('click', '#bulk_delete', function(){
        var id = [];
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then(function(result){
            if (result.value) {
                var oTableT = $("#requesttable").dataTable();
                $('.checkbox_global:checked', oTableT.fnGetNodes()).each(function(){
                    id.push($(this).val());
                });

                if(id.length > 0)
                {
                    url='{!! route("globalchargesapi.destroyArr",":id") !!}';
                    url = url.replace(':id', id);
                    var token = $("meta[name='csrf-token']").attr("content");
                    $.ajax({
                        url:url,
                        method:"post",
                        data:{id:id,_token:token},
                        success:function(data)
                        {
                            location.reload(true);
                        },
                        error: function(){
                            swal("Error!", "An internal error occurred!", "error");
                        }
                    });
                }
                else
                {
                    swal("Error!", "Please select atleast one checkbox", "error");
                }
            } else if (result.dismiss === 'cancel') {
                swal(
                    'Cancelled',
                    'Your GloblaChargers is safe :)',
                    'error'
                )
            }
        });
    });

</script>
<script src="/js/globalcharges.js"></script>
@if(Session::has('globalcharge.msg'))
<script>
    swal(
        'Done!',
        '{!! Session::get("globalcharge.msg") !!}',
        'success'
    )
</script>
@endif

@stop
