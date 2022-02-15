
<div class="m-portlet">
    <!--begin::Form-->
    {{ Form::model($contract, array('route' => array('contract.duplicated.store', $contract->id), 'method' => 'post', 'id' => 'frmRates')) }}


    <div class="m-portlet__body">
        <div class="form-group m-form__group row"> 
            <div class="col-lg-6">
                {!! Form::label('reference', 'Reference') !!}
                {{ Form::text('reference',$contract->name,['id' => 'reference','class'=>' form-control', 'style' => 'width:100%;']) }} 
            </div>
            <div class="col-lg-6">
                {!! Form::label('Carriers', 'Carriers') !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('carrier_id[]', $carrier,$contract->carriers->pluck('carrier_id'),['id' => 'localcarrier','class'=>'m-select2-general form-control','multiple' => 'multiple']) }}

                </div>

            </div>
        </div>
        <div class="form-group m-form__group row">
            <input type="hidden" name="company_user_id" value="{{$contract->company_user_id}}" >
            <div class="col-lg-6">
                {!! Form::label('validation_expire', 'Validation') !!}
                {!! Form::text('validation_expire', $contract->validity.' / '.$contract->expire, ['placeholder' => 'Please enter validation  date','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}

            </div>
            <div class="col-lg-6">
                {!! Form::label('Direction', 'Direction') !!}
                <div class="m-input-icon m-input-icon--right">
                    {{ Form::select('direction_id', $directions,$contract->direction_id,['id' => 'carrier','class'=>'m-select2-general form-control' ]) }}
                </div>
            </div>


        </div>

        <div class="m-portlet__foot m-portlet__foot--fit">
            <div class="m-form__actions m-form__actions">
                <br>
                {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
                <button class="btn btn-success" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">Cancel</span>
                </button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>  
</div>
<script src="/assets/demo/default/custom/components/forms/widgets/bootstrap-daterangepicker.js" type="text/javascript"></script>
<!--end::Form-->
<script>


    $('.m-select2-general').select2({
        placeholder: "Select an option"
    });


</script>
