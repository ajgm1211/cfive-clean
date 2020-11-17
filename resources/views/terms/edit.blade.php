@extends('layouts.app')
@section('title', 'Edit term & condition')
@section('content')
<div class="m-portlet">
    <!--begin::Form-->
    {!! Form::model($term, ['route' => ['terms.update', $term], 'method' => 'PUT']) !!}
    <div class="m-portlet__body">
        <div class="m-form__section m-form__section--first">
            <div class="form-group m-form__group">
                @include('terms.partials.form_terms')
            </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <br>
            <div class="m-form__actions m-form__actions">
                {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
                <a class="btn btn-danger" href="{{url()->previous()}}">
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