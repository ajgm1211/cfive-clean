<?php $__env->startSection('css'); ?>
##parent-placeholder-2f84417a9e73cead4d5c99e05daff2a534b30132##
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="/assets/datatable/jquery.dataTables.css">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('title', 'Global Charges'); ?>
<?php $__env->startSection('content'); ?>



<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Global Charges
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
                                List Global Charge
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
                                        <button id="new" type="button"  onclick="AbrirModal('addGlobalCharge',0)" class="new btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                            Add New &nbsp;
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </a>
                                    <a href="<?php echo e(route('RequestsGlobalchargersFcl.create')); ?>">

                                        <button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
                                            <span>
                                                <span>
                                                    New Import Contract &nbsp;
                                                </span>
                                                <i class="la la-clipboard"></i>
                                            </span>
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
                                    <!--<a href="<?php echo e(route('RequestsGlobalchargersFcl.indexListClient')); ?>">

<button type="button" class="btn btn-primary m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill" >
<span>
<span>
New \ Status Import  &nbsp;
</span>
<i class="la la-clipboard"></i>
</span>
</button>
</a>-->
                                </div>

                            </div>
                        </div>
                        <table class="table m-table m-table--head-separator-primary"  id="requesttable" width="100%" style="width:100%">
                            <thead>
                                <tr>
                                    <th>
                                        Select
                                    </th>
                                    <th>
                                        Type
                                    </th>
                                    <th>
                                        Origin Port
                                    </th>
                                    <th>
                                        Destination Port
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
                        Global Charges
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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
##parent-placeholder-93f8bb0eb2c659b85694486c41717eaf0fe23cd4##



<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>

<script>
    function AbrirModal(action,id){


        if(action == "editGlobalCharge"){
            var url = '<?php echo e(route("edit-global-charge", ":id")); ?>';
            url = url.replace(':id', id);
            $('#global-body').load(url,function(){
                $('#global-modal').modal({show:true});
            });

        }
        if(action == "addGlobalCharge"){
            var url = '<?php echo e(route("add-global-charge")); ?>';

            $('#global-body').load(url,function(){
                $('#global-modal').modal({show:true});
            });

        }
        if(action == "duplicateGlobalCharge"){

            var url = '<?php echo e(route("duplicate-global-charge", ":id")); ?>';
            url = url.replace(':id', id);
            $('#global-body').load(url,function(){
                $('#global-modal').modal({show:true});
            });
        }

    }

    $(function() {
        $('#requesttable').DataTable({
            processing: true,
            //serverSide: true,
            ajax: '<?php echo route("globalcharges.show",$company_userid); ?>',
            columns: [
                { data: 'checkbox', orderable:false, searchable:false},
                { data: 'surchargelb', name: 'surchargelb' },
                { data: 'origin_portLb', name: 'origin_portLb' },
                { data: 'destiny_portLb', name: 'destiny_portLb' },
                { data: 'typedestinylb', name: 'typedestinylb' },
                { data: 'calculationtypelb', name: 'calculationtypelb' },
                { data: 'currencylb', name: 'currencylb' },
                { data: 'carrierlb', name: 'carrierlb' },
                { data: 'ammount', name: 'ammount' },
                { data: 'validitylb', name: 'validitylb' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            "order": [[0, 'des']],
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "width": true,
            "info": true,
            "deferLoading": 57,
            "autoWidth": false,
            "stateSave": true,
            "processing": true,
            "dom": 'Bfrtip',
            "paging": true
        });

    });

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
                    url='<?php echo route("globalcharges.destroyArr",":id"); ?>';
                    url = url.replace(':id', id);
                    var token = $("meta[name='csrf-token']").attr("content");
                    $.ajax({
                        url:url,
                        method:"post",
                        data:{id:id,_token:token},
                        success:function(data)
                        {
                            if(data.success == 1){
                                swal(
                                    'Deleted!',
                                    'Your GloblaChargers has been deleted.',
                                    'success'
                                );
                                $('#requesttable').DataTable().ajax.reload();
                            }else if(data == 2){
                                swal("Error!", "an internal error occurred!", "error");
                            }
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
<?php if(session('globalchar')): ?>
<script>
    swal(
        'Done!',
        'GlobalCharge updated.',
        'success'
    )
</script>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>