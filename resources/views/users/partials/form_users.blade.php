<div>
    {!! Form::label('name', 'First Name') !!}
    {!! Form::text('name', null, ['class' => 'form-control m-input','required' => 'required']) !!}
    <span class="m-form__help">
        Please enter your firts name
    </span>
</div>

<div class="form-group m-form__group">
    {!! Form::label('lastname', 'Last Name') !!}
    {!! Form::text('lastname', null, ['class' => 'form-control m-input','required' => 'required']) !!}
    <span class="m-form__help">
        Please enter your last name
    </span>
</div>
<div class="form-group m-form__group">
    {!! Form::label('email', 'Email') !!}
    {!! Form::text('email', null, ['class' => 'form-control m-input','required' => 'required']) !!}

    <span class="m-form__help">
        We'll never share your email with anyone else
    </span>
</div>
<div class="form-group m-form__group">
    {!! Form::label('password', 'password') !!}
    {!! Form::text('password', null, ['class' => 'form-control m-input','required' => 'required']) !!}
    <span class="m-form__help">
        Please enter your password
    </span>
</div>
<div class="form-group m-form__group">
    {!! Form::label('type', 'Type') !!}<br>

    Administrator: {!! Form::radio('type', 'admin',true,['onclick' => 'change(1)']) !!} &nbsp;
    Company: {!! Form::radio('type', 'company',null,['onclick' => 'change(2)']) !!} &nbsp;
    Sub-User: {!! Form::radio('type', 'subuser',null,['onclick' => 'change(3)']) !!}<br>  


    <span class="m-form__help">
        Please select this type
    </span>
</div>


<div id="divCompany" class="form-group m-form__group"  style="display:none">
    {!! Form::label('name_company', 'Name Company') !!}<br>
    {!! Form::text('name_company', null, ['class' => 'form-control m-input','id'=>'txtCompany']) !!}
    <span class="m-form__help">
        Please enter name of company
    </span>
</div>



<div id="divSubuser" style="display:none">

    @if( Auth::user()->type == 'admin'   )
    <div    class="form-group m-form__group"  >
        {!! Form::label('Company', 'Company') !!}<br>
        {{ Form::select('id_company', $companyall,$valorSelect) }}
        <span class="m-form__help">
            Please enter position
        </span>
    </div>
    @endif


    <div    class="form-group m-form__group" >
        {!! Form::label('position', 'Position') !!}<br>
        {!! Form::text('position', null, ['class' => 'form-control m-input','id'=>'txtSubuser']) !!}
        <span class="m-form__help">
            Please enter position
        </span>
    </div>


</div>





