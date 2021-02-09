<div class="form-group m-form__group">
    {!! Form::text('name', null, ['id'=>'name', 'placeholder' => 'Please enter your firts name','class' => 'form-control m-input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::text('lastname', null, ['placeholder' => 'Please enter your last name','class' => 'form-control m-input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    {!! Form::text('email', null, ['placeholder' => 'Please enter your  email','class' => 'form-control m-input','required' => 'required']) !!}
</div>
<div class="form-group m-form__group">
    <input type="password" name="password" class="form-control m-input" placeholder="Please enter your password">
</div>
<div class="form-group m-form__group">
    <input type="password" name="password_confirmation" class="form-control m-input" placeholder="Please confirm your password">
</div>

@if($type == 'add' )
    @if( Auth::user()->type == 'admin')
        <div class="form-group m-form__group">
            <select class="form-control" name="type">
                <option value="">Choose a type</option>
                <option value="admin">Admin</option>
                <option value="company">Company</option>
                <option value="subuser">Subuser</option>
                <option value="data_entry">Data entry</option>
            </select>
        </div>
        <div class="form-group m-form__group">
            <select class="form-control" name="delegation_id">
                <option value="">Choose a delegation</option>
                @foreach($delegation as $data)
                    <option value="{{$data['id']}}">{{$data['name']}}</option> 
                @endforeach    
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
        <div class="form-group m-form__group">
            <select class="form-control" name="delegation_id">
                <option value="">Choose a delegation</option>
                @foreach($delegation as $data)
                    <option value="{{$data['id']}}">{{$data['name']}}</option> 
                @endforeach    
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
                <option value="data_entry" {{$user->type=='data_entry' ? 'selected':''}}>Data entry</option>
            </select>
        </div>
        <div class="form-group m-form__group">
            <select class="form-control" name="delegation_id">
                @if($userd!= null)
                    <option value="{{$userd->id }}">{{$userd->name}}</option>
                @else
                    <option value="">Choose a delegation</option>
                @endif
                @foreach($delegation as $data)
                    <option value="{{$data['id']}}">{{$data['name']}}</option> 
                @endforeach    
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
        
        <div class="form-group m-form__group">
            <select class="form-control" name="delegation_id">
                @if($userd!=null)
                    <option value="{{$userd->id}}">{{$userd->name}}</option>
                @else
                    <option value="">Choose a delegation</option>
                @endif
                @foreach($delegation as $data)
                    <option value="{{$data['id']}}">{{$data['name']}}</option> 
                @endforeach    
            </select>
        </div>
    @endif
@endif




