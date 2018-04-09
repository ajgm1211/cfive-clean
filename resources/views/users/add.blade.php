
<div class="m-portlet">

    <!--begin::Form-->
    {!! Form::open(['route' => 'users.store']) !!}
    <div class="m-portlet__body">
        <div class="m-form__section m-form__section--first">
            <div class="form-group m-form__group">
                @include('users.partials.form_users')


            </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <div class="m-form__actions m-form__actions">
                {!! Form::submit('Save', ['class'=> 'btn btn-primary  btn-sm']) !!}
                <a class="btn btn-success btn-sm" href="{{url()->previous()}}">
                    Cancel
                </a>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
    <!--end::Form-->
</div>

<script src="/js/users.js"></script>
