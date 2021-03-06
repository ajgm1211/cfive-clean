@extends('layouts.app')
@section('title', 'Add Remark')
@section('content')
<div class="m-portlet">


    <!--begin::Form-->
    {!! Form::open(['route' => 'remarks.store']) !!}
    <div class="m-portlet__body">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissable alert-dismissible">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="m-form__section m-form__section--first">
            <div class="form-group m-form__group">
                @include('remarks.partials.form_terms')
            </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <br>
            <div class="m-form__actions m-form__actions">
                {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
                <a class="btn btn-danger" href="/remarks/list">
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
<script src="/js/remarks.js" type="text/javascript"></script>
<script>
    $('.m-select2-general').select2({
       placeholder: "Select an option"
   });
</script>
@stop