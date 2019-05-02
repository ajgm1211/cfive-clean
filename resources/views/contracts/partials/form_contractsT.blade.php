<div class="form-group m-form__group row">
    <div class="col-lg-2">
        {!! Form::label('name', 'References') !!}
        {!! Form::text('name', null, ['placeholder' => 'Contract Name','class' => 'form-control m-input','required' => 'required']) !!}
    </div>
    <div class="col-lg-2">
        {!! Form::label('direction', 'Type') !!}
        {!! Form::select('direction',$direction,@$contracts->direction_id, ['class' => 'form-control m-select2-general','required' => 'required']) !!}
    </div>
    <div class="col-lg-2">
        <div class="row">
            {!! Form::label('status', 'Status') !!}<br>
            {{ Form::select('status',['1' => 'Publish','2' => 'Draft'],null,['class'=>'custom-select form-control','id' => 'm_select2_2_modal']) }}
        </div>
    </div>
    <div class="col-lg-3">
        {!! Form::label('carrier', 'Carriers') !!}
        {!! Form::select('carrierAr[]',$carrier,@$contracts->carriers, ['class' => 'form-control m-select2-general','required' => 'required','multiple'=>'multiple']) !!}
    </div>
    <div class="col-lg-3">
        {!! Form::label('validation_expire', 'Validation') !!}
        {!! Form::text('validation_expire', $validation_expire, ['placeholder' => 'Contract Validity','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}
    </div>

    <!--
<div class="col-lg-1">

{!! Form::label('free_days', 'Free Days') !!}<br>
{{ Form::number('free_days',null,['placeholder'=> '0','class'=>'custom-select form-control','id' => 'free_days','min' =>'0']) }}

</div> -->


</div>

<br>
