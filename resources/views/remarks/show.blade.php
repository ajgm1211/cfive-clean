@extends('layouts.app')
@section('title', 'Show remarks & condition')
@section('content')
<div class="m-portlet">
    <!--begin::Form-->
    {!! Form::model($remark, ['route' => ['remarks.update', $remark], 'method' => 'PUT']) !!}
    <div class="m-portlet__body">
        <div class="m-form__section m-form__section--first">
            <div class="form-group m-form__group">
                <div class="form-group m-form__group">
                    {!! Form::label('Name', 'Name') !!}
                    {!! Form::text('name', null, ['placeholder' => 'Please enter the term name','class' => 'form-control m-input','disabled' => 'true']) !!}

                </div>

                <div class="form-group m-form__group">
                    {!! Form::label('Countries', 'Countries') !!}
                    {!! Form::select('countries[]',$countries,@$selected_countries, 
                    ['class' => 'm-select2-general form-control', 'multiple' => 'multiple','disabled' => 'true']) !!}
                </div>
                
                <div class="form-group m-form__group">
                    {!! Form::label('Port', 'Ports') !!}
                    {!! Form::select('ports[]',$harbors,@$selected_harbors, 
                    ['class' => 'm-select2-general form-control', 'multiple' => 'multiple','disabled' => 'true']) !!}
                </div>
                
                <div class="form-group m-form__group">
                    {!! Form::label('Carrier', 'Carriers') !!}
                    {!! Form::select('carriers[]',$carriers,@$selected_carriers, 
                    ['class' => 'm-select2-general form-control', 'multiple' => 'multiple','disabled' => 'true']) !!}
                </div>
                
                <div class="form-group m-form__group">
                    {!! Form::label('Language', 'Language') !!}
                    {!! Form::select('language',$languages,$remark['language_id'], 
                    ['class' => 'm-select2-general form-control','disabled' => 'true']) !!}
                </div>

                <div class="form-group m-form__group">
                    {!! Form::label('Import', 'Import terms') !!}
                    <div class="jumbotron">{!! $remark->import!!}</div>
                </div>

                <div class="form-group m-form__group">
                    {!! Form::label('Export', 'Export terms') !!}
                    <div class="jumbotron">{!! $remark->export!!}</div>
                </div>
            </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <br>
            <div class="m-form__actions m-form__actions">
                <a class="btn btn-danger" href="/remarks/list">
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