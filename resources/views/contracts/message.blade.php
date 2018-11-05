<!--begin::Form-->
{!! Form::open(['route' => ['delete-rates', $rate_id],'method' => 'PUT']) !!}

<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        are you sure you want to delete this rate?

    </div>
</div>
<div class="m-portlet__foot m-portlet__foot--fit">
    <div class="m-form__actions m-form__actions">
        {!! Form::submit('Yes', ['class'=> 'btn btn-primary']) !!}
        <a class="btn btn-success" href="{{url()->previous()}}">
            No
        </a>
    </div>
</div>

{!! Form::close() !!}
<!--end::Form-->


