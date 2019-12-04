<div class="m-portlet">

    {!! Form::open(['route' => 'gclcladm.store','method' => 'POST','class' => 'form-group m-form__group']) !!}
    <div class="m-portlet__body">
        <div class="form-group m-form__group row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4">
                        <label>
                            {!! Form::label('Type Route', 'Type Route') !!}
                        </label>
                        <div class="m-radio-inline">
                            <label class="m-radio">
                                <input type="radio" id="rdrouteP" onclick="activarCountry('divport')" checked='true' name="typeroute" value="port"> Port
                                <span></span>
                            </label>
                            <label class="m-radio">
                                <input type="radio" id="rdrouteC" onclick="activarCountry('divcountry')"  name="typeroute" value="country"> Country
                                <span></span>
                            </label>
                        </div>
                    </div>
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
            <div class="col-lg-4">
                <label>
                    {!! Form::label('type', 'Type') !!}
                </label>
                {{ Form::select('type', $surcharge,null,['id' => 'type','class'=>'m-select2-general form-control ' ,'required' => 'true']) }}
            </div>
            <div class="col-lg-4">
                <div class="divport" >
                    {!! Form::label('orig', 'Origin Port') !!}
                    {{ Form::select('port_orig[]', $harbor,
                    null,['id' => 'port_orig','class'=>'m-select2-general form-control ','multiple' => 'multiple' , 'required' =>'true' ]) }}
                </div>
                <div class="divcountry" hidden="true">
                    {!! Form::label('origC', 'Origin Country') !!}
                    {{ Form::select('country_orig[]', $countries,
                    null,['id' => 'country_orig','class'=>'m-select2-general form-control col-lg-12','multiple' => 'multiple' ]) }}
                </div>
            </div>			
            <div class="col-lg-4">
                <div class="divport" >
                    {!! Form::label('dest', 'Destination Port') !!}
                    <div class="m-input-icon m-input-icon--right">
                        {{ Form::select('port_dest[]', $harbor,
                        null,['id' => 'port_dest','class'=>'m-select2-general form-control ','multiple' => 'multiple' , 'required' =>'true' ]) }}
                        <span class="m-input-icon__icon m-input-icon__icon--right">
                            <span>
                                <i class="la la-info-circle"></i>
                            </span>
                        </span>
                    </div>
                </div>
                <div class="divcountry" hidden="true" >
                    {!! Form::label('destC', 'Destination Country') !!}
                    {{ Form::select('country_dest[]',$countries,null,[ 'id' => 'country_dest','class'=>'m-select2-general form-control' ,'multiple' => 'multiple'   ]) }}
                </div>
            </div>
        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-4">
                {!! Form::label('typed', 'Destination type') !!}
                {{ Form::select('changetype',$typedestiny, null,['id' => 'changetype','class'=>'m-select2-general form-control' ,'required' => 'true']) }}
            </div>
            <div class="col-lg-4">
                {!! Form::label('validation_expire', 'Validation') !!}
                {!! Form::text('validation_expire', null, ['placeholder' => 'Contract Validity','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}
            </div>

            <div class="col-lg-4">
                {!! Form::label('calculationt', 'Calculation Type') !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('calculationtype[]', $calculationT,null,['id' => 'calculationtype','class'=>'m-select2-general form-control ' ,'required' => 'true','multiple' => 'multiple' ]) }}
                    <span class="m-input-icon__icon m-input-icon__icon--right">
                        <span>
                            <i class="la la-map-marker"></i>
                        </span>
                    </span>
                </div>

            </div>
        </div>
        <div class="form-group m-form__group row">

            <div class="col-lg-4">
                {!! Form::label('carrierL', 'Carrier') !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('localcarrier[]', $carrier,null,['id' => 'localcarrier','class'=>'m-select2-general form-control','multiple' => 'multiple' ,'required' => 'true']) }}
                    <span class="m-input-icon__icon m-input-icon__icon--right">
                        <span>
                            <i class="la la-info-circle"></i>
                        </span>
                    </span>
                </div>

            </div>

            <div class="col-lg-4">
                {!! Form::label('ammountL', 'Amount') !!}
                <div class="m-input-icon m-input-icon--right">
                    {!! Form::number('ammount', null, ['id' => 'ammount','placeholder' => 'Please enter the Ammount','class' => 'form-control m-input','required','required' => 'true','step'=>'0.01']) !!}
                    <span class="m-input-icon__icon m-input-icon__icon--right">
                        <span>
                            <i class="la la-bookmark-o"></i>
                        </span>
                    </span>
                </div>

            </div>
            <div class="col-lg-4">
                {!! Form::label('minimum', 'Minimum') !!}
                <div class="m-input-icon m-input-icon--right">
                    {!! Form::text('minimum', null, ['id' => 'minimum','placeholder' => 'Please enter the Minimum','class' => 'form-control m-input' ,'required' => 'true']) !!}
                    <span class="m-input-icon__icon m-input-icon__icon--right">
                        <span>
                            <i class="la la-bookmark-o"></i>
                        </span>
                    </span>
                </div>

            </div>
        </div>
        <div class="form-group m-form__group row">
            <div class="col-lg-4">
                {!! Form::label('currencyl', 'Currency') !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('localcurrency_id',$currency,$currency_cfg->id,['id' => 'localcurrency','class'=>'m-select2-general form-control' ,'required' => 'true' ]) }}
                </div>

            </div>

        </div>
    </div>  
    <br>
    <hr>
    <div class="m-portlet__foot m-portlet__foot--fit">
        <div class="m-form__actions m-form__actions">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
            <button class="btn btn-danger" type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">Cancel</span>
            </button>
        </div>
    </div>
    <input type="hidden" name="company_user_id_selec" value="{{$company_user_id_selec}}">
    <input type="hidden" name="carrier_id_selec" value="{{$carrier_id_selec}}">
    <input type="hidden" name="reload_DT" value="{{$reload_DT}}">
    {!! Form::close() !!}
</div>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<script src="/js/globalchargeslcl.js"></script>
<script>


    $('.m-select2-general').select2({
        placeholder: "Select an option"
    });


    $(document).ready(function(){
        var id = $('select[name=company_user_id]').val()
        url = '{{route("gclcladm.typeCharge",":id")}}';
        url = url.replace(':id', id);
        $.get(url,function(response,status){
            if(response.length >= 1){
                $('#type').removeAttr('disabled','disabled');
            } else {
                $('#type').attr('disabled','disabled');
            }
            $('#type').empty();
            for(i=0;i < response.length; i++){
                $('#type').append("<option value='"+response[i].id+"'>"+response[i].name+"</option>");
            }
        })
    });

    $('#company_user_id').change(function(event){
        url = '{{route("gclcladm.typeCharge",":id")}}';
        url = url.replace(':id', event.target.value);
        $.get(url,function(response,status){
            //console.log(response);
            if(response.length >= 1){
                $('#type').removeAttr('disabled','disabled');
            } else {
                $('#type').attr('disabled','disabled');
            }
            $('#type').empty();
            $('#type').append("<option value=''>Selecciona</option>");
            for(i=0;i < response.length; i++){
                $('#type').append("<option value='"+response[i].id+"'>"+response[i].name+"</option>");
            }
        })
    });

</script>
