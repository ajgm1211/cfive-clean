@extends('layouts.app')
@section('title', 'Add new email template')
@section('content')
<div class="m-portlet">
    <!--begin::Form-->
    {!! Form::open(['route' => 'templates.store','novalidate' => 'novalidate']) !!}   <!--justo donde agregue esto'novalidate' => 'novalidate' -->
    
    <div class="m-portlet__body">
        <div class="m-form__section m-form__section--first">
            <div class="form-group m-form__group">
                @include('emails-template.partials.form_email_template')
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

