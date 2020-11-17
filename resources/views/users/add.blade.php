<!-- En caso de ser Subusuario precargamos el combo de editar con el valor correspondiente -->
@if(isset($datosSubuser))
{{ $valorSelect = $datosSubuser->id }}
@else
{{ $valorSelect = '' }}
@endif
<!--begin::Form-->
{!! Form::open(['route' => 'users.store' ]) !!}

<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        @include('users.partials.form_users', array('type'=>'add'))

    </div>
</div>
<div class="m-portlet__foot m-portlet__foot--fit">
    <br>
    <div class="m-form__actions m-form__actions">
        {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
    </div>
    <br>
</div>
{!! Form::close() !!}
<!--end::Form-->

<script src="/js/users.js"></script>
