@extends('layouts.app')
@section('title', 'Send email')
@section('content')
<div class="m-portlet">
    <!--begin::Form-->
    {!! Form::model($mail, ['route' => ['mail.update', $mail], 'method' => 'PUT']) !!}
    <div class="m-portlet__body">
        <div class="m-form__section m-form__section--first">
            <div class="form-group m-form__group">
                @include('mail.partials.form_mail_send')
            </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <br>
            <div class="m-form__actions m-form__actions">
                {!! Form::submit('Send', ['class'=> 'btn btn-primary']) !!}
                <a class="btn btn-success" href="{{url()->previous()}}">
                    Cancel
                </a>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    <!--end::Form-->
</div>

@endsection