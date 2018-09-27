
<!--begin::Form-->
{!! Form::model($saleterms, ['route' => ['saleterms.update', $saleterms], 'method' => 'PUT']) !!}

<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">                
        @include('saleTerms.partials.add_sale_term')
    </div>
</div>

<div class="m-form__actions m-form__actions">
    {!! Form::submit('Update', ['class'=> 'btn btn-primary']) !!}
</div>
<br>

{!! Form::close() !!}
<!--end::Form-->