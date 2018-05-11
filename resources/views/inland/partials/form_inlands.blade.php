<div class="m-portlet__body">
    <div class="form-group m-form__group row">
        <div class="col-lg-3">
            {!! Form::label('name', 'Provider') !!}
            {!! Form::text('name', null, ['placeholder' => 'Please enter the contract name','class' => 'form-control m-input','required' => 'required']) !!}
        </div>
        <div class="col-lg-3">
            {!! Form::label('ports', 'Port') !!}
            {{ Form::select('irelandports[]', $harbor,null,['class'=>'m-select2-general form-control port','multiple' => 'multiple']) }}
        </div>
        <div class="col-lg-4">
            {!! Form::label('validation_expire', 'Validation') !!}
            {!! Form::text('validation_expire', $validation_expire, ['placeholder' => 'Please enter validation  date','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}
        </div>
        <div class="col-lg-2">
      
                {!! Form::label('change', 'Change Type') !!}<br>
                {{ Form::select('status',['1' => 'Export','2' => 'Import'],null,['class'=>'m-select2-general form-control']) }}

       
        </div>
    </div>
</div>

