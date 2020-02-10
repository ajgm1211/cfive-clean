@extends('layouts.login')
@section('title', 'Welcome to Cargofive')
@section('content')
<!-- begin:: Page -->
<div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-grid--tablet-and-mobile m-grid--hor-tablet-and-mobile m-login m-login--1 m-login--signin" id="m_login">
  <div class="m-grid__item m-grid__item--order-tablet-and-mobile-2 m-login__aside">
    <div class="m-stack m-stack--hor m-stack--desktop">
      <div class="m-stack__item m-stack__item--fluid">
        <div class="m-login__wrapper">
          <div class="m-login__signin">
            <div class="m-login__head">
              <h3 class="m-login__title">
                Welcome to Cargofive
              </h3>
            </div>
            @if (session('status'))
            <div class="alert alert-success">
              {{ session('status') }}
            </div>
            @endif
            @if (session('warning'))
            <div class="alert alert-warning">
              {{ session('warning') }}
            </div>
            @endif
            <form  class="m-login__form m-form" role="form" action="{{ route('login') }} " method="post" class="">
              {{ csrf_field()  }}
              <div class="form-group m-form__group">
                <input class="form-control m-input" type="text" placeholder="Email" name="email" autocomplete="off">
                @if ($errors->has('email'))
                <span class="help-block">
                  <strong> {{ $errors->first('email') }} </strong>
                </span>
                @endif
              </div>
              <div class="form-group m-form__group">
                <input class="form-control m-input m-login__form-input--last" type="password" placeholder="Password" name="password">
                @if ($errors->has('password'))
                <span class="help-block">
                  <strong> {{ $errors->first('password') }} </strong>
                </span>
                @endif
              </div>
              <div class="m-login__form-action">
                <button type="submit" class="btn btn-primary btn-focus m-btn m-btn--pill m-btn--custom m-btn--air">
                  {{ __('Login')  }}
                </button>
              </div>
            </form>
          </div>
          <div class="m-login__signup">
            <div class="m-login__head">
              <h3 class="m-login__title">
                Sign Up in Cargofive
              </h3>
            </div>
            <form method="POST" class="m-login__form m-form" action="{{ route('register') }}">
              @csrf
              <div class="form-group m-form__group">
                <input class="form-control m-input" type="text" placeholder="First Name" name="name" required>
              </div>
              <div class="form-group m-form__group">
                <input class="form-control m-input" type="text" placeholder="Last Name" name="lastname" autocomplete="off" required>
              </div>
              <div class="form-group m-form__group">
                <input class="form-control m-input" type="text" placeholder="Email" name="email" autocomplete="off" required>
              </div>
              <div class="form-group m-form__group">
                <input class="form-control m-input" type="text" placeholder="Phone" name="phone" autocomplete="off">
              </div>
              <div class="form-group m-form__group">
                <input class="form-control m-input" type="password" placeholder="Password" name="password" required>
              </div>
              <div class="form-group m-form__group">
                <input class="form-control m-input m-login__form-input--last" type="password" placeholder="Confirm Password" name="password_confirmation" required>
              </div>
              <div class="row form-group m-form__group m-login__form-sub">
                <div class="col m--align-left">
                  <label class="m-checkbox m-checkbox--focus">
                    <input type="checkbox" name="agree" required>
                    I acept the
                    <a href="https://cargorive.com/terms-and-conditions" class="m-link m-link--focus" >
                      Terms and Conditions
                    </a>
                    .
                    <span></span>
                  </label>
                  <span class="m-form__help"></span>
                </div>
              </div>

              <div class="row form-group m-form__group m-login__form-sub">
                <div class="col m--align-left">

                  <span class="m-form__help"></span>
                </div>
              </div>

              {!! Recaptcha::render() !!}

              <div class="m-login__form-action">
                <button type="submit" id="" class="btn btn-primary btn-focus m-btn m-btn--pill m-btn--custom m-btn--air">
                  Sign Up
                </button>
                <button id="m_login_signup_cancel" class="btn btn-danger m-btn m-btn--pill m-btn--custom">
                  Cancel
                </button>
              </div>
            </form>
          </div>
          <div class="m-login__forget-password">
            <div class="m-login__head">
              <h3 class="m-login__title">
                {{   __('Forgot Your Password?')  }}
              </h3>
              <a class="btn btn-link" href=" route('password.request') ">Forgot</a>
            </div>
          </div>
        </div>
      </div>
      <div class="m-stack__item m-stack__item--center">
        <div class="m-login__account">
          <span class="m-login__account-msg">
            Don't have an account yet ?
          </span>
          &nbsp;&nbsp;
          <a href="#" id="m_login_signup" class="m-link m-link--focus m-login__account-link">
            Sign Up
          </a>

          <div class="m-login__head">
            <span class="m-login__account-msg">
              {{   __('Forgot Your Password?')  }}
            </span>

            <a class="m-link m-link--focus m-login__account-link" href=" {{ route('password.request')}} ">Forgot</a>
          </div>
        </div>

      </div>
    </div>
  </div>
  <div class="m-grid__item m-grid__item--fluid m-grid m-grid--center m-grid--hor m-grid__item--order-tablet-and-mobile-1	m-login__content" style="background-image: url(/images/login.jpg)">
  </div>
</div>

<script src="/assets/vendors/base/vendors.bundle.js" type="text/javascript"></script>
<script src="/assets/demo/default/base/scripts.bundle.js" type="text/javascript"></script>
<script src="/assets/snippets/pages/user/login.js" type="text/javascript"></script>


<script type="text/javascript">
  $crisp = [];
  CRISP_WEBSITE_ID = '011f006f-3864-44b5-9443-d700e87df5f7';
  (function(){d=document;s=d.createElement('script');s.src='//client.crisp.chat/l.js';s.async=1;d.getElementsByTagName('head')[0].appendChild(s);})();
</script>
<script type="text/javascript">
  $crisp.push(["do", "session:reset"]);
</script>


@endsection
