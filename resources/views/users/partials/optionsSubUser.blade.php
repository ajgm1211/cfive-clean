@if($type == 'add' )
<div class="form-group m-form__group" id="optionsSubUser" style="display: none">
    <select class="form-control" name="subtype">
        <option value="operaciones" selected>Operaciones</option>
        <option value="comercial">Comercial</option>
    </select>
</div>
@endif

@if($type == 'edit' )
    @if(Auth::user()->company_user_id == '148')
    <div class="form-group m-form__group @if($user->type !== 'subuser' ) hidden @endif" id="optionsSubUser">
        <select class="form-control" name="subtype">
            <option value="operaciones" {{$user->options['subtype']=='operaciones' ? 'selected':''}}>Operaciones</option>
            <option value="comercial" {{$user->options['subtype']=='comercial' ? 'selected':''}}>Comercial</option>
        </select>
    </div>
    @endif
@endif