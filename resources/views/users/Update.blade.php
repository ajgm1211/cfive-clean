@extends('layouts.app')
@section('title', 'My Profile')
@section('content')
<style>
.btn-action__user {
    font-size: 18px;
    position: relative;
    padding: 13px 30px;
    border-radius: 50px !important;
}
</style>

<div class="container m-content">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(Session::has('message.nivel'))
            <div class="col-md-16">               
                <div class="m-alert m-alert--icon m-alert--outline alert alert-{{ session('message.nivel') }} alert-dismissible fade show" role="alert">
                    <div class="m-alert__icon">
                        <i class="la la-warning"></i>
                    </div>
                    <div class="m-alert__text">
                        <strong>
                            {{ session('message.title') }}
                        </strong>
                        {{ session('message.content') }}
                    </div>
                    <div class="m-alert__close">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
            @endif
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">                           
                                My Profile
                               <i class="m-menu__link-icon la la-user"></i>                     
                            </h3>
                        </div>
                    </div>
                </div>
                {!! Form::open(['route' => ['user.update',$user->id],'method' => 'POST']) !!}
                    <div class="container m-portlet__body">                                          
                        <div class="row">
                            <div class="col-lg-12">                           
                                <div class="form-group m-form__group">
                                    <label for="name">First Name<span style="color:red">*</span></label>
                                    <input id="name" type="text" name="name" class="form-control m-input" value="{{ $user->name }}" placeholder="Please enter your Firs Name" required>
                                </div>

                                <div class="form-group m-form__group">
                                    <label for="lastname">Last Name<span style="color:red">*</span></label>
                                    <input id="lastname" type="text" name="lastname" class="form-control m-input" value="{{ $user->lastname }}" placeholder="Please enter your Lastname">
                                </div>

                                <div class="form-group m-form__group">
                                    <label for="email">Email<span style="color:red">*</span></label>
                                    <input id="email" type="email" name="email" class="form-control m-input" value="{{ $user->email }}" placeholder="Please enter your Email">
                                </div>

                                <div class="form-group m-form__group">
                                    <label for="password">Password</label>
                                    <input id="password" type="password" name="password" class="form-control m-input" value="" placeholder="Please enter your password">
                                </div>

                                <div class="form-group m-form__group">
                                    <label for="password">Confirm your password</label>
                                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control m-input" value="" placeholder="Please confirm your password">
                                </div>                                                               
                            </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-lg-12">
                                    <center>                                    
                                            <button type="submit"
                                                class="btn m-btn--pill  btn-action__user  btn-info quote_search"
                                                <i class="flaticon-search-magnifier-interface-symbol"></i> &nbsp;Update
                                            </button>                               
                                        <a href="{{route('quotes-v2.search')}}">
                                            <button type="button" class="btn btn-danger btn-action__user">
                                                Cancel
                                            </button>
                                        </a>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div> 
                {!! Form::close() !!}                                                                      
            </div>
        </div>
    </div>
</div>
@endsection