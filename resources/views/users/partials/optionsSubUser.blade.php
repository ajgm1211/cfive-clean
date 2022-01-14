@if($type == 'add' )
<div class="form-group m-form__group" id="optionsSubUser" style="display: none">
    <select class="form-control" name="options">
        <option value="operaciones" selected>Operaciones</option>
        <option value="comercial">Comercial</option>
    </select>
</div>
@endif

@if($type == 'edit' )
<div class="form-group m-form__group @if($user->company_user_id == '148' && $user->type !== 'subuser' ) hidden @endif" id="optionsSubUser">
    <select class="form-control" name="options">
        <option value="operaciones" {{$user->options=='operaciones' ? 'selected':''}}>Operaciones</option>
        <option value="comercial" {{$user->options=='comercial' ? 'selected':''}}>Comercial</option>
    </select>
</div>
@endif