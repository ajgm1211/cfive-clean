<!--begin::Form-->
{!! Form::open(['route' => ['delete-emails-template', $id],'method' => 'PUT']) !!}

<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        <h5>Are you sure?</h5>
    </div>
</div>
<br>

<div class="m-form__actions m-form__actions">
    {!! Form::submit('Yes', ['class'=> 'btn btn-primary']) !!}
    <a class="btn btn-danger" href="{{url()->previous()}}">
        No
    </a>
</div>
<br>

{!! Form::close() !!}
<!--end::Form-->