<!--begin::Form-->
{!! Form::open(['route' => 'saleterms.store']) !!}
<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        @include('saleTerms.partials.add_sale_term')
    </div>
</div>
<div class="m-form__actions m-form__actions">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
</div>
<br>
{!! Form::close() !!}
<!--end::Form-->