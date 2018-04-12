<div>
    {!! Form::label('name', 'First Name') !!}
    {!! Form::text('name', null, ['placeholder' => 'Please enter your firts name','class' => 'form-control m-input','required' => 'required']) !!}

</div>

<div class="form-group m-form__group">
    {!! Form::label('lastname', 'Last Name') !!}
    {!! Form::text('lastname', null, ['placeholder' => 'Please enter your last name','class' => 'form-control m-input','required' => 'required']) !!}

</div>
<div class="form-group m-form__group">
    {!! Form::label('email', 'Email') !!}
    {!! Form::text('email', null, ['placeholder' => 'Please enter your  email','class' => 'form-control m-input','required' => 'required']) !!}

</div>


<div class="form-group m-form__group">
    {!! Form::label('password', 'Password') !!}<br>
    {!! Form::password('password', null, ['placeholder' => 'Please enter your password','class' => 'form-control m-input','required' => 'required']) !!}

</div>


@if( Auth::user()->type == 'admin'   )
<div class="form-group m-form__group">
    {!! Form::label('type', 'Type') !!}<br>

    Administrator: {!! Form::radio('type', 'admin',true,['onclick' => 'change(1)']) !!} &nbsp;
    Company: {!! Form::radio('type', 'company',null,['onclick' => 'change(2)']) !!} &nbsp;
    Sub-User: {!! Form::radio('type', 'subuser',null,['onclick' => 'change(3)']) !!}<br>  
    <span class="m-form__help">
        Please select this type
    </span>
</div>
@endif


@if( Auth::user()->type == 'company'   )
<div class="form-group m-form__group">
    {!! Form::label('type', 'Type') !!}<br>

    Sub-User: {!! Form::radio('type', 'subuser',true) !!}<br>  

</div>
@endif


<div id="divCompany" class="form-group m-form__group"  style="display:none">
    {!! Form::label('name_company', 'Name Company') !!}<br>
    {!! Form::text('name_company', null, ['placeholder' => 'Please enter name of company','class' => 'form-control m-input','id'=>'txtCompany']) !!}

</div>

@if( Auth::user()->type == 'admin'   )

<div id="divSubuser" style="display:none">


    <div    class="form-group m-form__group"  >
        {!! Form::label('Company', 'Company') !!}<br>
        {{ Form::select('id_company', $companyall,$valorSelect) }}

    </div>



    <div    class="form-group m-form__group" >
        {!! Form::label('position', 'Position') !!}<br>
        {!! Form::text('position', null, ['placeholder' => 'Please enter position','class' => 'form-control m-input','id'=>'txtSubuser']) !!}

    </div>


</div>
@endif


@if( Auth::user()->type == 'company')
<div id="divSubuser" style="display:block">


    <div    class="form-group m-form__group"  >
        {!! Form::label('Company', 'Company') !!}<br>
        {{ Form::select('company', $companyall, Auth::user()->id ,['disabled' => 'true','id'=>'txtSubuser']) }}
        {{ Form::hidden('id_company', Auth::user()->id) }}

    </div>



    <div    class="form-group m-form__group" >
        {!! Form::label('position', 'Position') !!}<br>
        {!! Form::text('position', null, ['placeholder' => 'Please enter position','class' => 'form-control m-input','id'=>'txtSubuser']) !!}

    </div>


</div>
@endif




