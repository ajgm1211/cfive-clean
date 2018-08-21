<!--begin::Form-->
{!! Form::open(['route' => 'surcharges.store']) !!}

<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        @include('surcharges.partials.form_surcharges')
        <div class="form-group m-form__group">
            {!! Form::label('sale_term_id', 'Sale Terms') !!}<br> 
            {{ Form::select('sale_term_id',$sale_terms,null,['class'=>'custom-select form-control','id' => 'sale_term_id','placeholder'=>'Select an option']) }}

        </div>
    </div>
</div>
<div class="m-form__actions m-form__actions">
    {!! Form::submit('Save', ['class'=> 'btn btn-success']) !!}
</div>
<br>
{!! Form::close() !!}
<!--end::Form-->

<script src="/js/users.js"></script>
<script type="text/javascript">
    $('#sale_term_id').select2({
        placeholder: "Select an option"
    });
</script>