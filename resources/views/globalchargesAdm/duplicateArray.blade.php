<div class="m-scrollable" data-scrollbar-shown="true" data-scrollable="true" data-max-height="200">

    {!! Form::open(['route' => 'gcadm.store.array', 'method' => 'post','class' => 'form-group m-form__group']) !!}
    <div class="m-portlet__body">
        <div class="form-group m-form__group row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-8">
                        {!! Form::label('company_user', 'Company User') !!}
                        <div class="m-input-icon m-input-icon--right">
                            {{ Form::select('company_user_id',$company_users,null,['id' => 'company_user_id','class'=>'m-select2-general form-control' ,'required' => 'true' ]) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group m-form__group row">

            <div class="col-lg-12">
                <table class="table m-table m-table--head-separator-primary examm"  id="load" >
                    <thead class="examm" width="100%">
                        <tr>
                            <th>Type</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Charge T</th>
                            <th>Calculation T</th>
                            <th>Currency</th>
                            <th>Carrier</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($global as $gb)
                        <tr>
                            <th>{{$gb['surcharge']}}</th>
                            <th>{{$gb['origin']}}</th>
                            <th>{{$gb['destination']}}</th>
                            <th>{{$gb['typedestiny']}}</th>
                            <th>{{$gb['calculationtype']}}</th>
                            <th>{{$gb['currency']}}</th>
                            <th>{{$gb['carrier']}}</th>
                            <th>{{$gb['ammount']}}</th>

                        </tr>

                        @endforeach
                    </tbody>

                </table>
                @foreach($globals_id_array as $gb)
                <input type="hidden" name="idArray[]" value="{{$gb}}">
                @endforeach
                <style>
                    .scrollStyle
                    {
                        overflow-x:auto;
                    }
                </style>
            </div>
        </div>
    </div>  
    <br>
    <div class="m-portlet__foot m-portlet__foot--fit">
        <br>
        <div class="m-form__actions m-form__actions">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
            <button class="btn btn-danger" type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">Cancel</span>
            </button>
        </div>
        <br>
    </div>
    {!! Form::close() !!}

</div>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/js/globalcharges.js"></script>
<script>

    $('.m-select2-general').select2({
        placeholder: "Select an option"
    });



    $(document).ready(function(){
        var id =[];
        $('input[name=array]').each(function(){
            id.push($(this).val());
        });
        // alert(id);

        $('#load').DataTable( {
            "scrollY":        "200px",
            "scrollCollapse": true,

            "paging":         false
        } ).columns.adjust().draw();

        setTimeout(function () {
            $('#load').DataTable().columns.adjust().draw();
        },200);
    });



</script>
