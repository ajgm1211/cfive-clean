<?php $__env->startSection('title', 'Welcome to Cargofive'); ?>
<?php $__env->startSection('content'); ?>
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
                                    <?php if(session('status')): ?>
                                    <div class="alert alert-success">
                                        <?php echo e(session('status')); ?>

                                    </div>
                                    <?php endif; ?>
                                    <?php if(session('warning')): ?>
                                    <div class="alert alert-warning">
                                        <?php echo e(session('warning')); ?>

                                    </div>
                                    <?php endif; ?>
                                    <form  class="m-login__form m-form" role="form" action="<?php echo e(route('login')); ?> " method="post" class="">
                                        <?php echo e(csrf_field()); ?>

                                        <div class="form-group m-form__group">
                                            <input class="form-control m-input" type="text" placeholder="Email" name="email" autocomplete="off">
                                            <?php if($errors->has('email')): ?>
                                            <span class="help-block">
                                                <strong> <?php echo e($errors->first('email')); ?> </strong>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group m-form__group">
                                            <input class="form-control m-input m-login__form-input--last" type="password" placeholder="Password" name="password">
                                            <?php if($errors->has('password')): ?>
                                            <span class="help-block">
                                                <strong> <?php echo e($errors->first('password')); ?> </strong>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="m-login__form-action">
                                            <button type="submit" class="btn btn-primary btn-focus m-btn m-btn--pill m-btn--custom m-btn--air">
                                                <?php echo e(__('Login')); ?>

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
                                    <form method="POST" class="m-login__form m-form" action="<?php echo e(route('register')); ?>">
                                        <?php echo csrf_field(); ?>
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
                                            <?php echo e(__('Forgot Your Password?')); ?>

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
                                <a href="javascript:;" id="m_login_signup" class="m-link m-link--focus m-login__account-link">
                                    Sign Up
                                </a>

                                <div class="m-login__head">
                                    <span class="m-login__account-msg">
                                        <?php echo e(__('Forgot Your Password?')); ?>

                                    </span>

                                    <a class="m-link m-link--focus m-login__account-link" href=" <?php echo e(route('password.request')); ?> ">Forgot</a>
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


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.login', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>