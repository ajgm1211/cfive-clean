<div>
    {!! Form::label('name', 'Contract Name') !!}
    {!! Form::text('name', null, ['placeholder' => 'Please enter the contract name','class' => 'form-control m-input','required' => 'required']) !!}

</div>

<div class="form-group m-form__group">
    {!! Form::label('number', 'Number') !!}
    {!! Form::text('number', null, ['placeholder' => 'Please enter contract number','class' => 'form-control m-input','required' => 'required']) !!}

</div>

<div class="form-group m-form__group">
    {!! Form::label('validation_expire', 'Validation') !!}
    {!! Form::text('validation_expire', null, ['placeholder' => 'Please enter validation  date','class' => 'form-control m-input','readonly'=>true,'id'=>'m_daterangepicker_1','required' => 'required']) !!}

</div>


<div class="form-group m-form__group">
    <div class="row">
        <div class="col-6 col-md-5">
            {!! Form::label('carrier', 'Carrier') !!}
            {{ Form::select('carrier_id', $carrier) }}
        </div>
        <div class="col-6 col-md-5">

            <div class="form-group m-form__group">
                {!! Form::label('status', 'Status') !!}<br>
                {{ Form::select('status',['1' => 'Publish','2' => 'Draft'] ) }}
            </div>
        </div>
    </div>
</div>
<div class="form-group m-form__group">
    {!! Form::label('origin', 'Origin Country') !!}<br>
    <div class="col-lg-4 col-md-9 col-sm-12">
        {{ Form::select('origin_country', $country,null,['class'=>'form-control m-select2-general','id' => '','data-placeholder' => 'Please select origin country' ]) }}
    </div>

</div>
<div class="form-group m-form__group">
    {!! Form::label('destiny', 'Destiny Country') !!}<br>
    {{ Form::select('destiny_country', $country) }}

</div>




