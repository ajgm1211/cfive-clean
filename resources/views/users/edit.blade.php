<!-- Condiciones para mostrar o no los bloques que se necesitan -->
@if($user->type == 'admin')

@php
$load = '1';
@endphp
@elseif($user->type == 'company')
@php
$load = '2';
@endphp
@else
@php
$load = '3';
@endphp
@endif


<!-- En caso de ser Subusuario precargamos el combo de editar con el valor correspondiente -->
@if(isset($datosSubuser))
@php
$valorSelect = $datosSubuser->id;
@endphp
@else
@php
$valorSelect = '';
@endphp
@endif

<!--begin::Form-->
{!! Form::model($user, ['route' => ['users.update', $user], 'method' => 'PUT']) !!}

<div class="m-form__section m-form__section--first">
    <div class="form-group m-form__group">
        @include('users.partials.form_users', array('type'=>'edit'))
    </div>
</div>

<div class="m-form__actions m-form__actions">
    {!! Form::submit('Save', ['class'=> 'btn btn-primary']) !!}
</div>
<br>

{!! Form::close() !!}
<!--end::Form-->

<script src="/js/users.js"></script>
<script>change({!! $load !!})</script>



