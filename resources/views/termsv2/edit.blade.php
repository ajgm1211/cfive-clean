@extends('layouts.app')
@section('title', 'Edit term & condition')
@section('content')
<div class="dropdown show" align="right" style="margin:20px;">
    <a class="dropdown-toggle" style="font-size:16px" href="#" role="button" id="helpOptions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        See how it works
    </a>

    <div 
        class="dropdown-menu" 
        aria-labelledby="helpOptions"
    >
        <a class="dropdown-item" target="_blank" href="https://support.cargofive.com/how-to-create-terms-and-conditions/"> 
            How to create terms and conditions
        </a>
    </div>
</div>
<div class="m-portlet">
    <!--begin::Form-->
    {!! Form::model($term, ['route' => ['termsv2.update', $term], 'method' => 'PUT']) !!}
    <div class="m-portlet__body">
        <div class="m-form__section m-form__section--first">
            <div class="form-group m-form__group">
                @include('termsv2.partials.form_terms')
            </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <br>
            <div class="m-form__actions m-form__actions">
                {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
                <a class="btn btn-danger" href="{{url()->route('termsv2.list')}}">
                    Cancel
                </a>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    <!--end::Form-->
</div>

@endsection

@section('js')
@parent
<script>
   $('.m-select2-general').select2({
       placeholder: "Select an option"
   });
</script>
@stop