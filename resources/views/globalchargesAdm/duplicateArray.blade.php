<div class="m-portlet">
    {!! Form::open(['route' => 'gcadm.store', 'method' => 'post','class' => 'form-group m-form__group']) !!}
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
                <a href="#" onclick="show()"> dd</a>
                <table class="table m-table m-table--head-separator-primary"  id="requesttable" width="100%" style="width:100%">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Charge T</th>
                            <th>Calculationtype T</th>
                            <th>Currency</th>
                            <th>Carrier</th>
                            <th>Amount</th>
                            <th>Validity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($global as $gb)
                        <tr>
                            <th>{{ $gb->surcharge->name}}</th>
                            <th>{{ $gb->currency->alphacode}}</th>
                            <th>{{ $gb->currency->alphacode}}</th>
                            <th>{{ $gb->typedestiny->description}}</th>
                            <th>{{ $gb->calculationtype->name}}</th>
                            <th>{{ $gb->currency->alphacode}}</th>
                            <th>{{ str_replace(['[',']'],'',$gb['globalcharcarrier']->pluck('carrier')->pluck('name'))}}</th>
                            <th>{{ $gb->ammount}}</th>
                            <!--<th>{{ $gb->validity.'/'.$gb->expire}}</th>-->

                        </tr>
                        
                        @endforeach
                        @foreach($globals_id_array as $gb)
                        <input type="hidden" name="array" value="{{$gb}}">
                        @endforeach
                    </tbody>

                </table>
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
        alert(id);
    });


</script>
