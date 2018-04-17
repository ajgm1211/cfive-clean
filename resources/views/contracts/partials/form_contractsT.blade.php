<div class="m-portlet__body">
    <div class="form-group m-form__group row">
        <div class="col-lg-4">
            {!! Form::label('name', 'Contract Name') !!}
            {!! Form::text('name', null, ['placeholder' => 'Please enter the contract name','class' => 'form-control m-input','required' => 'required']) !!}
        </div>
        <div class="col-lg-4">
            {!! Form::label('number', 'Number') !!}
            {!! Form::text('number', null, ['placeholder' => 'Please enter contract number','class' => 'form-control m-input','required' => 'required']) !!}
        </div>
        <div class="col-lg-4">
            <div class="row">

                <div class="col-lg-4">

                    {!! Form::label('status', 'Status') !!}<br>
                    {{ Form::select('status',['1' => 'Publish','2' => 'Draft'],null,['class'=>'form-control m-select2-general','id' => 'm_select2_2_modal']) }}
                </div>
            </div>
        </div>
    </div>
    <div class="form-group m-form__group row">
        <div class="col-lg-4">
            {!! Form::label('validation_expire', 'Validation') !!}
            {!! Form::text('validation_expire', $validation_expire, ['placeholder' => 'Please enter validation  date','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}
        </div>
    </div>


</div>

