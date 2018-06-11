
<div class="form-group m-form__group">
    {!! Form::text('name', null, ['placeholder' => 'Please enter your firts name','class' => 'form-control m-input','required' => 'required']) !!}

</div>

<div class="form-group m-form__group">
    {!! Form::text('lastname', null, ['placeholder' => 'Please enter your last name','class' => 'form-control m-input','required' => 'required']) !!}

</div>

<div class="form-group m-form__group">
    {!! Form::text('email', null, ['placeholder' => 'Please enter your  email','class' => 'form-control m-input','required' => 'required']) !!}

</div>
@if($type == 'add' )
    <div class="form-group m-form__group">
        <input type="password" class="form-control m-input" placeholder="Please enter your password" required>
    </div>
@endif
@if( Auth::user()->type == 'admin'   )
    <div class="form-group m-form__group">
        {!! Form::label('type', 'Type') !!}
        <hr>
    </div>
    {!! Form::label('admin', 'Admin') !!}
    <div class="form-group m-form__group">

        {!! Form::radio('type', 'admin',true,['onclick' => 'change(1)']) !!} &nbsp;
    </div>
    {!! Form::label('company', 'Company') !!}
    <div class="form-group m-form__group">
        {!! Form::radio('type', 'company',null,['onclick' => 'change(2)']) !!}
    </div>
    {!! Form::label('subuser', 'Sub-User') !!}
    <div class="form-group m-form__group">
        {!! Form::radio('type', 'subuser',null,['onclick' => 'change(3)']) !!}
    </div>
@endif

@if( Auth::user()->type == 'company'   )
    {!! Form::radio('type', 'subuser',true,['class'=>'hide']) !!}<br>
@endif

@if( Auth::user()->type == 'admin'   )
    <div id="divSubuser" style="display:none">
        <div class="form-group m-form__group"  >
            {!! Form::label('Company', 'Company') !!}<br>
            {{ Form::select('id_company', $companyall,$valorSelect, ['class'=>'form-control']) }}
        </div>

        <div class="form-group m-form__group" >
            {!! Form::label('position', 'Position') !!}<br>
            {!! Form::text('position', null, ['placeholder' => 'Please enter position','class' => 'form-control m-input','id'=>'txtSubuser']) !!}
        </div>
    </div>
@endif




