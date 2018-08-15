<div class="form-group m-form__group">
    {!! Form::text('name', null, ['id'=>'name', 'placeholder' => 'Please enter your firts name','class' => 'form-control m-input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::text('lastname', null, ['placeholder' => 'Please enter your last name','class' => 'form-control m-input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::text('email', null, ['placeholder' => 'Please enter your  email','class' => 'form-control m-input','required' => 'required']) !!}
</div>
@if($type == 'add' )
    <div class="form-group m-form__group">
        <input type="password" name="password" class="form-control m-input" placeholder="Please enter your password" required>
    </div>
@endif
@if($type == 'add' )
    @if( Auth::user()->type == 'admin')
        <div class="form-group m-form__group">
            <select class="form-control" name="type">
                <option value="">Choose a type</option>
                <option value="admin">Admin</option>
                <option value="company">Company</option>
                <option value="subuser">Subuser</option>
            </select>
        </div>
    @else
        <div class="form-group m-form__group">
            <select class="form-control" name="type">
                <option value="">Choose a type</option>
                <option value="company">Company</option>
                <option value="subuser">Subuser</option>
            </select>
        </div>
    @endif
@else
    @if( Auth::user()->type == 'admin')
        <div class="form-group m-form__group">
            <select class="form-control" name="type">
                <option value="">Choose a type</option>
                <option value="admin" {{$user->type=='admin' ? 'selected':''}}>Admin</option>
                <option value="company" {{$user->type=='company' ? 'selected':''}}>Company</option>
                <option value="subuser" {{$user->type=='subuser' ? 'selected':''}}>Subuser</option>
            </select>
        </div>
    @else
        <div class="form-group m-form__group">
            <select class="form-control" name="type">
                <option value="">Choose a type</option>
                <option value="company" {{$user->type=='company' ? 'selected':''}}>Company</option>
                <option value="subuser" {{$user->type=='subuser' ? 'selected':''}}>Subuser</option>
            </select>
        </div>
    @endif
@endif




