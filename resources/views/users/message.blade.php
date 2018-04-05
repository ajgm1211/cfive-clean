


    <!--begin::Form-->
    {!! Form::open(['route' => ['delete-user', $userid],'method' => 'PUT']) !!}
   
        <div class="m-form__section m-form__section--first">
            <div class="form-group m-form__group">
               are you sure you want to delete the user?

            </div>
        </div>
        <div class="m-portlet__foot m-portlet__foot--fit">
            <div class="m-form__actions m-form__actions">
                {!! Form::submit('Yes', ['class'=> 'btn btn-primary  btn-sm']) !!}
                <a class="btn btn-success btn-sm" href="{{url()->previous()}}">
                    No
                </a>
            </div>
        </div>
  
    {!! Form::close() !!}
    <!--end::Form-->


