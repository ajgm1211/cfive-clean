@extends('layouts.app')
@section('title', 'Show term & condition')
@section('content')
<div class="m-portlet">
    <!--begin::Form-->
    {!! Form::model($term, ['route' => ['terms.update', $term], 'method' => 'PUT']) !!}
    <div class="m-portlet__body">
        <div class="m-form__section m-form__section--first">
            <div class="form-group m-form__group">
                <div class="form-group m-form__group">
                    {!! Form::label('Name', 'Name') !!}
                    {!! Form::text('name', null, ['placeholder' => 'Please enter the term name','class' => 'form-control m-input','disabled' => 'true']) !!}

                </div>
                   <div class="form-group m-form__group">
                    {!! Form::label('Type', 'Type') !!}
                    {!! Form::text('type', null, ['placeholder' => 'Please enter the term name','class' => 'form-control m-input','disabled' => 'true']) !!}

                </div>

                
                <div class="form-group m-form__group">
                    {!! Form::label('Language', 'Language') !!}
                    {!! Form::select('language',$languages,$term['language_id'], 
                    ['class' => 'm-select2-general form-control','disabled' => 'true']) !!}
                </div>

                <div class="form-group m-form__group">
                    {!! Form::label('Import', 'Import terms') !!}
                    <div class="jumbotron">{!! $term->import!!}</div>
                </div>

                <div class="form-group m-form__group">
                    {!! Form::label('Export', 'Export terms') !!}
                    <div class="jumbotron">{!! $term->export!!}</div>
                </div>
            </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <br>
            <div class="m-form__actions m-form__actions">
                <a class="btn btn-danger" href="{{url()->previous()}}">
                    Go back
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